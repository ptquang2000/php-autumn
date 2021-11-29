<?php

namespace Core;

use Core\HttpSecurity;

class SecurityConfiguration
{
  public HttpSecurity $http_security;

  private $enable = false;
  private $userdetails_service;

  public function is_enable()
  {
    return $this->enable;
  }

  public function set_enable($enable)
  {
    $this->enable = $enable;
  }

  public function httpConfigure(HttpSecurity $http)
  {
    foreach (Router::$paths as $path => $prop)
      $http->authorizeRequest()->antMatchers($prop['method'], $path);
  }

  public function set_userdetails_service($userdetails_service)
  {
    $reflection = new \ReflectionClass($userdetails_service);
    foreach ($reflection->getProperties() as $prop) {
      if (
        !($attr = $prop->getAttributes())
        || $attr[0]->getName() != 'Core\Autowired'
      ) continue;

      $reflection_prop =  $reflection->getProperty($prop->getName());
      $reflection_prop->setAccessible(true);
      $class_name = $prop->getType()->getName();

      $reflection_prop->setValue($userdetails_service, ($o = autowired($class_name)));
    }
    $this->userdetails_service = $userdetails_service;
  }

  public function logout()
  {
    unset($_SESSION['USER']);
    $url = $GLOBALS['config']['security.logout_redirect'] ?? false;
    if (!$url) {
      if (isset($_SERVER['HTTP_REFERER']) && !$this->is_authorized_path($_SERVER['HTTP_REFERER']))
        $url = $_SERVER['HTTP_REFERER'];
      else $url = ($GLOBALS['config']['security.login'] ?? '/login') . '?error=logout';
    }

    header('Location: ' . $url);
    exit();
  }

  public function authenticate()
  {
    $username = htmlspecialchars($_REQUEST['username']);
    $password = htmlspecialchars($_REQUEST['password']);
    try {
      $user = $this->userdetails_service->load_user_by_username($username);
      if (!password_verify($password, $user->get_password()))
        throw new UserDetailsException('WrongPassword');
      $_SESSION['USER'] = $user;
      $url = $_SESSION['CURRENT_URL'] ?? $GLOBALS['config']['view.login_success'] ?? '/';
      header('Location: ' . $url);
    } catch (UserDetailsException $e) {
      unset($_SESSION['USER']);
      header('Location: ' . ($GLOBALS['config']['security.login'] ?? '/login') . '?error=' . $e->getMessage());
    }
    exit();
  }

  public function is_authorized_path($path)
  {
    return in_array(
      $path,
      array_map(function ($property) {
        return $property['path'];
      }, $this->http_security->antMatchers->property)
    );
  }

  public function get_path_authority($path)
  {
    $result = array_filter(
      $this->http_security->antMatchers->property,
      function ($property) use ($path) {
        return $property['path'] == $path;
      }
    );
    return $result[array_key_first($result)]['role'];
  }

  public function authorize($path)
  {
    $check_auth_path = $this->is_authorized_path($path);
    if (!$check_auth_path) {
      if (isset($_SESSION['USER']) && $path == $GLOBALS['config']['security.login'] ?? '/login') {
        $url = $_SERVER['HTTP_REFERER'] ?? $GLOBALS['config']['view.login_success'] ?? '/';
        $url = $url == ($GLOBALS['config']['security.login'] ?? '/login') ? $url : '/';
        header('Location: ' . $url);
        exit();
      }
      if (
        !isset($_SESSION['USER'])
        && $path == $GLOBALS['config']['security.login'] ?? '/login'
      )
        $_SESSION['CURRENT_URL'] = $_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'];
      return true;
    }
    if (isset($_SESSION['USER'])) {
      try {
        $user = $_SESSION['USER'];
        if ($check_auth_path && !in_array($user->get_authority(), $this->get_path_authority($path))) {
          throw new HttpException('403');
        }
        if (!$user->is_account_expired())
          throw new UserDetailsException('AccountExpired');
        if (!$user->is_account_non_locked())
          throw new UserDetailsException('AccountLocked');
        if (!$user->is_credentials_non_expired())
          throw new UserDetailsException('CredentialsExpired');
      } catch (UserDetailsException $e) {
        unset($_SESSION['USER']);
        $_SESSION['CURRENT_URL'] = Router::$url;
        header('Location: ' . ($GLOBALS['config']['security.login'] ?? '/login') . '?error=' . $e->getMessage());
        exit();
      }
      unset($_SESSION['CURRENT_URL']);
      return true;
    }
    $_SESSION['CURRENT_URL'] = Router::$url;
    header('Location: ' . $GLOBALS['config']['security.login'] ?? '/login');
    exit();
  }
}

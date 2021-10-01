<?php

namespace Core;

class SecurityConfiguration
{
  public static $paths = array();

  private $userdetails_service;

  public function set_userdetails_service($userdetails_service)
  {
		$reflection = new \ReflectionClass($userdetails_service);
		foreach($reflection->getProperties() as $prop) {
			if (!($attr=$prop->getAttributes()) 
			|| $attr[0]->getName()!='Core\Autowired') continue;            
			
			$reflection_prop =  $reflection->getProperty($prop->getName());
			$reflection_prop->setAccessible(true);
			$class_name = $prop->getType()->getName();
			
			$reflection_prop->setValue($userdetails_service, ($o=autowired($class_name)));
		}
    $this->userdetails_service = $userdetails_service;
  }

  public function authenticate()
  {
    $username = htmlspecialchars($_REQUEST['username']);
    $password = htmlspecialchars($_REQUEST['password']);
    try{
    $user = $this->userdetails_service->load_user_by_username($username);
    if (!password_verify($password, $user->get_password()))
      throw new UserDetailsException('WrongPassword');
    if (isset(SecurityConfiguration::$paths[$_SESSION['CURRENT_URL']]))
    {
      $roles = SecurityConfiguration::$paths[$_SESSION['CURRENT_URL']];
      if(!in_array($user->get_authority(), $roles))
        throw new UserDetailsException('DontHavePermission');
    }
    if (!$user->is_account_expired())
      throw new UserDetailsException('AccountExpired');
    if (!$user->is_account_non_locked())
      throw new UserDetailsException('AccountLocked');
    if (!$user->is_credentials_non_expired())
      throw new UserDetailsException('CredentialsExpired');

    unset($_SESSION['LOGIN-ERROR']);
    $_SESSION['USER'] = $user;
    header('Location: '.$_SESSION['CURRENT_URL']);
    }catch (UserDetailsException $e)
    {
      unset($_SESSION['USER']);
      $_SESSION['LOGIN-ERROR'] = $e->getMessage();
      header('Location: '.$GLOBALS['config']['security.login'] ?? '/login');
    }
  }

  public function authorize($path)
  {
    if (!array_key_exists($path, SecurityConfiguration::$paths))
      return true;
    $_SESSION['CURRENT_URL'] = Router::$path;
    if (isset($_SESSION['USER']))
      return true;
    header('Location: '.$GLOBALS['config']['security.login'] ?? '/login');
    return false;
  }
}

?>
<?php

namespace App\PHP;
use Core\{UserDetails, Service};

#[Service]
class MyUserDetails implements UserDetails
{
  private User $user;

  public function __construct($user)
  {
    $this->user = $user;
  }
  public function get_uid()
  {
    return $this->user->get_uid();
  }
  public function get_authority()
  {
    return explode('_', $this->user->get_role())[1];
  }
  public function get_password()
  {
    return $this->user->get_password();
  }
  public function get_username()
  {
    return $this->user->get_username();
  }
  public function is_account_expired()
  {
    return true;
  }
  public function is_account_non_locked()
  {
    return true;
  }
  public function is_credentials_non_expired()
  {
    return true;
  }
}

?>
<?php

namespace App\PHP;
use Core\{Service, Autowired, UserDetailsService, UserDetailsException};
use App\PHP\{UserRepository};

#[Service]
class MyUserDetailsService implements UserDetailsService
{
  #[Autowired]
  private UserRepository $user_repository;
  
  public function load_user_by_username($username)
  {
    $user = $this->user_repository->find_by_username($username);
    if ($user)
      return new MyUserDetails($user[0]);
    throw new UserDetailsException('UserNotFound');
  }
}

?>
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

  public function save_user(User $user)
  {
    $user->set_role('ROLE_MEMBER');
    $user->set_password(password_hash($user->get_password(), PASSWORD_DEFAULT));
    return $this->user_repository->save($user);
  }
  public function save_password(User $user)
  {
    return $this->user_repository->save($user);
  }
  public function delete_user($id)
  {
    return $this->user_repository->delete($id);
  }
}

?>
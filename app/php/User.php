<?php

namespace App\PHP;
use Core\{Table, ID, Column};

#[Table(name: 'user')]
class User 
{
  #[ID(name: 'uid')]
  private $uid;
  #[Column(name: 'username')]
  private $username;
  #[Column(name: 'password')]
  private $password;
  #[Column(name: 'role')]
  private $role;

  public function get_uid(){return $this->uid;}
  public function set_uid($uid){$this->uid = $uid;}
  public function get_username(){return $this->username;}
  public function set_username($username){$this->username = $username;}
  public function get_password(){return $this->password;}
  public function set_password($password){$this->password = $password;}
  public function get_role(){return $this->role;}
  public function set_role($role){$this->role = $role;}

}

?>
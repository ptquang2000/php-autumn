<?php

namespace App\PHP;
use Core\{Table, ID, Column, OneToOne};

#[Table(name: 'member')]
class Member 
{
  #[ID(name: 'mid')]
  private $mid;
  #[Column(name: 'uid')]
  private $uid;
  #[Column(name: 'name')]
  private $name;
  #[Column(name: 'email')]
  private $email;
  #[Column(name: 'phone')]
  private $phone;
  #[Column(name: 'address')]
  private $address;
  #[Column(name: 'birth')]
  private $birth;
  #[Column(name: 'img')]
  private $img;

  #[OneToOne(name:'uid', map_by:'User')]
  private $user;
  public function get_user(){return $this->user;}
  public function set_user($user){$this->user = $user;}

  public function get_mid(){return $this->mid;}
  public function set_mid($mid){$this->mid = $mid;}
  public function get_uid(){return $this->uid;}
  public function set_uid($uid){$this->uid = $uid;}
  public function get_name(){return $this->name;}
  public function set_name($name){$this->name = $name;}
  public function get_email(){return $this->email;}
  public function set_email($email){$this->email = $email;}
  public function get_phone(){return $this->phone;}
  public function set_phone($phone){$this->phone = $phone;}
  public function get_address(){return $this->address;}
  public function set_address($address){$this->address = $address;}
  public function get_birth(){return $this->birth;}
  public function set_birth($birth){$this->birth = $birth;}
  public function get_img(){return $this->img;}
  public function set_img($img){$this->img = $img;}

}

?>
<?php

namespace App\PHP;
use Core\{Table, ID, Column};

#[Table(name: 'boardgame')]
class Boardgame 
{
  #[ID(name: 'bid')]
  private $bid;
  #[Column(name: 'name')]
  private $name;
  #[Column(name: 'age_min')]
  private $age_min;
  #[Column(name: 'player_min')]
  private $player_min;
  #[Column(name: 'time_min')]
  private $time_min;
  #[Column(name: 'age_max')]
  private $age_max;
  #[Column(name: 'player_max')]
  private $player_max;
  #[Column(name: 'time_max')]
  private $time_max;
  #[Column(name: 'level')]
  private $level;
  #[Column(name: 'price')]
  private $price;
  #[Column(name: 'img')]
  private $img;

  public function get_bid(){return $this->bid;}
  public function set_bid($bid){$this->bid = $bid;}
  public function get_name(){return $this->name;}
  public function set_name($name){$this->name = $name;}
  public function get_age_min(){return $this->age_min;}
  public function set_age_min($age_min){$this->age_min = $age_min;}
  public function get_player_min(){return $this->player_min;}
  public function set_player_min($player_min){$this->player_min = $player_min;}
  public function get_time_min(){return $this->time_min;}
  public function set_time_min($time_min){$this->time_min = $time_min;}
  public function get_age_max(){return $this->age_max;}
  public function set_age_max($age_max){$this->age_max = $age_max;}
  public function get_player_max(){return $this->player_max;}
  public function set_player_max($player_max){$this->player_max = $player_max;}
  public function get_time_max(){return $this->time_max;}
  public function set_time_max($time_max){$this->time_max = $time_max;}
  public function get_level(){return $this->level;}
  public function set_level($level){$this->level = $level;}
  public function get_price(){return $this->price;}
  public function set_price($price){$this->price = $price;}
  public function get_img(){return $this->img;}
  public function set_img($img){$this->img = $img;}

}

?>
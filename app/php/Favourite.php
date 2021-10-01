<?php

namespace App\PHP;
use Core\{Table, ID, Column};

#[Table(name: 'favourite')]
class Favourite 
{
  #[ID(name: 'fid')]
  private $fid;
  #[Column(name: 'bid')]
  private $bid;
  #[Column(name: 'mid')]
  private $mid;

  public function get_fid(){return $this->fid;}
  public function set_fid($fid){$this->fid = $fid;}
  public function get_bid(){return $this->bid;}
  public function set_bid($bid){$this->bid = $bid;}
  public function get_mid(){return $this->mid;}
  public function set_mid($mid){$this->mid = $mid;}

}

?>
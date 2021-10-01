<?php

namespace App\PHP;
use Core\{Table, ID, Column};

#[Table(name: 'comment')]
class Comment 
{
  #[ID(name: 'cid')]
  private $cid;
  #[Column(name: 'bid')]
  private $bid;
  #[Column(name: 'mid')]
  private $mid;
  #[Column(name: 'content')]
  private $content;

  public function get_cid(){return $this->cid;}
  public function set_cid($cid){$this->cid = $cid;}
  public function get_mid(){return $this->mid;}
  public function set_mid($mid){$this->mid = $mid;}
  public function get_bid(){return $this->bid;}
  public function set_bid($bid){$this->bid = $bid;}
  public function get_content(){return $this->content;}
  public function set_content($content){$this->content = $content;}

}

?>
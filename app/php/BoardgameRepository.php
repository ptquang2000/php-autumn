<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'Boardgame')]
interface BoardGameRepository extends IRepository
{
  public function find_by_name($name);
  public function find_by_name_like($name);
}

?>
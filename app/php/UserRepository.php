<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'User')]
interface UserRepository extends IRepository
{
  public function find_by_username($username);
}

?>
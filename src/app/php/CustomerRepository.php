<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'Customer')]
interface CustomerRepository extends IRepository
{
  public function find_by_name($name);
} 

?>
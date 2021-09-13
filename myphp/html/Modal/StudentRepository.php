<?php

use Core\Attributes\Entity;
use Core\Repository\IRepository;

#[Entity(class:'Student')]
interface StudentRepository extends IRepository
{
  public function find_by_name($name);
  public function find_by_name_and_major($name, $major);
  public function find_by_name_or_major($name, $major);
}

?>
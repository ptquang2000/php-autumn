<?php

namespace App\PHP;

use Core\{IRepository, Entity};


#[Entity(class:'Student')]
interface StudentRepository extends IRepository
{
  public function find_by_year($year);
  public function find_by_name($name);
  public function find_by_course_id($id);
  public function find_by_name_and_course_id($name, $id);
}

?>
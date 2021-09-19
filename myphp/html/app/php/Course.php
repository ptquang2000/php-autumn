<?php

namespace App\PHP;


use Core\{Table, ID, Column, OneToMany};

#[Table(name: 'course')]
class Course
{

  #[ID(name:'id')]
  private $id;
  #[Column(name:'name')]
  private $name;
  #[OneToMany(map_by:'Student')]
  private $students;

  public function get_id()
  {
    return $this->id;
  }
  public function get_name()
  {
    return $this->name;
  }
  public function get_students()
  {
    return $this->students;
  }
  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_name($name)
  {
    $this->name = $name;
  }
  public function set_students($students)
  {
    $this->students = $students;
  }

}

?>

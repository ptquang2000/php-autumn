<?php

namespace App\PHP;


use Core\{Table, ID, Column, ManyToOne};

#[Table(name: 'student')]
class Student
{

  #[ID(name:'id')]
  private $id;
  #[Column(name:'year')]
  private $year;
  #[Column(name:'name')]
  private $name;
  #[ManyToOne(name:'course_id', map_by:'Course')]
  private $course;

  public function get_id()
  {
    return $this->id;
  }
  public function get_year()
  {
    return $this->year;
  }
  public function get_name()
  {
    return $this->name;
  }
  public function get_course()
  {
    return $this->course;
  }
  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_year($year)
  {
    $this->year = $year;
  }
  public function set_name($name)
  {
    $this->name = $name;
  }
  public function set_course($course)
  {
    $this->course = $course;
  }

}

?>
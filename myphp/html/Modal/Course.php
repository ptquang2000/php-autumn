<?php

use Core\Attributes\Column;
use Core\Attributes\ID;
use Core\Attributes\Table;
use Core\Attributes\OneToMany;

#[Table('course')]
class Course {

  #[ID(name:'id')]
  private int $id;
  #[Column(name:'name')]
  private string $name;

  #[OneToMany(map_by:'Student', cascade:OneToMany::SETNULL)]
  private $students;

  public function get_id() {return $this->id;}
  public function set_id($id) {$this->id = $id;}
  public function get_name() {return $this->name;}
  public function set_name($name) {$this->name = $name;}
  public function get_students() {return $this->students;}
  public function set_students($students) {$this->students = $students;}

  public static function CourseSufficent($id, $name)
  {
    $obj = new Course();
    $obj->set_id($id);
    $obj->set_name($name);
    return $obj;
  }
  public static function CourseOnlyId($id)
  {
    $obj = new Course();
    $obj->set_id($id);
    return $obj;
  }
}
?>
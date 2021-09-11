<?php

use Core\Attributes\Column;
use Core\Attributes\ID;
use Core\Attributes\ManyToOne;
use Core\Attributes\Table;

#[Table('student')]
class Student {

  #[ID(name:'id')]
  private int $id;
  #[Column(name:'name')]
  private string $name2;
  #[Column(name:'age')]
  private int $age;
  #[Column(name:'major')]
  private string $major;

  #[ManyToOne(name:'id_course', ref_col_name:'id')]
  private Course $course;

  public static function StudentById($id)
  {
    $obj = new Student();
    $obj->set_id($id);
    return $obj;
  }
  public static function Student($name, $age, $major)
  {
    $obj = new Student();
    $obj->set_name2($name);
    $obj->set_age($age);
    $obj->set_major($major);
    return $obj;
  }
  public static function StudentInludeRela($name, $age, $major, $course)
  {
    $obj = new Student();
    $obj->set_name2($name);
    $obj->set_age($age);
    $obj->set_major($major);
    $obj->set_course($course);
    return $obj;
  }
  public static function StudentSufficent($id, $name, $age, $major)
  {
    $obj = new Student();
    $obj->set_id($id);
    $obj->set_name2($name);
    $obj->set_age($age);
    $obj->set_major($major);
    return $obj;
  }
  public static function StudentLackAge($id, $name, $major)
  {
    $obj = new Student();
    $obj->set_id($id);
    $obj->set_name2($name);
    $obj->set_major($major);
    return $obj;
  }


  public function get_id() {return $this->id;}
  public function set_id($id) {$this->id = $id;}
  public function get_name2() {return $this->name2;}
  public function set_name2($name) {$this->name2 = $name;}
  public function get_age() {return $this->age;}
  public function set_age($age) {$this->age = $age;}
  public function get_major() {return $this->major;}
  public function set_major($major) {$this->major = $major;}
  public function get_course() {return $this->course;}
  public function set_course($course) {$this->course = $course;}

}
?>
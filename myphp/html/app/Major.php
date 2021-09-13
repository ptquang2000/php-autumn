<?php

namespace App;

use Core\Column;
use Core\ID;
use Core\Table;
use Core\OneToMany;

#[Table(name:'major')]
class Major {

  #[ID(name:'id')]
  private $id;
  #[Column(name:'name')]
  private $name;

  #[OneToMany(map_by:'Student', cascade:OneToMany::SETNULL)]
  private $students;

  public static function Major($id, $name)
  {
    $obj = new Major();
    $obj->set_id($id);
    $obj->set_name($name);
    return $obj;
  }

  public static function MajorWithId($id)
  {
    $obj = new Major();
    $obj->set_id($id);
    return $obj;
  }

  public function get_id() {return $this->id;}
  public function set_id($id) {$this->id = $id;}
  public function get_name() {return $this->name;}
  public function set_name($name) {$this->name = $name;}
  public function get_students() {return $this->students;}
  public function set_students($students) {$this->students = $students;}
}
?>
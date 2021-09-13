<?php

namespace App\PHP;

use Core\Column;
use Core\ID;
use Core\ManyToOne;
use Core\Table;

#[Table(name:'student')]
class Student {

  #[ID(name:'id')]
  private $id;
  #[Column(name:'name')]
  private $name;
  #[ManyToOne(name:'major',ref_col_name:'id')]
  private Major $major;

  public static function StudentWithNameMajor($name, $major)
  {
    $obj = new Student();
    $obj->set_name($name);
    $obj->set_major($major);
    return $obj;
  }
  public static function Student($id, $name, $major)
  {
    $obj = new Student();
    $obj->set_id($id);
    $obj->set_name($name);
    $obj->set_major($major);
    return $obj;
  }

  public function get_id() {return $this->id;}
  public function set_id($id) {$this->id = $id;}
  public function get_name() {return $this->name;}
  public function set_name($name) {$this->name = $name;}
  public function get_major() {return $this->major;}
  public function set_major($major) {$this->major = $major;}

}
?>
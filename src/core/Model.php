<?php

namespace Core;

class Model
{
  private $attributes = array();
    
  public function add_attribute($name, $instance)
  {
    $this->attributes[$name]=$instance;
  }
  public function attribute($name)
  {
    return $this->attributes[$name];
  } 
  public function get_all_attributes()
  {
    return $this->attributes;
  }
}
?>
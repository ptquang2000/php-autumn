<?php

namespace Core;

class Model
{
  private $attributes = array();
    
  public function add_attribute($name, $instance)
  {
    $this->attributes[$name]=$instance;
  }
  
  public function get_attribute($name, $attr=null)
  {
    if (!$attr) $attr=$this->attributes;
    if (preg_match_all('/^\$\{[a-z|A-Z|_][a-z|A-Z|_|0-9]*\.[a-z|A-Z|_][a-z|A-Z|_|0-9]*\}$/', $name))
    {
      $variable = explode('.', rtrim(substr($name, 2), '}'));
      $instance = $attr[$variable[0]];
      $reflection = new \ReflectionClass($instance);
      if ($reflection->hasProperty($variable[1]))
      {
        $property = $reflection->getProperty($variable[1]);
        if ($property->isPublic())
          return $instance->{$variable[1]};
        if ($property->isPrivate() && $reflection->hasMethod('get_'.$variable[1]))
          return $instance->{'get_'.$variable[1]}();
      }
      throw new \Exception ('Model "'.$variable[0].'" does not have property "'.$variable[1].'" or properly naming access modifier');
    }
    if (preg_match_all('/^\$\{[a-z|A-Z|_][a-z|A-Z|_|0-9]*\}$/', $name))
    {
      $key = rtrim(substr($name, 2), '}');
      if (array_key_exists($key, $attr))
        return $attr[$key];
      throw new \Exception ('Model "'.$key.'" does not exist');
    }
    if (preg_match_all('/^\@\{.*\}$/', $name))
    {
      return str_replace('@{', '', rtrim($name,'}'));
    }
    throw new \Exception ("Invalid model $name");
  }
}
?>
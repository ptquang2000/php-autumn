<?php

namespace Core;

use Attribute;
#[Attribute(Attribute::TARGET_CLASS)]
class Service {}
#[Attribute(Attribute::TARGET_PROPERTY)]
class Autowired {}
#[Attribute]
class RequestMethod 
{
  const GET = 0;
  const POST = 1;
}
#[Attribute]
class RequestMapping 
{
  public function __construct(public $value, public $method)  {}
  public function path_variable()  
  { 
    //                  ^\/([^\/]+(\/[^\/]+)*)*$
    if (preg_match('/^\/([^\/]+(\/[^\/]+)*)*$/', $this->value) == 1)
    {
      // match args contains {}:   '/\\{[a-z|A-Z|_][a-z|A-Z|0-9|_]*\\}/' 
      // match args contains  $:   '/\\$[a-z|A-Z|_][a-z|A-Z|0-9|_]*/' 
      preg_match_all('/\$[a-z|A-Z|_][a-z|A-Z|_]*/', $this->value, $agrs);
      // return array_map(
      //   function($agr){ return rtrim(ltrim($agr, '{'), '}'); },
      //   $agrs[0]);
      return array_map(
          function($agr){ return ltrim($agr, '$'); },
          $agrs[0]);
    }
    return null;
  }
}
#[Attribute(Attribute::TARGET_CLASS)]
class Controller {}
#[Attribute(Attribute::TARGET_CLASS)]
class RestController {}
#[Attribute(Attribute::TARGET_ALL)]
class ManyToOne
{
  public function __construct(public $name, public $ref_col_name) {}
}
#[Attribute(Attribute::TARGET_ALL)]
class OneToMany
{
  const SETNULL = 0;
  const DELETE = 1;
  public function __construct(public $map_by, public $cascade)  {}
}
#[Attribute(Attribute::TARGET_ALL)]
class Entity
{
  public function __construct(public $class)  {}
}
#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
  private function __construct(public $name)  {}
}
#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
  public function __construct(public $name) {}
}
#[Attribute(Attribute::TARGET_PROPERTY)]
class ID
{
  public function __construct(public $name) {}
}
?>
<?php

namespace Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class ManyToOne
{

  public function __construct(public $name, public $ref_col_name)
  {
  }

}
#[Attribute(Attribute::TARGET_ALL)]
class OneToMany
{

  const SETNULL = 0;
  const REMOVE = 1;
  public function __construct(public $map_by, public $cascade)
  {
  }

}

#[Attribute(Attribute::TARGET_ALL)]
class Entity
{

  public function __construct(public $entity)
  {
  }

}

#[Attribute(Attribute::TARGET_CLASS)]
class Table
{

  private function __construct()
  {  
  }

}

#[Attribute(Attribute::TARGET_CLASS)]
class Service
{

  public function __construct()
  {
  }

}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
  public function __construct(public $name)
  {
  }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class ID
{
  public function __construct(public $name)
  {
  }
}

?>
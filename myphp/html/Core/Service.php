<?php
namespace Core\Service;

use ReflectionClass;

function validateMethod($method)
{
}
trait Service 
{

  public function __construct()
  {

    $reflection = new ReflectionClass($this);
    $attributes = $reflection->getAttributes();

    foreach($reflection->getProperties() as $object) {

      # Get repositoru interface
      $refRepo = $reflection->getProperty($object->name);
      $interface = $refRepo->getType()->getName();
      $refRepo->setAccessible(true);

      // # Process unimplement method
      // $userMethod = array_diff(get_class_methods($interface), get_class_methods('Core\Repository\IRepository'));
      
      // # Get Mapping Enity
      // $entity = (new ReflectionClass($interface))->getAttributes()[0]->getArguments()['entity'];

      $code =<<<EOF
      use Core\Repository\Repository;
      \$refRepo->setValue(\$this, new class extends Repository implements $interface {});
      EOF;
      eval($code);

    } 
  }
}
?>
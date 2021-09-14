<?php
namespace Core;

use ReflectionClass;
use ErrorException;

define ('FIND_BY', [
  'and'=> ['and', '='],
  'is'=> ['and', '='],
  'equal'=> ['and', '='],
  'or' => ['or', '='],
  'lessthan' => ['or', '<'],
  'lessthanequal' => ['or', '<='],
  'greaterthan' => ['or', '>'],
  'greaterthanequal' => ['or', '>='],
  'like' => ['or', 'like'],
  'notlike' => ['or', 'not like'],
]);

trait ServiceTrait
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

      # Process unimplement method
      $userMethod = array_diff(get_class_methods($interface), get_class_methods('Core\IRepository'));

      $unimp_methods = array();

      foreach ($userMethod as $method_name)
      {
        $method = str_replace('find_by_', '', $method_name);
        $case=count($kw=array_intersect(explode('_', $method), array_keys(FIND_BY)));
        echo '<br>';
        if ($case == 0)
        {
          $unimp_methods[] = 
          <<<EOF
          public function $method_name ($$method)
          {
            return \$this->find_by_props(['$method' => $$method]);     
          }
          EOF;
        }
        else if ($case == 1)
        {
          $cond = implode("','",FIND_BY[$kw[array_key_first($kw)]]);
          $cols = explode('_'.$kw[array_key_first($kw)].'_', $method);
          $args = '$' . implode(', $', $cols);
          $fields = implode(', ', array_map(function($e){
            return "'$e'" . '=> $' .$e;
          }, $cols));
          $unimp_methods[] = 
          <<<EOF
          public function $method_name ($args)
          {
            return \$this->find_by_props([$fields], ['$cond']);
          }
          EOF;
        }
        else throw new ErrorException("Invalid defined function in interface $interface");
      }
      $unimp_methods = implode(' ',$unimp_methods);

      $code =<<<EOF
      use Core\Repository;
      \$refRepo->setValue(\$this, new class extends Repository implements $interface {
        $unimp_methods
      });
      EOF;
      eval($code);

    } 
  }
}
?>
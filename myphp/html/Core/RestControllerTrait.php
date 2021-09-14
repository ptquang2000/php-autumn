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

trait RestControllerTrait
{
	public function autowired($class)
	{
		// Interface 
		if (interface_exists($class))
		{
			$interface = $class;

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
			\$new_instance = new class extends Repository implements $interface {	$unimp_methods };
			EOF;
			eval($code);
			return $new_instance;
		}
		// Service Module
		$new_instance = new $class();
		$reflection = new ReflectionClass($class);
		if ($reflection->getAttributes()[0]->getName() == 'Core\Service')
		{
			foreach($reflection->getProperties() as $prop) {
				if (!($attr=$prop->getAttributes()) 
				|| $attr[0]->getName()!='Core\Autowired') continue;

				$reflection_prop =  $reflection->getProperty($prop->getName());
				$reflection_prop->setAccessible(true);
				$class_name = $prop->getType()->getName();
				$reflection_prop->setValue($new_instance, $this->autowired($class_name));
			}
		}
		return $new_instance;
	}
	public function __construct()
	{
		echo 'RestControllerTrait<br>';
		$reflection = new ReflectionClass(get_parent_class($this));
		
		// instantiate autwired properties;
		foreach($reflection->getProperties() as $prop) {
			if (!($attr=$prop->getAttributes()) 
			|| $attr[0]->getName()!='Core\Autowired') continue;            
			
			$reflection_prop =  $reflection->getProperty($prop->getName());
			$reflection_prop->setAccessible(true);
			$class_name = $prop->getType()->getName();
			
			$reflection_prop->setValue($this, $this->autowired($class_name));
		}
	}
}
	
?>
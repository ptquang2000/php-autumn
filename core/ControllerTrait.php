<?php

namespace Core;

use Core\Model;

trait ControllerTrait
{
	private Model $model;
	public View $view;

	public function __construct()
	{
		$reflection = new \ReflectionClass(get_parent_class($this));
		
		// instantiate autwired properties;
		foreach($reflection->getProperties() as $prop) {
			if (!($attr=$prop->getAttributes()) 
			|| $attr[0]->getName()!='Core\Autowired') continue;            
			
			$reflection_prop =  $reflection->getProperty($prop->getName());
			$reflection_prop->setAccessible(true);
			$class_name = $prop->getType()->getName();
			
			$reflection_prop->setValue($this, autowired($class_name));
		}

		// init model
		$this->model = new Model();
	}

	public function get_model()
	{
		return $this->model;
	}

	public function init_view($template)
	{
		$template = $template ?? '';
		$this->view = new View($template, $this->model);
		return $this->view;
	}
}

?>
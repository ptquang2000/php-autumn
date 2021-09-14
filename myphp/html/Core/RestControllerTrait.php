<?php

namespace Core;
use ReflectionClass;

trait RestControllerTrait
{
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
            $reflection_prop->setValue($this, new $class_name());
        }
    }
}

?>
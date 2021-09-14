<?php

namespace Core;

use ArgumentCountError;
use ReflectionClass;
use Exception;
use Core\RequestMethod;

function Error404($url=''){
	return "Error404: Path $url does not exist";
};

function serialize($obj){
	if (!$obj) return null;
	$props = (new ReflectionClass($obj))->getProperties();
	$new_ins = new \stdClass();
	foreach($props as $prop)
	{
		$new_ins->{$prop->getName()} = $obj->{'get_'.$prop->getName()}();
		if (!is_scalar($new_ins->{$prop->getName()}))
		{
			$parsed_obj = serialize($new_ins->{$prop->getName()});
			if ($parsed_obj) $new_ins->{$prop->getName()} = $parsed_obj;
			else unset($new_ins->{$prop->getName()} );
		}
	}
	return $new_ins;
}

class Router
{
	private static $url;
	public static $contents;
	private static $paths = array();

	private static function restcontroller_result_handler($result)
	{
		if (!$result) return;
		if (is_iterable($result))
			return '['.implode(',',array_map(function($instance){
				return json_encode(serialize($instance));
			}, $result)).']';
		return json_encode(serialize($result));
	}
	public static function setup()
	{
		$df_app = glob("app/php/*.php");
		foreach ($df_app as $filename) 
		{
			$class = str_replace('app/php/', 'App\\PHP\\',
			str_replace('.php', '', $filename));
			$reflection = new ReflectionClass($class);
			$attributes = $reflection->getAttributes();
			if ($attributes && ($attributes[0]->getName() == 'Core\Controller'
			||  $attributes[0]->getName() == 'Core\RestController'))
			{
				$methods = $reflection->getMethods();
				foreach($methods as $method)
					if ($method->getAttributes()[0]->getName() == 'Core\RequestMapping')
					{
						$method_attr = $method->getAttributes()[0];
						$attr_args = $method->getAttributes()[0]->getArguments();
						// check error: attribute defining path's variables
						$path_params = $method_attr->newInstance()->path_variable();
						$method_para = array_map(function($a) { return $a->name; }
						,$method->getParameters());
						if (array_diff_assoc($method_para, $path_params))
							throw new ArgumentCountError(
								'Path "'.$attr_args['value'].
								'" in method "'.$method->getName().
								'" defines params error');
						// echo '------1<br>';
						if (array_key_exists($attr_args['value'], Router::$paths))
							throw new Exception(
								'Path "'.$attr_args['value'].
								'" has already defined in class "'.Router::$paths[$attr_args['value']]['class'].
								'" method "'.	Router::$paths[$attr_args['value']]['class_method']);

						Router::$paths[$attr_args['value']] = [
							'method'=> $attr_args['method'] ?? RequestMethod::GET,
							'class' => $class,
							'class_method' => $method->getName(),
							'type' => $attributes[0]->getName().'Trait',
							'params'=> $method_attr->newInstance()->path_variable()
						];
					}
				}	
		}
	}

	private static function get_path_params($path)
	{
		// validate attribute defining path
		$url_parts = explode('/', Router::$url);
		$path_parts = explode('/', $path);
		if (count($url_parts) != count($path_parts)) return null;
		
		// if the number of params matches
		$df_parts = array_diff_assoc($url_parts, $path_parts);
		$path_params = Router::$paths[$path]['params'];
		if (count($df_parts) != count($path_params))  return null;

		return $df_parts;
	}

	public static function route($url)
	{
		Router::$url = $url;
		if (array_key_exists(Router::$url, Router::$paths))
		{
			$class = Router::$paths[$url]['class'];
			$type = Router::$paths[$url]['type'];
			// instantiate controller
			$code = <<< EOF
			\$controler = new class extends $class
			{	use $type;	};
			EOF;
			eval($code);
			if ($type == 'Core\RestControllerTrait')
				Router::$contents = Router::restcontroller_result_handler($controler->{
					Router::$paths[Router::$url]['class_method']}()) ?? '';
			else if ($type == 'Core\ControllerTrait')
				Router::$contents = $controler->{
					Router::$paths[Router::$url]['class_method']}() ?? '';	
		}
		else
		{
			foreach (Router::$paths as $path => $props)
			{
				if ($props['params'])
				{
					$df_parts = Router::get_path_params($path);
					if (!$df_parts) 
					{
						if ($path === array_key_last(Router::$paths))
							throw new Exception (Error404($url));
						continue;
					}
					$class = Router::$paths[$path]['class'];
					$type = Router::$paths[$path]['type'];
					// instantiate controller
					$code = <<< EOF
					\$controler = new class extends $class
					{	use $type;	};
					EOF;
					eval($code);
					if ($type == 'Core\RestControllerTrait')
						Router::$contents = Router::restcontroller_result_handler($controler->{
							$props['class_method']}(...$df_parts)) ?? '';
					else if ($type == 'Core\ControllerTrait')
						Router::$contents = $controler->{
							$props['class_method']}(...$df_parts) ?? '';
					break;
				}
				if ($path === array_key_last(Router::$paths))
					throw new Exception(Error404($path));
			}
		}
	}
}
	
?>
<?php

namespace Core;

use ArgumentCountError;
use ReflectionClass;
use Exception;
use Core\RequestMethod;

function Error404($url='', $method=''){
	return "Error404: Path $url does not exist";
};

class Router
{
	private static $url;
	public static $paths = array();
	private static $controller;
	private static $path;
	private static $df_parts=array();
	private static $type;

	public static function setup($filename)
	{
		$class = str_replace('app/php/', 'App\\PHP\\',
		str_replace('.php', '', $filename));
		$reflection = new ReflectionClass($class);
		$attributes = $reflection->getAttributes();
		if (!$attributes || ($attributes[0]->getName() != 'Core\Controller'
		&&  $attributes[0]->getName() != 'Core\RestController')) return;

		$methods = $reflection->getMethods();
		foreach($methods as $method)
		{
			if ($method->getAttributes()[0]->getName() != 'Core\RequestMapping')
				continue;
			
			$method_attr = $method->getAttributes()[0];
			$attr_args = $method->getAttributes()[0]->getArguments();

			// check error: attribute defining path's variables
			$path_params = $method_attr->newInstance()->path_variable();
			
			$path_model = array_filter($method->getParameters(),
				function($param){
					return $param->getType() == 'Core\Model';
				});
			if (count($path_model) > 1)
				throw new Exception ('Method "'.$method->getName().' has two many 
				Model type parameters"');

			$path_object = array_filter($method->getParameters(),
				function($param){
					return class_exists($param->getType())
						&& $param->getType() != 'Core\Model';
				});
			if (count($path_object) > 1)
				throw new Exception ('Method "'.$method->getName().' has two many 
				Object type parameters"');

			$method_param = array_map(
				function($a){return $a->name;}
				, array_diff_assoc(
					array_diff_assoc($method->getParameters(), $path_model), 
					$path_object));
			
			if (array_diff_assoc($path_params, array_values($method_param)))
				throw new ArgumentCountError(
					'Path "'.$attr_args['value'].
					'" in method "'.$method->getName().
					'" defines params error');

			// check if path has been defined 		
			if (array_key_exists($attr_args['value'], Router::$paths))
				throw new Exception(
					'Path "'.$attr_args['value'].
					'" has already defined in class "'.
					Router::$paths[$attr_args['value']]['class'].
					'"\'s method "'.
					Router::$paths[$attr_args['value']]['class_method'].
					'"');

			Router::$paths[$attr_args['value']] = [
				'method'=> $attr_args['method'] ?? RequestMethod::GET,
				'class' => $class,
				'class_method' => $method->getName(),
				'type' => $attributes[0]->getName().'Trait',
				'params'=> $method_attr->newInstance()->path_variable(),
				'model' => array_key_first($path_model)
			];
			if ($path_object)
				Router::$paths[$attr_args['value']]['object'] = [
						'idx' => array_key_first($path_object),
						'type' => $path_object[array_key_first($path_object)]->getType()->getName()
				];
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
		if (pathinfo($url, PATHINFO_EXTENSION))
		{
			$url = str_replace('/', DL, $url);
			$url = 'app'.DL.'static'.DL.$url;
			if (file_exists($url)) include $url;
			return;
		}
		if (array_key_exists(Router::$url, Router::$paths))
		{
			if ($_SERVER['REQUEST_METHOD'] !== Router::$paths[$url]['method'])
				throw new Exception (Error404($url));

			Router::$path = $url;
			$class = Router::$paths[$url]['class'];
			Router::$type = $type = Router::$paths[$url]['type'];
			// instantiate controller
			$code = <<< EOF
			\$controller = new class extends $class
			{	use $type;	};
			EOF;
			eval($code);
			Router::$controller = $controller;
		}
		else foreach (Router::$paths as $path => $props)
		{
			Router::$df_parts = Router::get_path_params($path);
			if (!$props['params'] || !Router::$df_parts
			|| $_SERVER['REQUEST_METHOD'] !== Router::$paths[$path]['method']) 
			{
				if ($path === array_key_last(Router::$paths))
					throw new Exception (Error404($url));
				continue;
			}
			Router::$path = $path;
			$class = Router::$paths[$path]['class'];
			Router::$type = $type = Router::$paths[$path]['type'];
			// instantiate controller
			$code = <<< EOF
			\$controller = new class extends $class
			{	use $type;	};
			EOF;
			eval($code);
			Router::$controller = $controller;
			break;
		}

		// set model for method
		if (isset(Router::$paths[Router::$path]['model']))
			array_splice(Router::$df_parts, 
				Router::$paths[Router::$path]['model'], 0, 
				[$controller->init_model()]);
		// set model for method
		if (array_key_exists('object', Router::$paths[Router::$path]))
		{
			$reflection = new \ReflectionClass(Router::$paths[Router::$path]['object']['type']);	
			$object = new Router::$paths[Router::$path]['object']['type']();
			$body = json_decode(file_get_contents('php://input'));
			foreach($reflection->getProperties() as $prop)
				$object->{'set_'.$prop->getName()}(htmlspecialchars($body->{$prop->getName()} ?? null));
			array_splice(Router::$df_parts, 
				Router::$paths[Router::$path]['object']['idx'], 0, 
				[$object]);
		}
		// call url handle
		$result = Router::$controller->{Router::$paths[Router::$path]['class_method']}
			(...Router::$df_parts);
		if ($type == 'Core\RestControllerTrait')
			RestControllerTrait::encode($result);
		else if ($type == 'Core\ControllerTrait')
			Router::$controller->render($result);	
	}
}
	
?>
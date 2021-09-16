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
				{
					if ($method->getAttributes()[0]->getName() != 'Core\RequestMapping')
						continue;
					
					$method_attr = $method->getAttributes()[0];
					$attr_args = $method->getAttributes()[0]->getArguments();

					// check error: attribute defining path's variables
					$path_params = $method_attr->newInstance()->path_variable();
					$method_para = array_map(function($a) {
						return $a->name; },
						array_filter($method->getParameters(), function($param){
							return $param->getType() != 'Core\Model';
							})
					);
					
					$path_model = array_filter($method->getParameters(),
						function($param){
							return $param->getType() == 'Core\Model';
						});
					if (count($path_model) > 1)
						throw new Exception ('Method "'.$method->getName().' has two many Model type parameters"');

					if (array_diff_uassoc($path_params, $method_para, function ($a, $b) 
						use ($path_model){
						$idx = array_key_first($path_model);
						if ($b < $idx)
							return $a === $b ? 0 : -1;
						return $a === ($b-1) ? 0 : -1;
					}))
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
							Router::$paths[$attr_args['value']]['class_method']).
							'"';

					Router::$paths[$attr_args['value']] = [
						'method'=> $attr_args['method'] ?? RequestMethod::GET,
						'class' => $class,
						'class_method' => $method->getName(),
						'type' => $attributes[0]->getName().'Trait',
						'params'=> $method_attr->newInstance()->path_variable(),
						'model' => array_key_first($path_model),
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

			$class = Router::$paths[$url]['class'];
			$type = Router::$paths[$url]['type'];
			// instantiate controller
			$code = <<< EOF
			\$controller = new class extends $class
			{	use $type;	};
			EOF;
			eval($code);
			$df_model = array();
			if (isset(Router::$paths[$url]['model']))
				array_splice($df_model,
				Router::$paths[$url]['model'], 0,
				[$controller->init_model()]);

			if ($type == 'Core\RestControllerTrait')
				RestControllerTrait::encode(
					$controller->{Router::$paths[Router::$url]['class_method']}(...$df_model)	);
			else if ($type == 'Core\ControllerTrait')
				$controller->render(
					$controller->{Router::$paths[Router::$url]['class_method']}
					(...$df_model) );	
		}
		else
		{
			foreach (Router::$paths as $path => $props)
			{
				if (!$props['params'] || !($df_parts=Router::get_path_params($path))
				|| $_SERVER['REQUEST_METHOD'] !== Router::$paths[$path]['method']) 
				{
					if ($path === array_key_last(Router::$paths))
						throw new Exception (Error404($url));
					continue;
				}

				$class = Router::$paths[$path]['class'];
				$type = Router::$paths[$path]['type'];
				// instantiate controller
				$code = <<< EOF
				\$controller = new class extends $class
				{	use $type;	};
				EOF;
				eval($code);
				if (isset(Router::$paths[$path]['model']))
					array_splice($df_parts, 
						Router::$paths[$path]['model'], 0, 
						[$controller->init_model()]);

				if ($type == 'Core\RestControllerTrait')
					RestControllerTrait::encode(
						$controller->{$props['class_method']}(...$df_parts)	);
				else if ($type == 'Core\ControllerTrait')
					$controller->render(
						$controller->{$props['class_method']}(...$df_parts) );
				break;
			}
		}
	}
}
	
?>
<?php

namespace Core;

use ArgumentCountError;
use ReflectionClass;
use Exception;
use Core\RequestMethod;

class Router
{
	public static $url;
	public static $path;
	public static $request_method;
	public static $paths = array();
	public static $type;
	private static $controller;
	private static $df_parts=array();
	private static $security;

	public static function setup($filename)
	{
		$class = str_replace('app/php/', 'App\\PHP\\',
		str_replace('.php', '', $filename));
		$reflection = new ReflectionClass($class);
		$attributes = $reflection->getAttributes();

		if (is_subclass_of($class, 'Core\SecurityConfiguration'))
		{
			$security = new $class();
			array_map(
				function ($className) use($security) {
					$className = str_replace('app/php/', 'App\\PHP\\',
					str_replace('.php', '', $className));
					if (in_array('Core\\UserDetailsService', class_implements($className)))
						$security->set_userdetails_service(new $className());
				},
				glob("app/php/*.php")
			);
			$securityRef = new ReflectionClass($security);
			array_map(
				function ($attr) use ($security) {
					if ($attr->getName() == 'Core\EnableSecurity')
						$security->set_enable(true);
				},$securityRef->getAttributes());
			
			if ($security->is_enable())
			{
				$http = new HttpSecurity();
				$security->httpConfigure($http);
				$security->http_security = $http;
			}
			Router::$security = $security;
		}

		if (!$attributes || ($attributes[0]->getName() != 'Core\Controller'
		&&  $attributes[0]->getName() != 'Core\RestController')) return;

		$methods = $reflection->getMethods();
		foreach($methods as $method)
		{
			if (!$method->getAttributes() || 
			$method->getAttributes()[0]->getName() != 'Core\RequestMapping')
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
			$request_method = $method->getAttributes()[0]->newInstance()->method;
			if (
				array_key_exists($attr_args['value'], Router::$paths)
			&& 
				isset(Router::$paths[$request_method])
			)
				throw new Exception(
					'Path "'.$attr_args['value'].
					'" has already defined in class "'.
					Router::$paths[$request_method][$attr_args['value']]['class'].
					'"\'s method "'.
					Router::$paths[$request_method][$attr_args['value']]['class_method'].
					'"');
			
			Router::$paths[$attr_args['value']][$request_method] = [
				'class' => $class,
				'class_method' => $method->getName(),
				'type' => $attributes[0]->getName().'Trait',
				'params'=> $method_attr->newInstance()->path_variable(),
				'model' => array_key_first($path_model)
			];
			if ($path_object)
				Router::$paths[$attr_args['value']][$request_method]['object'] = [
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
		$path_params = Router::$paths[$path][Router::$request_method]['params'];
		if (count($df_parts) != count($path_params))  return null;

		return $df_parts;
	}

	public static function route($url)
	{
		Router::$url = $url;
		Router::$request_method = $_SERVER['REQUEST_METHOD'];

		// security filter
		if (isset(router::$security) && 
		Router::$request_method === RequestMethod::POST &&
		$url == ($GLOBALS['config']['security.login_processing'] ?? '/login'))
			Router::$security->authenticate();
		if (isset(router::$security) && 
		Router::$request_method === RequestMethod::GET &&
		$url == ($GLOBALS['config']['security.logout_processing'] ?? '/logout'))
			Router::$security->logout();

		if (pathinfo($url, PATHINFO_EXTENSION))
		{
			$url = str_replace('/', DL, $url);
			$url = 'app'.DL.'static'.DL.$url;
			if (file_exists($url)) include $url;
			return;
		}
		if (array_key_exists(Router::$url, Router::$paths))
		{
			if ( !array_key_exists(Router::$request_method, Router::$paths[$url]) )
				throw new HttpException('404');

			Router::$path = $url;
			$class = Router::$paths[$url][Router::$request_method]['class'];
			Router::$type = $type = Router::$paths[$url][Router::$request_method]['type'];
			// instantiate controller
			$code = <<< EOF
			\$controller = new class extends $class
			{	use $type;	};
			EOF;
			eval($code);
			Router::$controller = $controller;
		}
		else foreach (Router::$paths as $path => $request_methods)
		{
			Router::$df_parts = Router::get_path_params($path);
			if (
				!array_key_exists(Router::$request_method, $request_methods)
				||
				!$request_methods[Router::$request_method]['params'] || !Router::$df_parts
			) 
			{
				if ($path === array_key_last(Router::$paths))
					throw new HttpException('404');
				continue;
			}
			Router::$path = $path;
			$class = Router::$paths[$path][Router::$request_method]['class'];
			Router::$type = $type = Router::$paths[$path][Router::$request_method]['type'];
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
		if (isset(Router::$paths[Router::$path][Router::$request_method]['model']))
			array_splice(Router::$df_parts, 
				Router::$paths[Router::$path][Router::$request_method]['model'], 0, 
				[$controller->get_model()]);
		// set model for method
		if (array_key_exists('object', Router::$paths[Router::$path][Router::$request_method]))
		{
			$reflection = new \ReflectionClass(Router::$paths[Router::$path][Router::$request_method]['object']['type']);	
			$object = new Router::$paths[Router::$path][Router::$request_method]['object']['type']();
			$body = json_decode(file_get_contents('php://input'));
			foreach($reflection->getProperties() as $prop)
				$object->{'set_'.$prop->getName()}(htmlspecialchars($body->{$prop->getName()} ?? null));
			array_splice(Router::$df_parts, 
				Router::$paths[Router::$path][Router::$request_method]['object']['idx'], 0, 
				[$object]);
		}
		// call url handle
		if (isset(Router::$security) && Router::$security->is_enable()) Router::$security->authorize(Router::$path);

		$result = Router::$controller->{Router::$paths[Router::$path][Router::$request_method]['class_method']}
			(...Router::$df_parts);
		if (Router::$type == 'Core\RestControllerTrait')
			echo RestControllerTrait::encode($result);
		else if (Router::$type == 'Core\ControllerTrait')
			Router::$controller->init_view($result)->render();	
	}
}
	
?>
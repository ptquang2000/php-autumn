<?php

namespace Core;

use ArgumentCountError;
use ReflectionClass;
use Exception;

define ('Exception404', 'Exception404: Path does not exist');

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
	private static $contents;
	private static function restcontroller_result_handler($result)
	{
		if (!$result) return;
		if (is_iterable($result))
			// return implode('<br>',array_map(function($instance){
			// 	return json_encode(serialize($instance));
			// }, $result));
			return array_map(function($instance){
				return json_encode(serialize($instance));
			}, $result);
		return json_encode(serialize($result));
	}
	private static function check_matched_path($method)
	{
		$method_attr = $method->getAttributes()[0];
		// check error: attribute defining path's variables
		$path_para = $method_attr->newInstance()->path_variable();
		$method_para = array_map(function($a) { return $a->name; }
		,$method->getParameters());
		if (array_diff_assoc($method_para, $path_para))
			throw new ArgumentCountError(
				'Path "'.$method_attr->getArguments()['value'].
				'" in method "'.$method->getName().
				'" defines variable error');
		// echo '------1<br>';
			
		// validate attribute defining path
		$url_parts = explode('/', Router::$url);
		$path_parts = explode('/', $method_attr->getArguments()['value']);
		// var_dump($url_parts);
		// echo '<br>';
		// var_dump($path_parts);
		if (count($url_parts) != count($path_parts))
				throw new Exception (Exception404);
		// echo '------2<br>';
		
		// if the number of params matches
		$df_parts = array_diff_assoc($url_parts, $path_parts);
		// var_dump($df_parts, $path_para);
		if (count($df_parts) != count($path_para))
			throw new Exception (Exception404);
		// echo '------3<br>';
	
		return $df_parts;
	}
	public static function route($url)
	{
		Router::$url = $url;
		$df_app = glob("app/php/*.php");
		foreach ($df_app as $filename) 
		{
			$class = str_replace('app/php/', 'App\\PHP\\',
			str_replace('.php', '', $filename));
			$reflection = new ReflectionClass($class);
			$attributes = $reflection->getAttributes();
			
			if ($attributes && $attributes[0]->getName() == 'Core\RestController')
			{
				$methods = $reflection->getMethods();
				foreach($methods as $method)
				{
					if ($method->getAttributes()[0]->getName() == 'Core\RequestMapping')
					{
						try {$df_parts = Router::check_matched_path($method);}
						catch (Exception $e)
						{
							if ($e->getMessage() == Exception404) continue;
							else throw $e;
						}
						// instantiate controller
						$code = <<< EOF
						\$controler = new class extends $class
						{	use Core\RestControllerTrait;	};
						EOF;
						eval($code);
						Router::$contents = Router::restcontroller_result_handler(
							$controler->{$method->getName()}(...$df_parts)) ?? '';
						break;
					}
				}
			}
			if (isset(Router::$contents)) break;
			// path not found
			if ($filename === $df_app[array_key_last($df_app)]) 
				throw new Exception (Exception404);
		}
		echo Router::$contents;
	}
}
	
	?>
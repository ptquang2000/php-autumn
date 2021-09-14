<?php

namespace Core;

use ArgumentCountError;
use ReflectionClass;

class Router
{
	
	public static function route($url)
	{
		foreach (glob("app/php/*.php") as $filename) 
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
					$method_attr = $method->getAttributes()[0];
					if ($method_attr->getName() == 'Core\RequestMapping')
					{                   
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
						$url_parts = explode('/', $url);
						$path_parts = explode('/', $method_attr->getArguments()['value']);
						if (count($url_parts) != count($path_parts)) continue;
						// echo '------2<br>';
						
						// if the number of params matches
						$df_parts = array_diff_assoc($url_parts, $path_parts);
						if (count($df_parts) != count($path_para)) continue;
						// echo '------3<br>';
						
						// instantiate controller
						$code = <<< EOF
						\$controler = new class extends $class
						{
							use Core\RestControllerTrait;
						};
						EOF;
						eval($code);
						$controler->{$method->getName()}(...$df_parts);
						break;
					}
				}
				break;
			}
		}
	}
}
	
	?>
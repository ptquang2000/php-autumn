<?php

namespace Core;

use ReflectionClass;
use App\PHP\StudentController;

class Router
{
    public static function route($url)
    {
        foreach (glob("app/php/*.php") as $filename) {
            $class = str_replace('app/php/', 'App\\PHP\\',
                str_replace('.php', '', $filename));
            $reflection = new ReflectionClass($class);
            $attributes = $reflection->getAttributes();

            if ($attributes && $attributes[0]->getName() == 'Core\RestController')
            {
                $methods = $reflection->getMethods();
                foreach($methods as $method)
                {
                    if ($method->getAttributes()[0]->getName() == 'Core\RequestMapping'
                    &&  $method->getAttributes()[0]->getArguments()['value'] == $url)
                    {
                        $code = <<< EOF
                        \$controler = new class extends $class
                        {
                            use Core\RestController;
                        };
                        EOF;
                        eval($code);
                        $controler->{$method->getName()}();
                        break;
                    }
                }
                break;
            }
        }
    }
}

?>
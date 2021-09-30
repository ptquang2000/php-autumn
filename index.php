<?php
session_start();

include_once 'core\\Attributes.php';
include_once 'core\\Functions.php';
define("DL", "\\");

spl_autoload_register(function($class)
{
  $parts = explode(DL, $class);
  $file = array_pop($parts);
  $parts = implode(DL,array_map(function($item){
    return strtolower($item);
  },$parts));
  $path = __DIR__.DL.$parts.DL.$file.'.php';
  if (file_exists($path))
    include_once $path;
});

define('__STATIC__', 'app'.DL.'static'.DL);
define('__TEMPLATE__', 'app'.DL.'templates'.DL);

// configuration
$config = parse_ini_file('config.ini');

// Routing
$url = $_SERVER['REQUEST_URI'];
// $url = preg_replace('/(^\/)/', '', $url);
$url = preg_replace('/(\\/?\?.*)/', '', $url);

use Core\Router;

// setup
$df_app = glob("app/php/*.php");
foreach ($df_app as $filename) 
  Router::setup($filename);

Router::route($url);
?>
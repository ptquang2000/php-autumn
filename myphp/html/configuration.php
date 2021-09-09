<?php
  define('ROOT', $_SERVER['REQUEST_URI']);
  define('PROP_CONF', $_SERVER['DOCUMENT_ROOT'] . '/properties.conf');
  define('mysql', $properties['mysql']);

  function configure($file){
    $arr = [];
    if (file_exists($file)){

      $handle = fopen($file, 'r');
      while (($line = fgets($handle)) !== FALSE) {
        $properties = explode('=', $line);
        $prefix = explode('.', $properties[0]);
        if (!array_key_exists($prefix[0], $arr)){
          $arr[preg_replace('/\s+/', '', $prefix[0])] = [preg_replace('/\s+/', '', $prefix[1]) => preg_replace('/\s+/', '', $properties[1])];
        } else{
          $arr[preg_replace('/\s+/', '', $prefix[0])][preg_replace('/\s+/', '', $prefix[1])] = preg_replace('/\s+/', '', $properties[1]);
        }
      }
      fclose($handle);
      
    }else{
      header('Location: ' . '/repository/error.html');
    }
    return $arr;
  }

  class Entity {

    $properties = configure(PROP_CONF);

    public function __construct(){}
  }
  // $conn = mysqli_connect(mysql['url'], mysql['username'], mysql['password'], mysql['dbname']);
?>
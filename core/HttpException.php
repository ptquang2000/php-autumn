<?php

namespace Core;

class HttpException extends \Exception
{
  public function __construct($message, $code=0, \Throwable $previous = null)
  {
    switch ($message)
    {
      case '403': 
      {
        include 'core\\error\\403.html';
        exit();
      }
      case '404': 
      {
        include 'core\\error\\404.html';
        exit();
      }
    }
  }
}
?>
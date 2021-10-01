<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Autowired, Model, Router};

#[Controller]
class RegisterController
{

  #[RequestMapping(value: '/register', method: RequestMethod::GET)]
  function get_register()
  {
    return 'register.html';
  }
  #[RequestMapping(value: '/login', method: RequestMethod::GET)]
  function get_login()
  {
    include __TEMPLATE__.'login.php';
  }
}

?>
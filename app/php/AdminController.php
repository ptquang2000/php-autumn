<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod};

#[Controller]
class AdminController
{
  #[RequestMapping(value: '/list-user', method: RequestMethod::GET)]
  function get_list_user()
  {
    return 'list-user.html';
  }

  #[RequestMapping(value: '/user-info', method: RequestMethod::GET)]
  function get_user_info()
  {
    return 'user-info.html';
  }
}

?>
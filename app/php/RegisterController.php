<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Autowired, Model};

#[Controller]
class RegisterController
{
  #[Autowired]
  private MyUserDetailsService $usedetails_service;
  #[Autowired]
  private MemberService $member_service;

  #[RequestMapping(value: '/register', method: RequestMethod::GET)]
  function get_register()
  {
    return 'register.php';
  }

  #[RequestMapping(value: '/do-register', method: RequestMethod::POST)]
  function post_do_register(Model $model)
  {
    $user = form_model('User');
    $member = form_model('Member');
    try{
      $user = $this->usedetails_service->save_user($user);
    }catch (\mysqli_sql_exception $e)
    {
      if (preg_match('/^Duplicate entry \'.*\' for key \'username\'$/', $e->getMessage()) == 1)
      {
        $model->add_attribute('register_error', 'Username has been used'); 
        return 'register.php';
      }
      throw $e;
    }
    $member->set_uid($user->get_uid());
    $member = $this->member_service->save_member($member);
    return 'Location: /member-info';
  }


  #[RequestMapping(value: '/login', method: RequestMethod::GET)]
  function get_login()
  {
    return 'login.php';
  }
}

?>
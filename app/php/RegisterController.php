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
    return 'register.html';
  }

  #[RequestMapping(value: '/do-register', method: RequestMethod::POST)]
  function post_do_register()
  {
    $user = form_model('User');
    $member = form_model('Member');
    try{
      $user = $this->usedetails_service->new_user($user);
    }catch (\mysqli_sql_exception $e)
    {
      if (preg_match('/^Duplicate entry \'.*\' for key \'username\'$/', $e->getMessage()) == 1)
      return 'Location: /register?error=Username has been used';
      throw $e;
    }
    $member->set_uid($user->get_uid());
    $member = $this->member_service->save_member($member);
    return 'Location: /member-info';
  }


  #[RequestMapping(value: '/login', method: RequestMethod::GET)]
  function get_login()
  {
    return 'login.html';
  }
}

?>
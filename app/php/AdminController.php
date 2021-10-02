<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Model, Autowired};

#[Controller]
class AdminController
{
  #[Autowired]
  private MemberService $member_service;

  #[RequestMapping(value: '/list-user', method: RequestMethod::GET)]
  function get_list_user()
  {
    return 'list-user.html';
  }
  #[RequestMapping(value: '/member-info/$mid', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['ADMIN'])]
  function get_member_info_admin(Model $model, $mid)
  {
    $member = $this->member_service->get_member($mid);
    $model->add_attribute('member', $member);
    include __TEMPLATE__.'member-info.php';
  }
}

?>
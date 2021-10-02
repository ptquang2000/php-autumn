<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Model, Autowired, EnableSecurity};

#[Controller]
class AdminController
{
  #[Autowired]
  private MemberService $member_service;
  #[Autowired]
  private CommentService $comment_service;

  #[RequestMapping(value: '/list-member', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['ADMIN'])]
  function get_list_user()
  {
    $members = $this->member_service->get_all_members();
    $members = array_map(function($member) {
      return [
      'mid' => $member->get_mid(),
      'username' => $member->get_user()->get_username(),
      'email' => $member->get_email(),
      'phone' => $member->get_phone(),
      'address' => $member->get_address(),
      'birth' => $member->get_birth(),
      'img' => $member->get_img()
    ];}, $members);
    include __TEMPLATE__.'list-member.php';
  }

  #[RequestMapping(value: '/member-info/$mid', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['ADMIN'])]
  function get_member_info_admin($mid)
  {
    $member = $this->member_service->get_member($mid);
    if (!$member) 
    # raise 404
      return;
    $member = [
      'mid' => $member->get_mid(),
      'username' => $member->get_user()->get_username(),
      'email' => $member->get_email(),
      'phone' => $member->get_phone(),
      'address' => $member->get_address(),
      'birth' => $member->get_birth(),
      'img' => $member->get_img()
    ];
    include __TEMPLATE__.'member-info.php';
  }
  #[RequestMapping(value: '/edit-comment', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['ADMIN'])]
  function post_edit_comment()
  {
    $edited_cmt = form_model('Comment');
    $edited_cmt = $this->comment_service->save_comment($edited_cmt);
    header('Location: /product-detail?id='.$edited_cmt->get_bid());
    exit();
  }
  #[RequestMapping(value: '/delete-comment', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['ADMIN'])]
  function post_delete_comment()
  {
    $deleted_cmt = form_model('Comment');
    $bid = $deleted_cmt->get_bid();
    $deleted_cmt = $this->comment_service->delete_comment($deleted_cmt);
    header('Location: /product-detail?id='.$bid);
    exit();
  }
}

?>
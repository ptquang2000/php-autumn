<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Model, Autowired, EnableSecurity, HttpException};
use GdImage;

#[Controller]
class AdminController
{
  #[Autowired]
  private MemberService $member_service;
  #[Autowired]
  private BoardgameService $boardgame_service;
  #[Autowired]
  private FavouriteService $favourite_service;
  #[Autowired]
  private CommentService $comment_service;
  #[Autowired]
  private MyUserDetailsService $userdetails_service;

  #[RequestMapping(value: '/member-list', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['ADMIN'])]
  function get_list_user(Model $model)
  {
    $members = $this->member_service->get_all_members();
    $members = array_map(function($member) {
      return [
      'mid' => $member->get_mid(),
      'uid' => $member->get_uid(),
      'username' => $member->get_user()->get_username(),
      'email' => $member->get_email(),
      'phone' => $member->get_phone(),
    ];}, $members);
    $model->add_attribute('members', $members);
    return 'member-list.php';
  }

  #[RequestMapping(value: '/member-info/$mid', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['ADMIN'])]
  function get_member_info_admin(Model $model, $mid)
  {
    $member = $this->member_service->get_member($mid);
    if (!$member) 
      throw new HttpException('404');
    $member = [
      'mid' => $member->get_mid(),
      'uid' => $member->get_uid(),
      'name' => $member->get_name(),
      'email' => $member->get_email(),
      'phone' => $member->get_phone(),
      'address' => $member->get_address(),
      'birth' => $member->get_birth(),
      'img' => $member->get_img()
    ];
    $model->add_attribute('member', $member);
    return 'member-info.php';
  }

  #[RequestMapping(value: '/delete-member', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['ADMIN'])]
  function post_delete_member()
  {
    $member = form_model('Member');
    $this->member_service->delete_member($member);
    $this->userdetails_service->delete_user($member->get_uid());
    return 'Location: /member-list';
  }

  #[RequestMapping(value: '/edit-product', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['ADMIN'])]
  function post_edit_product()
  {
    $boardgame = form_model('Boardgame');
    if (is_uploaded_file($_FILES['image-file']['tmp_name']) &&
    getimagesize($_FILES['image-file']['tmp_name']))
    {
      if (file_exists(__STATIC__.'img'.DL.$boardgame->get_img()))
        unlink(__STATIC__.'img'.DL.$boardgame->get_img());
      $file_type = '.'.pathinfo($_FILES['image-file']['name'], PATHINFO_EXTENSION);
      $file_name = $boardgame->get_bid().$file_type;
      move_uploaded_file($_FILES['image-file']['tmp_name'], __STATIC__.'img'.DL.$file_name);
      $boardgame->set_img($file_name);
    }
    $boardgame = $this->boardgame_service->save_boardgame($boardgame);
    return 'Location: /product-detail?id='.$boardgame->get_bid();
  }

  #[RequestMapping(value: '/delete-product', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['ADMIN'])]
  function post_delete_product()
  {
    $boardgame = form_model('Boardgame');
    if (file_exists(__STATIC__.'img'.DL.$boardgame->get_img()))
      unlink(__STATIC__.'img'.DL.$boardgame->get_img());

    $bid = $boardgame->get_bid();
    $comments = $this->comment_service->get_comment_by_bid($bid);
    foreach($comments as $comment)
      $this->comment_service->delete_comment($comment);
    $favourites = $this->favourite_service->get_favourite_by_bid($bid);
    foreach($favourites as $favourite)
      $this->favourite_service->delete_favourite($favourite);
    $boardgame = $this->boardgame_service->delete_boardgame($boardgame);
    return 'Location: /product-list';
  }

  #[RequestMapping(value: '/edit-comment', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['ADMIN'])]
  function post_edit_comment()
  {
    $edited_cmt = form_model('Comment');
    $edited_cmt = $this->comment_service->save_comment($edited_cmt);
    return 'Location: /product-detail?id='.$edited_cmt->get_bid();
  }
  
  #[RequestMapping(value: '/delete-comment', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['ADMIN'])]
  function post_delete_comment()
  {
    $deleted_cmt = form_model('Comment');
    $bid = $deleted_cmt->get_bid();
    $deleted_cmt = $this->comment_service->delete_comment($deleted_cmt);
    return 'Location: /product-detail?id='.$bid;
  }
}

?>
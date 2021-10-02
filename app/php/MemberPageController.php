<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, EnableSecurity, Autowired, Model};

#[Controller]
class MemberPageController
{
  #[Autowired]
  private MemberService $member_service; 
  #[Autowired]
  private CommentService $comment_service; 
  #[Autowired]
  private FavouriteService $favourite_service; 

  #[RequestMapping(value: '/member-info', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['MEMBER'])]
  function get_member_info()
  {
    $user = $_SESSION['USER'];
    $member = $this->member_service->get_member_by_uid($user->get_uid())[0];
    $member = [
      'mid' => $member->get_mid(),
      'username' => $user->get_username(),
      'email' => $member->get_email(),
      'phone' => $member->get_phone(),
      'address' => $member->get_address(),
      'birth' => $member->get_birth(),
      'img' => $member->get_img()
    ];
    include __TEMPLATE__.'member-info.php';
  }
  #[RequestMapping(value: '/save-info', method: RequestMethod::POST)]
  #[EnableSecurity(role:['MEMBER', 'ADMIN'])]
  function post_save_info()
  {
    $member = form_model('Member');
    $member = $this->member_service->save_member($member);
    if ($member->get_user()->get_role() == 'ROLE_ADMIN')
    {
      header('Location: /member-info/'.$member->get_mid());
      exit();
    }
    header('Location: /member-info');
    exit();
  }
  #[RequestMapping(value: '/add-favourite', method: RequestMethod::POST)]
  #[EnableSecurity(role:['MEMBER'])]
  function post_add_favourite()
  {
    $new_favourite = form_model('Favourite');
    $new_favourite = $this->favourite_service->save_favourite($new_favourite);
    header('Location: /product-detail?id='.$new_favourite->get_bid());
    exit();
  }
  #[RequestMapping(value: '/delete-favourite', method: RequestMethod::POST)]
  #[EnableSecurity(role:['MEMBER'])]
  function post_delete_favourite()
  {
    $deleted_favourite = form_model('Favourite');
    $bid = $deleted_favourite->get_bid();
    $this->favourite_service->delete_favourite($deleted_favourite);
    header('Location: /product-detail?id='.$bid);
    exit();
  }
  #[RequestMapping(value: '/add-comment', method: RequestMethod::POST)]
  #[EnableSecurity(role:['MEMBER'])]
  function post_save_comment()
  {
    $new_comment = form_model('Comment');
    $new_comment = $this->comment_service->save_comment($new_comment);
    header('Location: /product-detail?id='.$new_comment->get_bid());
    exit();
  }
}

?>
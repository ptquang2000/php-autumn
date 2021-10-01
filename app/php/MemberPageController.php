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
  #[EnableSecurity(role: ['MEMBER', 'ADMIN'])]
  function get_main(Model $model)
  {
    include __TEMPLATE__.'member-info.php';
  }
  #[RequestMapping(value: '/save-info', method: RequestMethod::POST)]
  function post_save_info()
  {
    include __TEMPLATE__.'member-info.php';
  }
  #[RequestMapping(value: '/save-favourite', method: RequestMethod::POST)]
  function post_save_favourite()
  {
    include __TEMPLATE__.'product-detail.php';
  }
  #[RequestMapping(value: '/save-comment', method: RequestMethod::POST)]
  function post_save_comment()
  {
    include __TEMPLATE__.'product-detail.php';
  }
}

?>
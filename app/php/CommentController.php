<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod, Autowired};

#[RestController]
class CommentController 
{
  #[Autowired]
  private CommentService $comment_service; 

  #[Autowired]
  private MemberService $member_service; 

  #[RequestMapping(value: '/comment/$bid', method: RequestMethod::GET)]
  public function get_comment($bid)
  {
    $comments = array_map([$this, 'load_comments'], $this->comment_service->get_comment_by_bid($bid));
    return $comments;
  }

  #[RequestMapping(value: '/add-comment', method: RequestMethod::POST)]
  public function post_save_comment()
  {
    $new_comment = form_model('Comment');
    if (isset($_SESSION['USER']) && $_SESSION['USER']->get_authority() == 'ADMIN')
    {
      $mid = $this->member_service->get_member_by_uid(
        $_SESSION['USER']->get_uid())[0]->get_mid();
      $new_comment->set_mid($mid);
    }
    $this->comment_service->save_comment($new_comment);
    $comments = array_map([$this, 'load_comments'], $this->comment_service->get_comment_by_bid($new_comment->get_bid()));
    return $comments;
  }

  #[RequestMapping(value: '/edit-comment', method: RequestMethod::POST)]
  public function post_edit_comment()
  {
    $edited_cmt = form_model('Comment');
    $this->comment_service->save_comment($edited_cmt);
    $comments = array_map([$this, 'load_comments'], $this->comment_service->get_comment_by_bid($edited_cmt->get_bid()));
    return $comments;
  }
  
  #[RequestMapping(value: '/delete-comment', method: RequestMethod::POST)]
  function post_delete_comment()
  {
    $deleted_cmt = form_model('Comment');
    $this->comment_service->delete_comment($deleted_cmt);
    $comments = array_map([$this, 'load_comments'], $this->comment_service->get_comment_by_bid($deleted_cmt->get_bid()));
    return $comments;
  }

  private function load_comments($cmt){
    $object = new \stdClass();
    $object->bid = $cmt->get_bid();
    $object->content = $cmt->get_content();
    $object->cid = $cmt->get_cid();
    $object->username = $cmt->get_member()->get_user()->get_username();
    $object->mid = $cmt->get_mid();
    return $object;
  }
}

?>
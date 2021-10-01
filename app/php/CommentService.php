<?php

namespace App\PHP;
use Core\{Service, Autowired};
use App\PHP\{CommentRepository, Comment};

#[Service]
class CommentService
{
  #[Autowired]
  private CommentRepository $comment_repository;

  public function get_all_comments()
  {
    return $this->comment_repository->find_all();
  }

  public function get_comment($id)
  {
    return $this->comment_repository->find_by_id($id);
  }

  public function get_comment_by_bid($bid)
  {
    return $this->comment_repository->find_by_bid($bid);
  }

  public function delete_comment(Comment $comment)
  {
    return $this->comment_repository->delete($comment);
  }

  public function save_comment(Comment $comment)
  {
    return $this->comment_repository->save($comment);
  }
}

?>
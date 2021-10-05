<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Autowired, HttpException, Model};

#[Controller]
class CommonPageController
{
  #[Autowired]
  private BoardgameService $boardgame_service; 
  #[Autowired]
  private CommentService $comment_service; 
  #[Autowired]
  private FavouriteService $favourite_service; 
  #[Autowired]
  private MemberService $member_service; 

  #[RequestMapping(value: '/', method: RequestMethod::GET)]
  function get_main()
  {
    return 'index.html';
  }

  #[RequestMapping(value: '/product-list', method: RequestMethod::GET)]
  function get_product_list(Model $model)
  {
    $alph = $_GET['name'] ?? null;
    $price = $_GET['price'] ?? null;
    $level = $_GET['level'] ?? null;
    if (isset($_GET['search']))
      $model->add_attribute(
      'boardgames', 
      $this->boardgame_service->get_boardgames_by_search(
        $_GET['search'], $alph, $price, $level)
      );
    else 
      $model->add_attribute(
      'boardgames', 
      $this->boardgame_service->get_all_boardgames($alph, $price, $level)
      );
    return 'product-list.php';
  }

  #[RequestMapping(value: '/product-detail', method: RequestMethod::GET)]
  function get_product_detail(Model $model)
  {
    if (!isset($_GET['id'])) 
      throw new HttpException('404');

    $id = $_GET['id'];
    $boardgame = $this->boardgame_service->get_boardgame($id);
    if (!isset($boardgame))
      throw new HttpException('404');

    $model->add_attribute('boardgame', $boardgame);

    $comments = $this->comment_service->get_comment_by_bid($id);
    $comments = array_map([$this, 'match_member_comment'], $comments);
    $model->add_attribute('comments', $comments);

    if (isset($_SESSION['USER']))
    {
      $mid = $this->member_service->get_member_by_uid(
        $_SESSION['USER']->get_uid())[0]->get_mid();
      $model->add_attribute('mid', $mid);
      $fid = $this->favourite_service->get_favourite_by_mid($mid);
      $model->add_attribute('fid', $fid);
    }
    return 'product-detail.php';
  }

  private function match_member_comment($comment)
  {
    return [
      'cid' => $comment->get_cid(),
      'username'=>$this->member_service->get_member($comment->get_mid())->get_user()->get_username(),
      'content'=>$comment->get_content()
    ];
  }

  #[RequestMapping(value: '/news', method: RequestMethod::GET)]
  function get_news()
  {
    return 'news.php';
  }
}

?>
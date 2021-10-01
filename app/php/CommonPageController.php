<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Autowired, Model};

#[Controller]
class CommonPageController
{
  #[Autowired]
  private BoardgameService $boardgame_service; 
  #[Autowired]
  private CommentService $comment_service; 

  #[RequestMapping(value: '/', method: RequestMethod::GET)]
  function get_main()
  {
    return 'index.html';
  }

  #[RequestMapping(value: '/product-list', method: RequestMethod::GET)]
  function get_product_list(Model $model)
  {
    $alph = $_GET['alph'] ?? null;
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
    include __TEMPLATE__.'product-list.php';
  }

  #[RequestMapping(value: '/product-detail', method: RequestMethod::GET)]
  function get_product_detail(Model $model)
  {
    if (!isset($_GET['id'])) 
      # raise exception 404
      return; 
    $id = $_GET['id'];
    $boardgame = $this->boardgame_service->get_boardgame($id);
    $model->add_attribute('boardgame', $boardgame);
    $comments = $this->comment_service->get_comment_by_bid($id);
    $model->add_attribute('comments', $comments);
    include __TEMPLATE__.'product-detail.php';
  }

  #[RequestMapping(value: '/news', method: RequestMethod::GET)]
  function get_news()
  {
    include __TEMPLATE__.'news.php';
  }
}

?>
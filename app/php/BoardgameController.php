<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod, Autowired};

#[RestController]
class BoardgameController 
{
  #[Autowired]
  private BoardgameService $boardgame_service; 

  #[RequestMapping(value: '/boardgame/$id', method: RequestMethod::GET)]
  public function get_boardgame($id)
  {
    return $this->boardgame_service->get_boardgame($id);
  }
  #[RequestMapping(value: '/boardgames', method: RequestMethod::GET)]
  public function get_boardgames()
  {
    $alph = $_GET['name'] ?? null;
    $price = $_GET['price'] ?? null;
    $level = $_GET['level'] ?? null;
    if (isset($_GET['search']))
      return $this->boardgame_service->get_boardgames_by_search(
        $_GET['search'], $alph, $price, $level);
    else 
      return $this->boardgame_service->get_all_boardgames($alph, $price, $level);
  }
  #[RequestMapping(value: '/save-boardgame', method: RequestMethod::POST)]
  public function post_save_boardgames()
  {
    $obj = new \stdClass();
    $obj->error = false;
    $boardgame = form_model('Boardgame');
    try{
      if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name']) &&
      getimagesize($_FILES['image']['tmp_name']))
      {
        $obj = $this->boardgame_service->save_boardgame($boardgame);
        $file_type = '.'.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = $obj->get_bid().$file_type;
        $obj->set_img($file_name);
        $obj = $this->boardgame_service->save_boardgame($obj);
        move_uploaded_file($_FILES['image']['tmp_name'], __STATIC__.'img'.DL.$file_name);
      }else throw new \Exception('Missing boardgame image');
    }catch (\Exception $e)
    {
      $obj->error = $e->getMessage();
    }
    return $obj;
  }
}

?>
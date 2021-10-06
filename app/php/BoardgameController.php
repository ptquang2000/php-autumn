<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod, Autowired, EnableSecurity};

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
}

?>
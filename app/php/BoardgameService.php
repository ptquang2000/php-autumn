<?php

namespace App\PHP;
use Core\{Service, Autowired};
use App\PHP\{BoardgameRepository, Boardgame};

#[Service]
class BoardgameService
{
  #[Autowired]
  private BoardgameRepository $boardgame_repository;

  public function get_all_boardgames($aplh, $price, $level)
  {
    $boardgames = $this->boardgame_repository->find_all();
    return $this->filter_boardgames($boardgames, $aplh, $price, $level);
  }

  public function get_boardgames_by_search($search, $aplh, $price, $level)
  {
    $boardgames = $this->boardgame_repository->find_by_name_like("%{$search}%" , 1);
    return $this->filter_boardgames($boardgames, $aplh, $price, $level);
  }

  private function filter_boardgames($boardgames, $aplh, $price, $level)
  {
    if (isset($level))
      $boardgames = array_filter($boardgames, 
        function($val) use ($level)
        {
          return $val->get_level() == $level;
        });
    if (isset($aplh))
      uasort($boardgames, 
        function($a, $b) use ($aplh)
        {
          if ($a->get_name() == $b->get_name()) return 0;
          if ($aplh == 'asc')
            return $a->get_name() < $b->get_name() ? -1 : 1;
          if ($aplh == 'desc')
            return $a->get_name() < $b->get_name() ? 1 : -1;
          return 0;
        });
    if (isset($price))
      uasort($boardgames, 
        function($a, $b) use ($price)
        {
          if ($a->get_price() == $b->get_price()) return 0;
          if ($price == 'asc')
            return $a->get_price() < $b->get_price() ? -1 : 1;
          if ($price == 'desc')
            return $a->get_price() < $b->get_price() ? 1 : -1;
          return 0;
        });
    return array_values($boardgames);
  }

  public function get_boardgame($id)
  {
    return $this->boardgame_repository->find_by_id($id);
  }

  public function get_boardgame_by_name($name)
  {
    return $this->boardgame_repository->find_by_name($name);
  }

  public function delete_boardgame($id)
  {
    return $this->boardgame_repository->delete($id);
  }

  public function save_boardgame(Boardgame $boardgame)
  {
    return $this->boardgame_repository->save($boardgame);
  }
}

?>
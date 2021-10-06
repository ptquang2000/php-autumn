<?php

namespace App\PHP;
use Core\{Service, Autowired};
use App\PHP\{FavouriteRepository, Favourite};

#[Service]
class FavouriteService
{
  #[Autowired]
  private FavouriteRepository $favourite_repository;

  public function get_all_favourites()
  {
    return $this->favourite_repository->find_all();
  }

  public function get_favourite($fid)
  {
    return $this->favourite_repository->find_by_id($fid);
  }

  public function get_favourite_by_mid($mid)
  {
    return $this->favourite_repository->find_by_mid($mid);
  }

  public function get_favourite_by_member($mid, $bid)
  {
    return $this->favourite_repository->find_by_mid_and_bid($mid, $bid);
  }

  public function get_favourite_by_bid($mid)
  {
    return $this->favourite_repository->find_by_bid($mid);
  }

  public function delete_favourite($id)
  {
    return $this->favourite_repository->delete($id);
  }

  public function save_favourite(Favourite $favourite)
  {
    return $this->favourite_repository->save($favourite);
  }
}

?>
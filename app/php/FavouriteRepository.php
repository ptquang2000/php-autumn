<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'Favourite')]
interface FavouriteRepository extends IRepository
{
  public function find_by_mid($id);
}

?>
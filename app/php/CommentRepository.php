<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'Comment')]
interface CommentRepository extends IRepository
{
  public function find_by_bid($id);
}

?>
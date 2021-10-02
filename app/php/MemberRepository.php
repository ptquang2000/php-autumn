<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'Member')]
interface MemberRepository extends IRepository
{
  public function find_by_uid($uid);
}

?>
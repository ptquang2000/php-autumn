<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'member')]
interface MemberRepository extends IRepository
{
}

?>
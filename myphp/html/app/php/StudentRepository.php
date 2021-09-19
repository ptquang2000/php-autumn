<?php

namespace App\PHP;

use Core\{IRepository, Entity};


#[Entity(class:'Student')]
interface StudentRepository extends IRepository
{

}

?>
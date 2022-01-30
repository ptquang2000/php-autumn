<?php

namespace App\PHP;
use Core\{IRepository, Entity};


#[Entity(class:'Employee')]
interface EmployeeRepository extends IRepository
{
} 

?>
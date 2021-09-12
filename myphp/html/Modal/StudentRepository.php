<?php

use Core\Attributes\Entity;
use Core\Repository\IRepository;

#[Entity(class:'Student')]
interface StudentRepository extends IRepository
{
}

?>
<?php

use Core\Attributes\Entity;
use Core\Repository\IRepository;

#[Entity(entity:'Student')]
interface StudentRepository extends IRepository
{
}

?>
<?php

use Core\Attributes\Entity;
use Core\Repository\IRepository;

#[Entity(entity:'Course')]
interface CourseRepository extends IRepository
{
}

?>
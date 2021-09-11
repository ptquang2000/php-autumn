<?php

use Core\Attributes\Service;
use Core\Service\ModalService;

#[Service]
class CourseService
{
  use ModalService;

  private CourseRepository $repository;

  public function get_all_courses()
  {
    return $this->repository->find_all();
  }
  public function get_course(Course $course)
  {
    return $this->repository->find_by_id($course);
  }
  public function save_course(Course $course)
  {
    return $this->repository->save($course);
  }
  public function delete_course(Course $course)
  {
    return $this->repository->delete($course);
  }
}

?>

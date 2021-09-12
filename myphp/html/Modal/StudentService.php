<?php

use Core\Service\Service;

class StudentService
{
  use Service;

  private StudentRepository $repository;

  public function get_all_students()
  {
    return $this->repository->find_all();
  }
  public function get_student_by_id(Student $student)
  {
    return $this->repository->find_by_id($student);
  }
  public function save_student(Student $student)
  {
    return $this->repository->save($student);
  }
  public function delete_student(Student $student)
  {
    return $this->repository->delete($student);
  }
}

?>
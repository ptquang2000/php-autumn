<?php

namespace App\PHP;

// use Core\ServiceTrait;
use Core\Service;
use Core\Autowired;

#[Service]
class StudentService
{
  // use ServiceTrait;

  // private StudentRepository $repository;

  #[Autowired]
  private StudentRepository $repository;

  public function get_all_students()
  {
    return $this->repository->find_all();
  }
  public function get_student_by_id($id)
  {
    return $this->repository->find_by_id($id);
  }
  public function save_student(Student $student)
  {
    return $this->repository->save($student);
  }
  public function delete_student(Student $student)
  {
    return $this->repository->delete($student);
  }
  public function count_students($fields)
  {
    return $this->repository->count($fields);
  }
  public function get_student_1($name)
  {
    return $this->repository->find_by_name($name);
  }
  public function get_student_2($name, $major)
  {
    return $this->repository->find_by_name_and_major($name, $major);
  }
  public function get_student_3($name, $major)
  {
    return $this->repository->find_by_name_or_major($name, $major);
  }
}

?>
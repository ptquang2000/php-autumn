<?php

namespace App\PHP;

use Core\{Service, Autowired};
use App\PHP\StudentRepository;

#[Service]
class StudentService
{

  #[Autowired]
  private StudentRepository $test_repository;

  public function test_get_all()
  {
    return $this->test_repository->find_all();
  }

  public function test_get_by_id($id)
  {
    return $this->test_repository->find_by_id($id);
  }

  public function test_get_by_prop_name($name)
  {
    return $this->test_repository->find_by_name($name);
  }

  public function test_get_by_prop_year($year)
  {
    return $this->test_repository->find_by_year($year);
  }
  
  public function test_get_by_prop_course_id($id)
  {
    return $this->test_repository->find_by_course_id($id);
  }

  public function test_get_by_props($id, $course_id)
  {
    return $this->test_repository->find_by_name_and_course_id($id, $course_id);
  }

  public function test_count()
  {

  }

  public function test_delete($student)
  {
    return $this->test_repository->delete($student);
  }

  public function test_save($student)
  {
    return $this->test_repository->save($student);
  }
}

?>
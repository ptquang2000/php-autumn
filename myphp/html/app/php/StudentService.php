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

  public function test_get_by_prop_name()
  {
    return $this->test_repository->find_by_name($name);
  }

  public function get_by_prop_year()
  {
    return $this->test_repository->find_by_year($year);
  }

  public function test_count()
  {

  }

  public function test_delete()
  {

  }

  public function test_save()
  {

  }
}

?>
<?php

namespace App\PHP;

use Core\{Service, Autowired};

#[Service]
class MajorService
{
  #[Autowired]
  private MajorRepository $repository;

  public function get_all_major()
  {
    return $this->repository->find_all();
  }
  public function get_major(Major $course)
  {
    return $this->repository->find_by_id($course);
  }
  public function save_major(Major $course)
  {
    return $this->repository->save($course);
  }
  public function delete_major(Major $course)
  {
    return $this->repository->delete($course);
  }
}

?>

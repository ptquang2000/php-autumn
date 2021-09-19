<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod, Autowired};
use App\PHP\StudentService;

#[RestController]
class StudentController 
{
  #[Autowired]
  private StudentService $test_service; 

  #[RequestMapping(value: '/student', method:RequestMethod::GET)]
  public function get_all()
  {
    return $this->test_service->test_get_all();
  }
  #[RequestMapping(value: '/student/name/$name', method:RequestMethod::GET)]
  public function get_name($name)
  {
    return $this->test_service->test_get_by_prop_name($name);
  }
  #[RequestMapping(value: '/student/year/$year', method:RequestMethod::GET)]
  public function get_year($year)
  {
    return $this->test_service->test_get_by_prop_year();
  }
  #[RequestMapping(value: '/student/id/$id', method:RequestMethod::GET)]
  public function get_id($id)
  {
    return $this->test_service->test_get_by_id($id);
  }


}

?>
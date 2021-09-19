<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod, Autowired};
use App\PHP\CourseService;

#[RestController]
class CourseController 
{
  #[Autowired]
  private CourseService $test_service; 

  #[RequestMapping(value: '/course', method:RequestMethod::GET)]
  public function get_all()
  {
    return $this->test_service->test_get_all();
  }
  #[RequestMapping(value: '/course/name/${name}', method:RequestMethod::GET)]
  public function get_name($name)
  {
    return $this->test_service->test_get_by_prop_name($name);
  }
  #[RequestMapping(value: '/course/id/${id}', method:RequestMethod::GET)]
  public function get_id($id)
  {
    return $this->test_service->test_get_by_id();
  }


}

?>
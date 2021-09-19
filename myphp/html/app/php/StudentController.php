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
    return $this->test_service->test_get_by_prop_year($year);
  }
  #[RequestMapping(value: '/student/id/$id', method:RequestMethod::GET)]
  public function get_id($id)
  {
    return $this->test_service->test_get_by_id($id);
  }
  #[RequestMapping(value: '/student/course/$id', method:RequestMethod::GET)]
  public function get_course_id($id)
  {
    return $this->test_service->test_get_by_prop_course_id($id);
  }
  #[RequestMapping(value: '/student/name/$id/course/$course_id', method:RequestMethod::GET)]
  public function get_id_course_id($id, $course_id)
  {
    return $this->test_service->test_get_by_props($id, $course_id);
  }
  #[RequestMapping(value: '/student/$action', method:Requestmethod::POST)]
  public function post_student(Student $student, $action)
  {
    if ($action == 'save')
      return $this->test_service->test_save($student);
    else if ($action == 'delete')
      return $this->test_service->test_delete($student);
  }


}

?>
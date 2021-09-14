<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod};
use App\PHP\StudentService;
use Core\Autowired;

#[RestController]
class TestController
{
    #[Autowired]
    private StudentService $student_service;

    #[RequestMapping(value:'/' , method:RequestMethod::GET)]
    public function get1()
    {   
        $a = $this->student_service->get_all_students();
        return $a;
    }
    // #[RequestMapping(value:'/a' , method:'GET')]
    // public function get2()
    // {
    //     echo "GET2";
    // }
    #[RequestMapping(value:'/$a' , method:RequestMethod::GET)]
    public function get3($a)
    {
        $a = $this->student_service->get_student_by_id($a);
        return $a;
    }
    // #[RequestMapping(value:'/' , method:'GET')]
    // public function get4($a)
    // {
    //     echo "GET4 $a";
    // }
    // #[RequestMapping(value:'/$a' , method:'GET')]
    // public function get5($a, $b)
    // {
    //     echo "GET5 $a";
    // }
    // #[RequestMapping(value:'/a/b' , method:'GET')]
    // public function get6_1()
    // {
    //     echo "GET6-1";
    // }
    // #[RequestMapping(value:'/$a/$b' , method:'GET')]
    // public function get6_2($a, $b)
    // {
    //     echo "GET6_2 $a $b";
    // }
    // #[RequestMapping(value:'/$b/$a' , method:'GET')]
    // public function get7($a, $b)
    // {
    //     echo "GET7 $a";
    // }
    // #[RequestMapping(value:'/a/$a' , method:'GET')]
    // public function get8($a)
    // {
    //     echo "GET8 $a";
    // }
    // #[RequestMapping(value:'/$a/a' , method:'GET')]
    // public function get9($a)
    // {
    //     echo "GET9 $a";
    // }
    // #[RequestMapping(value:'/a/$a/b' , method:'GET')]
    // public function get10_1($a)
    // {
    //     echo "GET10_1 $a";
    // }
    // #[RequestMapping(value:'/a/b/$a' , method:'GET')]
    // public function get10_2($a)
    // {
    //     echo "GET10_2 $a";
    // }
    // #[RequestMapping(value:'/a/b/$a' , method:'GET')]
    // public function get10_3($a)
    // {
    //     echo "GET10_3 $a";
    // }
    // #[RequestMapping(value:'/a/$a/$b' , method:'GET')]
    // public function get11_1($a, $b)
    // {
    //     echo "GET11_1 $a, $b";
    // }
    // #[RequestMapping(value:'/$a/a/$b' , method:'GET')]
    // public function get11_2($a, $b)
    // {
    //     echo "GET11_2 $a, $b";
    // }
    // #[RequestMapping(value:'/$a/$b/a' , method:'GET')]
    // public function get11_3($a, $b)
    // {
    //     echo "GET11_3 $a, $b";
    // }
}
?>
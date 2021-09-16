<?php

namespace App\PHP;

use Core\{Controller, RequestMapping, RequestMethod};

#[Controller]
class TestController
{
    #[RequestMapping(value:'/' , method:RequestMethod::GET)]
    public function get1()
    {   
		$this->model->add_attribute('courses' , [new class {
			public $id = 1;
			public $name = 'joshua';
		},new class {
			public $id = 2;
			public $name = 'giorino giovana';
		},new class {
			public $id = 3;
			public $name = 'jotaro';
		}]);
        return 'template.html';
    }
    // #[RequestMapping(value:'/a' , method:'GET')]
    // public function get2()
    // {
    //     echo "GET2";
    // }
    #[RequestMapping(value:'/$a' , method:RequestMethod::GET)]
    public function get3($a)
    {
        return 'index.html';
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
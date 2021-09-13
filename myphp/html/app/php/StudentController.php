<?php

namespace App\PHP;

use Core\{RestController, RequestMapping};

#[RestController]
class StudentController
{
    
    #[RequestMapping(value:'/get-students' , method:'GET')]
    public function getStudent()
    {
        echo 'GET STUDENT CONTROLLER';
    }
}
?>
<?php

include 'Core\Attributes.php';
include 'Core\Service.php';
include 'Core\Repository.php';
include 'Core\Database.php';

spl_autoload_register(function($class)
{
  include 'Modal\\'.$class.'.php';
});

$service = new StudentService();
// echo '</h3><h1>UPDATE ENTITY</h1><h3>';
// var_dump($obj=$service->save_student(
//   Student::StudentInludeRela('Giorno Giorvarna', 1, 'Ecomerce', Course::CourseSufficent(1, 'Calculus 101'))));
// var_dump($service->save_student(
//   Student::StudentInludeRela('Johnathan Joestart', 1, 'Ecomerce', Course::CourseOnlyId(1))));
// echo '</h3>';
// echo '<h1>SELECT BY ID</h1><h3>';
// var_dump($service->get_student_by_id(Student::StudentById($obj->get_id())));
// echo '<h1>DELETE BY ID</h1><h3>';
// var_dump($service->delete_student(Student::StudentById($obj->get_id())));
// echo '</h3><h1>SELECT ALL</h1><h3>';
// var_dump($service->get_all_students());

$course_service = new CourseService();
echo '</h3><h1>SELECT BY ID</h1><h3>';
$obj = $course_service->get_course(Course::CourseOnlyId(1));
var_dump($obj);
echo '</h3><h1>SELECT ALL</h1><h3>';
$obj = $course_service->get_all_courses();
var_dump($obj);
echo '</h3>';
?>

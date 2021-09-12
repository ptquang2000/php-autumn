<?php
require 'Core/Attributes.php';
require 'Core/Repository.php';
require 'Core/Database.php';
require 'Core/Service.php';
spl_autoload_register(function($class)
{
  include 'Modal/'.$class.'.php';
});
echo '<h1>SELECT BY ID</h1>';
$student_s = new StudentService();
$major_s = new MajorService();
echo '</h3><h3>';
echo '<h1>SELECT ALL MAJOR</h1>';
$objs = $major_s->get_all_major();
foreach($objs as $obj)
{
  echo '</h3><h3>';
  var_dump($obj);
  echo '</h3>';
}
echo '<h1>SELECT ALL STUDENT</h1>';
$objs = $student_s->get_all_students();
foreach($objs as $obj)
{
  echo '</h3><h3>';
  var_dump($obj);
  echo '</h3>';
}
echo '<h1>SELECT DELETE MAJOR</h1>';
echo '<h3>';
$objs = $major_s->save_major(Major::Major(3, 'Economics'));
$student_s->save_student(Student::StudentWithNameMajor('Sasha', Major::MajorWithId(3)));
$student_s->save_student(Student::StudentWithNameMajor('Edie', Major::MajorWithId(3)));
$objs = $major_s->delete_major(Major::MajorWithId(3));
echo '</h3>';
?>

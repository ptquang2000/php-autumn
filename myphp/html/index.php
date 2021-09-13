<?php
require 'Core/Attributes.php';
require 'Core/Repository.php';
require 'Core/Database.php';
require 'Core/Service.php';
spl_autoload_register(function($class)
{
  include 'Modal/'.$class.'.php';
});
$student_s = new StudentService();
$major_s = new MajorService();
echo '<h1>select name</h1>';
echo '<h3>';
$objs = $student_s->get_student_1('Dio');
var_dump($objs);
echo '</h3>';
echo '<h1>select by name and major</h1>';
echo '<h3>';
$objs = $student_s->get_student_2('Dio', 1);
var_dump($objs);
echo '</h3>';
echo '<h1>select by name or major</h1>';
echo '<h3>';
$objs = $student_s->get_student_3('Dio', 2);
var_dump($objs);
echo '</h3>';
?>

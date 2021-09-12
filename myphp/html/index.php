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
echo '</h3><h3>';
echo '<h1>COUNT</h1>';
$objs = $student_s->count_students('index');
var_dump($objs);
echo '</h3><h3>';
echo '</h3>';
?>

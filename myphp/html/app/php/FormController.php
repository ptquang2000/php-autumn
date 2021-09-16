<?php

namespace App\PHP;

use Core\{Controller, RequestMethod, RequestMapping, Model};

#[Controller]
class FormController
{
  #[RequestMapping(value:'/', method:RequestMethod::GET)]
  public function getform()
  {
    // $model->add_attribute('name', 'getform');
    return 'form.html';
  }
  #[RequestMapping(value:'/form', method:RequestMethod::POST)]
  public function postform(Model $model)
  {
    foreach($_POST as $key => $value)
    {
      echo $key . ' : ';
      echo $value;
      echo '<br>';
    }
    $model->add_attribute('name', 'postform');
    return 'form.html';
  }
  #[RequestMapping(value:'/model/{$a}', method:RequestMethod::GET)]
  public function getmodelpathparam($a, Model $model)
  {
    $model->add_attribute('name', $a);
    return 'form.html';
  }
  #[RequestMapping(value:'/model/{$a}/check/{$b}', method:RequestMethod::GET)]
  public function getmodelcheck(Model $model,$a, $b)
  {
    $model->add_attribute('name', $a.$b);
    return 'form.html';
  }
  #[RequestMapping(value:'/model', method:RequestMethod::GET)]
  public function getmodel(Model $model)
  {
    $model->add_attribute('name', 'getmodel');
    return 'form.html';
  }
}

?>
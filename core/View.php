<?php 

namespace Core;

class View
{
  private $template;
  public Model $model;

  public function __construct($template, $model)
  {
    $this->template = $template;
    $this->model = $model;

    // set config
    $config = array_filter($GLOBALS['config'], function($key){
      return explode('.', $key)[0] == 'view';
    }, ARRAY_FILTER_USE_KEY);
    foreach($config as $key => $value)
      $this->model->add_attribute(explode('.', $key)[1], $value);
  }
  public function render() 
  {
    $redirect = explode(':', $this->template);
    if (count($redirect) == 2 && str_contains($redirect[0], 'Location'))
    {
      header($this->template);
      exit();
    }
    $attributes = $this->model->get_all_attributes();
    foreach($attributes as $key => $value)
    {
      $code = <<<EOF
      global \$$key;
      \$$key = \$value; 
      EOF;
      eval($code);
    }

    if (!empty($this->template) && file_exists(__STATIC__.$this->template))
      include __STATIC__.$this->template;
    else if (!empty($this->template) && file_exists(__TEMPLATE__.$this->template))
      include __TEMPLATE__.$this->template;
    else echo $this->template;
  }
}

?>
<?php

use Core\{OneToMany};

function dnd($data)
{
  echo '<pre>';
  var_dump($data);
  echo '</pre>';
  die;
}
function pr($data)
{
  echo '<pre>';
  var_dump($data);
  echo '</pre>';
}

function setup_reflection($name)
{
  $info=[
    'class'=>'',
    'table'=>'',
    'id'=>[
      'column'=>'',
      'name'=>''
    ],
    'props'=>[],
    'n-1'=>[],
    '1-n'=>[],
    '1-1'=>[],
  ];
  # Setup class information
  if (interface_exists($name))
    $info['class'] = 'App\\PHP\\'.(new ReflectionClass($name))->getAttributes()[0]->getArguments()['class']; 
  else 
    $info['class'] = 'App\\PHP\\'.$name;

  $class = new ReflectionClass($info['class']);
  $info['table'] = $class->getAttributes()[0]->getArguments()['name']; 
  foreach($class->getProperties() as $prop)
  {
    if (!($attr=$prop->getAttributes())) continue;
    if ($attr[0]->getName() == 'Core\ID')
      $info['id'] = [
        'column' => $attr[0]->getArguments()['name'],
        'name' => $prop->getName()
      ];
    else if ($attr[0]->getName() == 'Core\Column')
      $info['props'][] = [
        'column' => $attr[0]->getArguments()['name'],
        'name' => $prop->getName()
      ];
    else if ($attr[0]->getName() == 'Core\ManyToOne')
    {
      $mapby = setup_reflection($attr[0]->getArguments()['map_by']);
      $info['n-1'][$mapby['class']] = [
        'column' => $attr[0]->getArguments()['name'],
        'mapby' => $mapby,
        'name' => $prop->getName()
      ];
    }
    else if (str_contains($name, 'App\PHP') && $attr[0]->getName() == 'Core\OneToMany')
      $info['1-n'][] = [
        'mapby' => setup_reflection($attr[0]->getArguments()['map_by']),
        'cascade' => $attr[0]->getArguments()['cascade'] ?? OneToMany::DELETE,
        'name' => $prop->getName()
      ];
  }
  return $info;
}

?>
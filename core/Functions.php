<?php

use Core\{OneToMany, Router};

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

function form_model($model)
{
  $reflection = new \ReflectionClass(__APP__.$model);	
  $object = $reflection->newInstance();
  foreach($reflection->getProperties() as $prop)
    $object->{'set_'.$prop->getName()}(htmlspecialchars($_REQUEST[$prop->getName()] ?? null));
  return $object;
}

function autowired($class)
{
  // Repository Module
  if (interface_exists($class) && is_subclass_of($class, 'Core\IRepository'))
  {
    $interface = $class;

    # Process unimplement method
    $userMethod = array_diff(get_class_methods($interface), get_class_methods('Core\IRepository'));

    $unimp_methods = array();

    foreach ($userMethod as $method_name)
    {
      $method = str_replace('find_by_', '', $method_name);
      $case=count($kw=array_intersect(explode('_', $method), array_keys(FIND_BY)));
      if ($case == 0)
      {
        $unimp_methods[] = 
        <<<EOF
        public function $method_name ($$method)
        {
          return \$this->find_by_props(['$method' => $$method]);     
        }
        EOF;
      }
      else if ($case == 1)
      {
        $cond = implode("','",FIND_BY[$kw[array_key_first($kw)]]);
        $cols = explode('_'.$kw[array_key_first($kw)].'_', $method);
        if (count($cols) == 1)
          $cols = [explode('_', $method)[0]];
        $args = '$' . implode(', $', $cols);
        $fields = implode(', ', array_map(function($e){
          return "'$e'" . '=> $' .$e;
        }, $cols));
        $unimp_methods[] = 
        <<<EOF
        public function $method_name ($args)
        {
          return \$this->find_by_props([$fields], ['$cond']);
        }
        EOF;
      }
      else throw new \ErrorException("Invalid defined function in interface $interface");
    }
    $unimp_methods = implode(' ',$unimp_methods);

    $code =<<<EOF
    use Core\Repository;
    \$new_instance = new class extends Repository implements $interface {	$unimp_methods };
    EOF;
    eval($code);
    return $new_instance;
  }

  // Service Module
  $new_instance = new $class();
  $reflection = new \ReflectionClass($class);
  if ($reflection->getAttributes()[0]->getName() == 'Core\Service')
  {
    foreach($reflection->getProperties() as $prop) {
      if (!($attr=$prop->getAttributes()) 
      || $attr[0]->getName()!='Core\Autowired') continue;

      $reflection_prop =  $reflection->getProperty($prop->getName());
      $reflection_prop->setAccessible(true);
      $class_name = $prop->getType()->getName();
      $reflection_prop->setValue($new_instance, autowired($class_name));
    }
  }
  return $new_instance;
}

function rest_template($method, $url, $model)
{
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  $reflection = new \ReflectionClass(__APP__.$model);	
  $object = $reflection->newInstance();
  $output = curl_exec($curl);
  foreach($reflection->getProperties() as $prop)
    $object->{'set_'.$prop->getName()}(htmlspecialchars($output->{$prop->getName()} ?? null));
  curl_close($curl);
  return $object;
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
    else if ($attr[0]->getName() == 'Core\OneToOne')
    {
      $mapby = setup_reflection($attr[0]->getArguments()['map_by']);
      $info['1-1'][$mapby['class']] = [
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
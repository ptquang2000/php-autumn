<?php
namespace Core;

use Core\Database;
use Error;
use ReflectionClass;

class Repository 
{
  private $db;
  private $entity_name;
  private $entity_table;
  public $parent = NULL;
  private $_info;

  public function __construct()
  {
    $interface = (new ReflectionClass($this))->getInterfaceNames()[0];

    $class = 'App\\PHP\\'.(new ReflectionClass($interface))->getAttributes()[0]->getArguments()['class'];
    $this->entity_name = $class;

    $this->entity_table = (new ReflectionClass($class))->getAttributes()[0]->getArguments()['name'];

    $this->_info = setup_reflection($interface);

    $config = parse_ini_file('config.ini');

    $this->db = new Database(
      $config['sql.url'], 
      $config['sql.username'], 
      $config['sql.password'], 
      $config['sql.database']);
  }

  public function save($entity)
  {
    if (get_class($entity) != $this->entity_name) return null;

    $fields = array();
    $id_col = array();

    foreach((new ReflectionClass($this->entity_name))->getProperties() as $property)
    {
      $ref = $property->getAttributes()[0];
      try
      {
        if ($ref->getName() == 'Core\ID')
          $id_col[$ref->getArguments()['name']] = $entity->{'get_'.$property->name}();

        else if ($ref->getName() == 'Core\Column')
          $fields[$ref->getArguments()['name']] = $entity->{'get_'.$property->name}();

        else if ($ref->getName() == 'Core\ManyToOne')
          # Get relative info;
          foreach((new ReflectionClass($property->getType()->getName()))->getProperties() as $r_property)
            if ($r_property->getAttributes()[0]->getName() == 'Core\ID')
              $fields[$ref->getArguments()['name']] = $entity->{'get_'.$property->getName()}()
                ->{'get_'.$r_property->getName()}();
      }
      catch (Error $error)
      {
        if ($ref->getName() == 'Core\ID')
          $id_col[$ref->getArguments()['name']] = 0;
      }
    }
    if ($id_col[array_key_first($id_col)] != 0)
      $this->db->table($this->entity_table)->update(array_merge($fields, $id_col));
    else 
      $id_col[array_key_first($id_col)] = $this->db->table($this->entity_table)->insert($fields);

    $obj = $this->db->table($this->entity_table)->select_by_id($id_col);

    # insert enity including id fields if an entity had the id which didn't exist in db
    if (!$obj) 
      $id_col[array_key_first($id_col)] = $this->db->table($this->entity_table)->insert(array_merge($fields, $id_col));

    return $this->instantiate($this->db->table($this->entity_table)->select_by_id($id_col));
  }

  public function delete($entity)
  {
    if (get_class($entity) != $this->entity_name) return null;

    $id_col = array();

    $m_classes = array();

    foreach((new ReflectionClass($this->entity_name))->getProperties() as $property)
    {
      $attribute = $property->getAttributes()[0];
      if ($attribute->getName() == 'Core\ID')
        $id_col[$attribute->getArguments()['name']] = $entity->{'get_'.$property->name}();
      else if ($attribute->getName() == 'Core\OneToMany')
      {
        if (isset($attribute->getArguments()['cascade']))
          $m_classes[] = array_merge($attribute->getArguments(), ['property' => $property->getName()]);
      }
    }
    if ($m_classes)
    {
      $this->db->get_conn()->begin_transaction(); 
      try{
        foreach($m_classes as $m_class)
        {
          foreach (($reflection=new ReflectionClass($m_class['map_by']))->getProperties() as $property) {
            if ($property->getAttributes()[0]->getName() == 'Core\ID'
            && $m_class['cascade'] == 1)
            {
              foreach ($this->instantiate($this->db->table($this->entity_table)
              ->select_by_id($id_col))->{'get_'.$m_class['property']}() as $m_obj)
              
                  $this->db->table($reflection->getAttributes()[0]->getArguments()['name'])
                    ->delete([$property->getAttributes()[0]->getArguments()['name']
                      =>$m_obj->{'get_'.$property->getName()}()]);
              break;
            }
            else if ($property->getAttributes()[0]->getName() == 'Core\ManyToOne'
            && $m_class['cascade'] == 0
            && $property->getType()->getName() == $this->entity_name)
            {
              foreach ($this->instantiate($this->db->table($this->entity_table)
              ->select_by_id($id_col))->{'get_'.$m_class['property']}() as $m_obj)
              {
                foreach ($reflection->getProperties() as $prop) {
                  if ($prop->getAttributes()[0]->getName() == 'Core\ID')
                  {
                    $this->db->table($reflection->getAttributes()[0]->getArguments()['name'])
                      ->update([
                        $property->getAttributes()[0]->getArguments()['name']
                          =>null, #fk
                        $prop->getAttributes()[0]->getArguments()['name']
                          =>$m_obj->{'get_'.$prop->getName()}() #id
                      ]);
                    break;
                  }
                }
              }
              break;
            }
          }
        }
        $this->db->table($this->entity_table)->delete($id_col);
        $this->db->get_conn()->commit();
      }
      catch (mysqli_sql_exception $exception)
      {
        $this->db->get_conn()->rollback(); 
        throw $exception;
      }
    }
    else
      $this->db->table($this->entity_table)->delete($id_col);
  }

  public function find_all()
  {
    $objs = array();
    foreach($this->db->table($this->entity_table)->select_all() as $obj)
      $objs[] = $this->instantiate($obj);
    return $objs;
  }

  public function find_by_id($id)
  {
    $id_col = array();
    foreach((new ReflectionClass($this->entity_name))->getProperties() as $property)
    {
      $attribute = $property->getAttributes()[0];
      if ($attribute->getName() == 'Core\ID')
      {
        $id_col[$attribute->getArguments()['name']] = $id;
        break;
      }
    }
    $obj = $this->db->table($this->entity_table)->select_by_id($id_col);
    return $obj ? $this->instantiate($obj) : $obj;
  }

  public function find_by_props($fields, $cond=[null, '=']) {
    $objs = array();

    foreach($this->db->table($this->entity_table)->select_by_fields($fields, $cond) as $obj)
      $objs[] = $this->instantiate($obj);
    return $objs;

  }

  public function find_by_sql($sql="")
  {
    if (!$sql) return null;
    return $this->db->sql_query($sql);
  }

  public function count($fields = [])
  {
    return $this->db->table($this->entity_table)->count_by_fields($fields);
  }

  private function instantiate($obj, $info=null)
  {
    $info = $info ?? $this->_info;
    $entity = new $info['class']();

    // set id
    $value = $obj->{$info['id']['column']};
    $entity->{'set_'.$info['id']['name']}($value);
    // set other property
    foreach ($info['props'] as $prop)
    {
      $value = $obj->{$prop['column']};
      $entity->{'set_'.$prop['name']}($value);
    }
    // set n-1 reletionship
    foreach ($info['n-1'] as $prop)
    {
      $fk_id = $obj->{$prop['column']};
      if (!$fk_id) continue;
      $entity->{'set_'.$prop['name']}($this->instantiate(
        $this->db->table($prop['mapby']['table'])
        ->select_by_id([
              $prop['mapby']['id']['column'] => $fk_id
            ]),
        $prop['mapby']
      ));
    }
    // set 1-n relationship
    foreach ($info['1-n'] as $prop)
    {
      $r_entities = $this->db->table($prop['mapby']['table'])
        ->select_by_fields([
          $prop['mapby']['id']['column'] => $obj->{$prop['id']['name']}
      ]);
      $entity->{'set_'.$prop['name']}(array_walk(
        $r_entities,
        [$this, 'instantiate'],
        $prop['mapby']
      ));
    }
    return $entity;
  }
}

?>
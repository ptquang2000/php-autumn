<?php
namespace Core\Repository;

use Database;
use Error;
use ReflectionClass;

interface IRepository 
{
  public function save($entity);
  public function delete($entity);
  public function find_all();
  public function find_by_id($entity);
  public function find_by_props($prop, $cond=null);
  public function find_by_sql($sql="");
  public function count($entity);

  # find_by_$col1()
  # find_by_$col1_and_$col2()
  # find_by_$col1_or_$col2()
}

class Repository 
{
  private $db;
  private $entity_name;
  private $entity_table;
  public $parent = NULL;

  public function __construct()
  {
    $interface = (new ReflectionClass($this))->getInterfaceNames()[0];
    $class = (new ReflectionClass($interface))->getAttributes()[0]->getArguments()['class'];
    $this->entity_name = $class;
    $this->entity_table = (new ReflectionClass($class))->getAttributes()[0]->getArguments()['name'];

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
        if ($ref->getName() == 'Core\Attributes\ID')
          $id_col[$ref->getArguments()['name']] = $entity->{'get_'.$property->name}();

        else if ($ref->getName() == 'Core\Attributes\Column')
          $fields[$ref->getArguments()['name']] = $entity->{'get_'.$property->name}();

        else if ($ref->getName() == 'Core\Attributes\ManyToOne')
          # Get relative info;
          foreach((new ReflectionClass($property->getType()->getName()))->getProperties() as $r_property)
            if ($r_property->getAttributes()[0]->getName() == 'Core\Attributes\ID')
              $fields[$ref->getArguments()['name']] = $entity->{'get_'.$property->getName()}()
                ->{'get_'.$r_property->getName()}();
      }
      catch (Error $error)
      {
        if ($ref->getName() == 'Core\Attributes\ID')
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
      if ($attribute->getName() == 'Core\Attributes\ID')
        $id_col[$attribute->getArguments()['name']] = $entity->{'get_'.$property->name}();
      else if ($attribute->getName() == 'Core\Attributes\OneToMany')
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
            if ($property->getAttributes()[0]->getName() == 'Core\Attributes\ID'
            && $m_class['cascade'] == 1)
            {
              foreach ($this->instantiate($this->db->table($this->entity_table)
              ->select_by_id($id_col))->{'get_'.$m_class['property']}() as $m_obj)
              
                  $this->db->table($reflection->getAttributes()[0]->getArguments()['name'])
                    ->delete([$property->getAttributes()[0]->getArguments()['name']
                      =>$m_obj->{'get_'.$property->getName()}()]);
              break;
            }
            else if ($property->getAttributes()[0]->getName() == 'Core\Attributes\ManyToOne'
            && $m_class['cascade'] == 0
            && $property->getType()->getName() == $this->entity_name)
            {
              foreach ($this->instantiate($this->db->table($this->entity_table)
              ->select_by_id($id_col))->{'get_'.$m_class['property']}() as $m_obj)
              {
                foreach ($reflection->getProperties() as $prop) {
                  if ($prop->getAttributes()[0]->getName() == 'Core\Attributes\ID')
                  {
                    var_dump($this->db->table($reflection->getAttributes()[0]->getArguments()['name'])
                      ->update([
                        $property->getAttributes()[0]->getArguments()['name']
                          =>null, #fk
                        $prop->getAttributes()[0]->getArguments()['name']
                          =>$m_obj->{'get_'.$prop->getName()}() #id
                      ]));
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
      if ($attribute->getName() == 'Core\Attributes\ID')
      {
        $id_col[$attribute->getArguments()['name']] = $id;
        break;
      }
    }

    return $this->instantiate($this->db->table($this->entity_table)->select_by_id($id_col));
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

  private function instantiate($obj)
  {
    $entity = new $this->entity_name();

    foreach((new ReflectionClass($this->entity_name))->getProperties() as $property)
    {
      $attribute = $property->getAttributes()[0];
      if ($attribute->getName() == 'Core\Attributes\ID' || $attribute->getName() == 'Core\Attributes\Column')
      {
        $value = $obj->{$attribute->getArguments()['name']};
        // settype($value, $property->getType());
        $entity->{'set_'.$property->name}($value);
      }
      else if ($attribute->getName() == 'Core\Attributes\ManyToOne')
      {
        $fk_class = $property->getType()->getName();
        if ($fk_id=$obj->{$attribute->getArguments()['name']})
        {
          $ref_col_name = $attribute->getArguments()['ref_col_name'];
          $ref_table_name = (new ReflectionClass($fk_class))->getAttributes()[0]->getArguments()['name'];
          $entity->{'set_'.$property->name}(
            $this->instantiate_related(
              $fk_class, 
              $this->db->table($ref_table_name)
                ->select_by_id([$ref_col_name => $fk_id])
          ));
        }
      }
      else if ($attribute->getName() == 'Core\Attributes\OneToMany')
      {
        $m_class = $attribute->getArguments()['map_by'];
        $reflection = new ReflectionClass($m_class);
        $m_objs = array();
        foreach($reflection->getProperties() as $m_property)
          if ($m_property->getAttributes()[0]->getName() == 'Core\Attributes\ManyToOne')
          {
            $r_col = $m_property->getAttributes()[0]->getArguments()['name'];
            $r_col_name = $m_property->getAttributes()[0]->getArguments()['ref_col_name'];
            foreach(
            $this->db->table($reflection->getAttributes()[0]->getArguments()['name'])
              ->select_by_fields([$r_col => $obj->$r_col_name])
            as $m_obj)
              $m_objs[] = $this->instantiate_related($m_class, $m_obj);
            $entity->{'set_'.$property->getName()}($m_objs);
          }
      }
    }
    return $entity;
  }
  private function instantiate_related($class, $obj) 
  {
    $entity = new $class();
    foreach((new ReflectionClass($class))->getProperties() as $property)
    {
      $ref = $property->getAttributes()[0];
      if ($ref->getName() == 'Core\Attributes\ID' || $ref->getName() == 'Core\Attributes\Column')
      {
        $value = $obj->{$ref->getArguments()['name']};
        // settype($value, $property->getType());
        $entity->{'set_'.$property->name}($value);
      }
    }
    return $entity;
  }
}

?>
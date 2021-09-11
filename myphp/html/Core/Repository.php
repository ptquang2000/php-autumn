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
  public function find_by_prop($prop, $value);
  public function find_all_by_prop($prop, $value);
  public function find_by_sql($sql="");
  public function count($where = NULL);
}

trait Repository 
{
  private $db;
  private $entity_name;
  public $parent = NULL;

  public function __construct()
  {
    $interface = (new ReflectionClass($this))->getInterfaceNames()[0];
    $ref = (new ReflectionClass($interface))->getAttributes()[0]->getArguments();
    $this->entity_name = $ref['entity'];

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
      $this->db->table($this->entity_name)->update(array_merge($fields, $id_col));
    else 
      $id_col[array_key_first($id_col)] = $this->db->table($this->entity_name)->insert($fields);

    $obj = $this->db->table($this->entity_name)->select_by_id($id_col);

    # insert enity including id fields if an entity had the id which didn't exist in db
    if (!$obj) 
      $id_col[array_key_first($id_col)] = $this->db->table($this->entity_name)->insert(array_merge($fields, $id_col));

    return $this->instantiate($this->db->table($this->entity_name)->select_by_id($id_col));
  }

  public function delete($entity)
  {
    if (get_class($entity) != $this->entity_name) return null;

    $id_col = array();

    foreach((new ReflectionClass($this->entity_name))->getProperties() as $property)
    {
      $ref = $property->getAttributes()[0];
      if ($ref->getName() == 'Core\Attributes\ID')
        $id_col[$ref->getArguments()['name']] = $entity->{'get_'.$property->name}();
    }
    $this->db->table($this->entity_name)->delete($id_col);
  }

  public function find_all()
  {
    $objs = array();
    foreach($this->db->table($this->entity_name)->select_all() as $obj)
      $objs[] = $this->instantiate($obj);
    return $objs;
  }

  public function find_by_id($entity)
  {
    if (get_class($entity) != $this->entity_name) return null;

    $id_col = array();

    foreach((new ReflectionClass($this->entity_name))->getProperties() as $property)
    {
      $ref = $property->getAttributes()[0];
      if ($ref->getName() == 'Core\Attributes\ID')
        $id_col[$ref->getArguments()['name']] = $entity->{'get_'.$property->name}();
    }

    return $this->instantiate($this->db->table($this->entity_name)->select_by_id($id_col));
  }
  public function find_by_prop($prop, $value) {}
  public function find_all_by_prop($prop, $value) {}
  public function find_by_sql($sql="") {}
  public function count($where = NULL) {}

  private function instantiate($obj)
  {
    $entity = new $this->entity_name();
    foreach((new ReflectionClass($this->entity_name))->getProperties() as $property)
    {
      $ref = $property->getAttributes()[0];
      if ($ref->getName() == 'Core\Attributes\ID' || $ref->getName() == 'Core\Attributes\Column')
      {
        $value = $obj->{$ref->getArguments()['name']};
        settype($value, $property->getType());
        $entity->{'set_'.$property->name}($value);
      }
      else if ($ref->getName() == 'Core\Attributes\ManyToOne')
      {
        $r_class = $property->getType()->getName();

        $r_id = $obj->{$ref->getArguments()['name']};

        if ($r_id)
        {
          $r_col = $ref->getArguments()['ref_col_name'];
          $r_table = (new ReflectionClass($r_class))
            ->getAttributes()[0]->getArguments()[0];

          $r_obj = $this->db->table($r_table)->select_by_id([$r_col => $r_id]);
          $entity->{'set_'.$property->name}($this->instantiate_related($r_class, $r_obj));
        }
      }
      else if ($ref->getName() == 'Core\Attributes\OneToMany')
      {
        $m_entity = $ref->getArguments()['map_by'];
        $m_ref = new ReflectionClass($m_entity);
        $m_table = $m_ref->getAttributes()[0]->getArguments()[0];
        $m_objs = array();
        foreach($m_ref->getProperties() as $m_property)
        {
          if ($m_property->getAttributes()[0]->getName() == 'Core\Attributes\ManyToOne')
          {
            $r_col = $m_property->getAttributes()[0]->getArguments()['name'];
            $r_col_name = $m_property->getAttributes()[0]->getArguments()['ref_col_name'];
            foreach($this->db->table($m_table)
              ->select_by_fields([$r_col => $obj->$r_col_name]) as $m_obj)
            {
              $m = $this->instantiate_related($m_entity, $m_obj);
              $m->{'set_'.$m_property->getName()}($entity);
              $m_objs[] = $m;
            }
            $entity->{'set_'.$property->getName()}($m_objs);
          }
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
        settype($value, $property->getType());
        $entity->{'set_'.$property->name}($value);
      }
    }
    return $entity;
  }
}

?>
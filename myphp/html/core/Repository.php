<?php
namespace Core;

use Core\Database;
use Error;
use ReflectionClass;

class Repository 
{
  private $db;
  private $_info;

  public function __construct()
  {
    $interface = (new ReflectionClass($this))->getInterfaceNames()[0];
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
    if (get_class($entity) != $this->_info['class']) return null;

    $id_col = [
      $this->_info['id']['column'] 
        => $entity->{'get_'.$this->_info['id']['name']}() ?? null
    ];

    $fields = array_combine(
      array_map(function($item){
        return $item['column'];
      }, $this->_info['props']),
      array_map(function($item) use($entity){
        return $entity->{'get_'.$item['name']}();
      }, $this->_info['props'])
    );

    if ($id_col[array_key_first($id_col)])
      $this->db->table($this->_info['table'])->update(array_merge($fields, $id_col));
    else 
      $id_col[array_key_first($id_col)] = $this->db->table($this->_info['table'])->insert($fields);

    $obj = $this->db->table($this->_info['table'])->select_by_id($id_col);
    # insert enity including id fields if an entity had the id which didn't exist in db
    if (!$obj) 
      $id_col[array_key_first($id_col)] = $this->db->table($this->_info['table'])->insert(array_merge($fields, $id_col));

    return $this->instantiate($this->db->table($this->_info['table'])->select_by_id($id_col));
  }

  public function delete($entity)
  {
    $id_col = array();
    if (!is_scalar($entity) && get_class($entity) == $this->_info['class']) 
      $id_col[$this->_info['id']['column']] = $entity->
        {'get_'.$this->_info['id']['name']}() ?? null;
    else
      $id_col[$this->_info['id']['column']] = $entity;

    $this->db->get_conn()->begin_transaction(); 
    try{
      foreach($this->_info['1-n'] as $info)
      {
        $fk_entities = $this->instantiate(
          $this->db->table($this->_info['table'])
          ->select_by_id($id_col)
          )->{'get_'.$info['name']}();
        if ($info['cascade'] == OneToMany::DELETE)
          foreach($fk_entities as $fk_entity)
            $this->db->table($info['mapby']['table'])
            ->delete([$info['mapby']['id']['column'] 
              => $fk_entity->{'get_'.$info['mapby']['id']['name']}()
            ]);
        else if ($info['cascade'] == OneToMany::SETNULL)
          foreach($fk_entities as $fk_entity)
            $this->db->table($info['table'])
            ->update([
              $info['mapby']['n-1'][$this->_info['class']]['column']
                => null, #fk
              $info['mapby']['id']['column'] 
                => $fk_entity->{'get_'.$info['mapby']['id']['name']}() #id
            ]);
      }
      $this->db->table($this->_info['table'])->delete($id_col);
      $this->db->get_conn()->commit();
    }
    catch (mysqli_sql_exception $exception)
    {
      $this->db->get_conn()->rollback(); 
      throw $exception;
    }
  }

  public function find_all()
  {
    $objs = array();
    foreach($this->db->table($this->_info['table'])->select_all() as $obj)
      $objs[] = $this->instantiate($obj);
    return $objs;
  }

  public function find_by_id($id)
  {
    $id_col = [$this->_info['id']['column']=>$id];
    $obj = $this->db->table($this->_info['table'])->select_by_id($id_col);
    return $obj ? $this->instantiate($obj) : $obj;
  }

  public function find_by_props($fields, $cond=[null, '=']) {
    $objs = array();
    foreach($this->db->table($this->_info['table'])->select_by_fields($fields, $cond) as $obj)
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
    return $this->db->table($this->_info['table'])->count_by_fields($fields);
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
          $prop['mapby']['n-1'][$info['class']]['column'] => $obj->{$info['id']['name']}
      ], [null, '=']);
      foreach($r_entities as $key=>$value)
        $r_entities[$key] = $this->instantiate($value, $prop['mapby']);
      $entity->{'set_'.$prop['name']}($r_entities);
    }
    return $entity;
  }
}

?>
<?php

namespace Core;

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

?>
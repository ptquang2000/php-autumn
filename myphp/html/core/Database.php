<?php

namespace Core;
use mysqli;

class Query
{
  public function __construct($table, &$conn)
  {
    $this->table = $table;
    $this->conn = $conn;
  }

  public function select_all()
  {
    $stmt = 'SELECT * FROM ' . $this->table;
    $result =$this->conn->query($stmt); 

    $objs = [];
    while ($obj = $result->fetch_object())
      $objs[] = $obj; 
    return $objs;
  }

  public function select_by_fields($fields = [], $cond)
  {
    if (!$fields) throw new mysqli_sql_exception('Fields in where clause cannot be empty');
    # Sql query
    $query = 'SELECT * FROM ' . $this->table . ' WHERE ';
    $query .= implode($cond[1] .'? ' . $cond[0] . ' ',array_keys($fields)) . $cond[1].'?';

    # Prepare statement
    $stmt = $this->conn->prepare($query);
    $type = array_map(function($e){
      return gettype($e)[0];
    }, $fields);
    $stmt->bind_param(implode('', $type), ...array_values($fields));
    $stmt->execute();

    $objs = [];
    $result = $stmt->get_result();
    while ($obj = $result->fetch_object())
      $objs[] = $obj;
    return $objs;
  }

  public function count_by_fields($fields = [])
  {
    $query = 'SELECT COUNT(*) FROM ' . $this->table;
    if (!$fields) return (int)$this->conn->query($query)->fetch_array()[0];
    if (gettype($fields) == 'string') 
      return (int)$this->conn->query($query.' GROUP BY '.$fields)->fetch_array()[0];
    $query .= ' GROUP BY ' . implode(', ',array_keys($fields));

    # Prepare statement
    $stmt = $this->conn->prepare($query);
    $type = array_map(function($e){
      return gettype($e)[0];
    }, $fields);
    $stmt->bind_param(implode('', $type), ...array_values($fields));
    $stmt->execute();

    return $stmt->get_result()->fecth_array()[0];
  }

  public function select_by_id($id = [])
  {
    # Sql query
    $query = 'SELECT * FROM ' . $this->table; 
    $query .= ' WHERE ' . array_key_first($id) . ' = ?';

    # Prepare statement 
    $stmt = $this->conn->prepare($query);
    $type = gettype($id[array_key_first($id)])[0];

    $stmt->bind_param($type, $id[array_key_first($id)]);
    $stmt->execute();

    $obj = $stmt->get_result();
    return !$obj ? false : $obj->fetch_object();
  }

  public function insert($fields = []) # not include id col
  {
    # Sql query
    $query = 'INSERT INTO ' . $this->table;
    $query .= ' (' . implode(',',array_keys($fields)) . ')';
    $query .= ' VALUES ' . '(' . str_repeat('?,', count($fields) - 1) . '?)';

    # Prepare statement
    $stmt = $this->conn->prepare($query);
    $type = array_map(function($e){
      return gettype($e)[0];
    }, $fields);
    $stmt->bind_param(implode('', $type), ...array_values($fields));
    $stmt->execute();

    return $stmt->insert_id;
  }

  public function update($fields = []) # last fields -> id col
  {
    # Sql query
    $query = 'UPDATE ' . $this->table . ' SET ';
    foreach($fields as $key=>$val){
      $fields[$key . ' =?'] = $val;
      unset($fields[$key]);
    }
    $query .= implode(',', array_keys(array_slice($fields, 0, -1))) . ' WHERE ' . array_key_last($fields);

    # Prepare statement
    $stmt = $this->conn->prepare($query);
    $type = array_map(function($e){
      return $e ? gettype($e)[0] : 'b';
    }, $fields);
    $stmt->bind_param(implode('', $type), ...array_values($fields));
    $stmt->execute();

    return $stmt->affected_rows;
  }

  public function delete($id = []) # one fields only
  {
    # Sql query
    $query = 'DELETE FROM ' . $this->table; 
    $query .= ' WHERE ' . array_key_first($id) . ' = ?';

    # Prepare statement 
    $stmt = $this->conn->prepare($query);
    $type = gettype($id[array_key_first($id)])[0];

    $stmt->bind_param($type, $id[array_key_first($id)]);
    $stmt->execute();

    return $stmt->affected_rows;
  }

  private $table;
  private $conn;
}

class Database
{
  public function __construct($url, $usr, $pass, $db)
  {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $this->conn = new mysqli($url, $usr, $pass, $db);
    if ($this->conn->connect_errno)
      throw new RuntimeException('mysqli connection error: ' . $this->conn->connect_error);
  }

  public function table($tbl=null) { return new Query($tbl, $this->conn); }

  public function sql_query($stmt) {
    $this->db->get_conn()->query($sql);
    $result = $stmt->get_result();
    while ($obj = $result->fetch_object())
      $objs[] = $obj;
    return $objs;
  }

  public function get_conn() { return $this->conn;}

  private $conn;
}

?>
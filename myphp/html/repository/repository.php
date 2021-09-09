<?php
  interface IRepository{
    public function save($entity);
    public function findById($primary_key);
    public function findAll();
    public function count();
    public function delete($entity);
    public function existsById($primary_key);
  }

  class Repository implements IRepository{
    private $context;

    public function __construct($context){
      $this->context = $context;
    }

    public function save($entity){

    }

    public function findById($primary_key){

    }
    public function findAll(){

    }

    public function count(){

    }

    public function delete($entity){

    }
    
    public function existsById($primary_key){

    }
  }
?>
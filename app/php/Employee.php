<?php

namespace App\PHP;
use Core\{Table, ID, Column, OneToMany};

#[Table(name: 'employee')]
class Employee {

    #[ID(name: 'eid')]
    private $eid;
    #[Column(name: 'name')]
    private $name;

    #[OneToMany(map_by:'Customer')]
    private $customers;

    public static function newEmployee($name) {
        $employee = new Employee();
        $employee->set_name($name);
        return $employee;
    }

    public function set_eid($eid) {
        $this->eid = $eid;
    }
    public function get_eid() {
        return $this->eid;
    }
    public function set_name($name) {
        $this->name = $name;
    }
    public function get_name() {
        return $this->name;
    }
    public function set_customers($customers) {
        $this->customers = $customers;
    }
    public function get_customers() {
        return $this->customers;
    }
    public function to_string() {
        return "Employee [id: ".$this->eid.", name: ".$this->name."]";
    }

}

?>
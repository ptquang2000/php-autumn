<?php

namespace App\PHP;
use Core\{Table, ID, Column, ManyToOne};

#[Table(name: 'customer')]
class Customer {

    #[ID(name: 'cid')]
    private $cid;
    #[Column(name: 'name')]
    private $name;
    #[Column(name: 'eid')]
    private $eid;

    #[ManyToOne(name: 'eid', map_by:'Employee')]
    private $employee;

    public static function newCustomer($name, $eid) {
        $customer = new Customer();
        $customer->set_name($name);
        $customer->set_eid($eid);
        return $customer;
    }

    public function set_cid($cid) {
        $this->cid = $cid;
    }
    public function get_cid() {
        return $this->cid;
    }
    public function set_name($name) {
        $this->name = $name;
    }
    public function get_name() {
        return $this->name;
    }
    public function set_eid($eid) {
        $this->eid = $eid;
    }
    public function get_eid() {
        return $this->eid;
    }
    public function set_employee($employee) {
        $this->employee = $employee;
    }
    public function get_employee() {
        return $this->employee;
    }
    public function to_string() {
        return "Customer [id: ".$this->cid.", name: ".$this->name."]";
    }

}

?>
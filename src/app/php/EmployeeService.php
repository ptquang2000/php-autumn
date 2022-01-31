<?php

namespace App\PHP;
use Core\{Service, Autowired};
use App\PHP\{EmployeeRepository};

#[Service]
class EmployeeService {
    #[Autowired]
    private EmployeeRepository $employee_repository;

    public function find_all() {
        return $this->employee_repository->find_all();
    }
    public function find_one($id) {
        return $this->employee_repository->find_by_id($id);
    }
    public function add($employee) {
        return $this->employee_repository->save($employee);
    }
    public function update($employee, $id) {
        $employee->set_eid($id);
        return $this->employee_repository->save($employee);
    }
    public function delete($id){
        return $this->employee_repository->delete($id);
    }
}

?>
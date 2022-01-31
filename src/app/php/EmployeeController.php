<?php

namespace App\PHP;
use Core\{RestController, RequestMapping, Autowired, RequestMethod};
use App\PHP\{EmployeeService};

#[RestController]
class EmployeeController {
    #[Autowired]
    private EmployeeService $employee_service;

    #[RequestMapping(value: '/employees')]
    public function all() {
        return $this->employee_service->find_all();
    }
    #[RequestMapping(value: '/employees', method: RequestMethod::POST)]
    public function newEmployee(Employee $employee) {
        return $this->employee_service->add($employee);
    }
    #[RequestMapping(value: '/employees/$id')]
    public function one($id) {
        return $this->employee_service->find_one($id);
    }
    #[RequestMapping(value: '/employees/$id', method: RequestMethod::PUT)]
    public function replaceEmployee(Employee $employee, $id) {
        return $this->employee_service->update($employee, $id);
    }
    #[RequestMapping(value: '/employees/$id', method: RequestMethod::DELETE)]
    public function deleteEmployee($id) {
        $this->employee_service->delete($id);
    }

}

?>
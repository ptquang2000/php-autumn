<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, Autowired};
use App\PHP\{Customer, CustomerRepository, Employee, EmployeeRepository};

#[Controller]
class Demo {
    #[Autowired]
    private CustomerRepository $customer_repository;
    #[Autowired]
    private EmployeeRepository $employee_repository;

    #[RequestMapping(value: "/")]
    public function display_result() {
        $employee = $this->employee_repository->save(Employee::newEmployee("Charles"));
        $this->customer_repository->save(Customer::newCustomer("Caitlin", $employee->get_eid()));

        $employee = $this->employee_repository->save(Employee::newEmployee("Thomas"));
        $thomasID = $employee->get_eid();
        $this->customer_repository->save(Customer::newCustomer("Karie", $employee->get_eid()));
        $this->customer_repository->save(Customer::newCustomer("Jenna", $employee->get_eid()));
        
        $employee = $this->employee_repository->save(Employee::newEmployee("Francis"));
        $this->customer_repository->save(Customer::newCustomer("Tasha", $employee->get_eid()));
        $this->customer_repository->save(Customer::newCustomer("Julia", $employee->get_eid()));
        $this->customer_repository->save(Customer::newCustomer("Bela", $employee->get_eid()));

        echo "<strong>All employees and their customers</strong><br>";
        echo "======<br>";
        foreach($this->employee_repository->find_all() as $employee){
            echo $employee->to_string();
            echo "<ul>";
            foreach($employee->get_customers() as $customer){
                echo "<li>";
                echo $customer->to_string();
                echo "</li>";
            }
            echo "</ul>";
        }
        echo "<br>";

        $employee = $this->employee_repository->find_by_id($thomasID);
        echo "<strong>Employee Thomas and Thomas's customers</strong><br>";
        echo "======<br>";
        echo $employee->to_string()."<br>";
        echo "<ul>";
        foreach($employee->get_customers() as $customer){
            echo "<li>";
            echo $customer->to_string();
            echo "</li>";
        }
        echo "</ul>";
        echo "<br>";

        echo "<strong>Customer with name Bela</strong><br>";
        echo "======<br>";
        foreach($this->customer_repository->find_by_name("Bela") as $customer){
            echo $customer->to_string()."<br>";
            echo $customer->get_employee()->to_string()."<br>";
            echo "<br>";
        }
        echo "<br>";

    }

}

?>
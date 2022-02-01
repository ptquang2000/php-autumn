# php-autumn guide

## Database connection
Follow this [guide](https://github.com/ptquang2000/php-autumn/tree/db-connection) to create an entity class mapped to table in database.
The implementation is as the following code (in `/app/php/Employee.php`, `/app/php/EmployeeRepository.php`)

## Create Servie
The following code defines an Employee in our system.

app/php/EmployeeService.php

```php
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
```
This class manipulates data using `EmployeeRepository` interface methods supporting creating, reading, updating, and deleting records against a back end database. 

## Create RestController
In the controller, we only need to focus on the actions:

`app/php/EmployeeController`

```php
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

```
* `#[RestController]` indicates that the data returned by each method will be written straight into the response body instead of rendering a template.

* An `EmployeeService` is injected by constructor into the controller.

* We have routes for each operation (`method` `RequestMapping` in  corresponding to HTTP GET, POST, PUT, and DELETE calls). 

## Test Application with Docker
Run these command to start the application

```bash
docker-compose up --build
```
When the app starts, we can immediately interrogate it.
```bash
curl -X GET 127.24.0.4/employees | json_pp
```
This will yield:

![image](../assets/GET.png)

To create a new Employee record we use the following command in a terminal:
```bash
curl -X POST 127.24.0.4/employees -H 'Content-type:application/json' -d '{"name":"Jayce"}' | json_pp
```
Then it stores newly created employee and sends it back to us:

![image](../assets/POST.png)


You can update the employee. Let’s change his name.
```bash
curl -X PUT 127.24.0.4/employees/4 -H 'Content-type:application/json' -d '{"name": "Zed"}' | json_pp
```
And we can see the change reflected in the output.

![image](../assets/PUT.png)

Finally, you can delete users like this:
```bash
curl -X DELETE 127.24.0.4/employees/4 | json_pp
```

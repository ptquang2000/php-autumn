# php-autumn guide

## Acessing MySQL Database

### Define a Simple Entity
The following listing shows the Customer class (in  `/app/php/Customer.php`):
```php
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
```
Here you have a Customer class with three attributes: `$id, $name, and $employee`. 
You also have a custome constructors. The default constructor exists only for the sake of PHP-autume. 
The other constructor is the one you use to create instances of Customer to be saved to the database.

The `Customer` class is annotated with `#[Table(name: 'customer')]`, indicating that it is an entity, which is mapped to a table named customer.

The `Customer` object’s `$eid` property is annotated with `#[ID(name: 'cid')]` so that PHP-autumn recognizes it as the object’s ID. 
The `$eid` property is also annotated to indicate that the ID should be generated automatically.

The other property, name is annotated with `#[Column(name: 'name')]`. 
It is assumed that it is mapped to columns that assign to the key `name` in the annotation.

The convenient `to_string()` method print outs the customer’s properties. 

### Create Simple Queries
PHP-autumn focuses storing data in a relational database. 
Its most compelling feature is the ability to create repository implementations automatically, at runtime, from a repository interface.

To see how this works, create a repository interface that works with `Customer` entities as the following listing (in `app/php/Customerepositor.php`) shows:
```php
<?php
namespace App\PHP;
use Core\{IRepository, Entity};
#[Entity(class:'Customer')]
interface CustomerRepository extends IRepository
{
  public function find_by_name($name);
} 
?>
```
`CustomerRepository` extends the `IRepository` interface. 
The type of entity that it works with, `Customer`, are specified in the annotation on `#[Entity(class:'Customer')]`. 
By extending `IRepository`, `CustomerRepository` inherits several methods for working with `Customer` persistence, including methods for saving, deleting, and finding `Customer` entities.

php-autumn also lets you define other query methods by declaring their method signature. For example, `CustomerRepository` includes the `find_by_name()` method.

The same convention can be applied to the `Employee` entity which is mapped to employee table.

But to demonstrate the connection between two entity as well as tables. Two annotations `ManyToOne` and `OneToMany` represent the n-1 and 1-n relationship. 
You can either have singledirectional connection, bidirectional connection, or none at all.

The listing below is added to the `Customer` class:
```php
#[ManyToOne(name: 'eid', map_by:'Employee')]
private $employee;
public function set_employee($employee) {
    $this->employee = $employee;
}
public function get_employee() {
    return $this->employee;
}
```
and this for `Employee` class:
```php
#[OneToMany(map_by:'Customer')]
private $customers;
public function set_customers($customers) {
    $this->customers = $customers;
}
public function get_customers() {
    return $this->customers;
}
```
These relationship properties are not required when creating entity class.

### Create Apllication Class
The following listing shows the class that Initializr created for this example (in `app/php/Demo.php`):
```php
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
```
This class has two pproperties `$customer_repository` and `$employee_repository` annotated with `#[Autowired]` which is marked in order to help php-autumn initialize that properties automatically

These two repository saves a handful of `Customer` objects and `Employee` objects, demonstrating the `save()` method and setting up some data to work with. 

Next, it calls `find_all()` to fetch all `Employee` objects from the database. For each employee object, a array of customers is also assigned to `$customers` property by php-autumn.

Then it calls `find_by_id()` to fetch a single `Employee` by its ID. 

Finally, it calls `find_by_name()` to find all customers whose name is "Bela". These customers also has property `$employee` assigned by php-autumn.
The result is `echo` to the screen.

## Test Application with Docker
Run these command to start the application
```bash
docker-compose up --build
```
You can view the result at `http://127.24.0.4`

![image](../assets/db-connection.png)
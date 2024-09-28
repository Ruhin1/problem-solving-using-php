<?php

class Employee {
    private $name;
    private $designation;
    private $salary;

    public function __construct(string $name, string $designation, int $salary) {
        $this->name = $name;
        $this->designation = $designation;
        $this->salary = $salary;
    }

    public function getName() {
        return $this->name;
    }

    public function showEmployeeData() {
        return [
            'name' => $this->name,
            'designation' => $this->designation,
            'salary' => $this->salary
        ];
    }

    public function salaryIncrease(int $amount) {
        if ($amount > 0) {
            $this->salary += $amount;  // Increase salary
        }
    }
}

class EmployeeManager {
    private $employees = [];

    public function printArray(?array $array) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    public function getAllEmployee() {
        return count($this->employees) > 0 ? $this->employees : null;
    }

    public function addEmployee(Employee $employee) {
        $this->employees[strtolower($employee->getName())] = $employee;
    }

    public function findEmployee(string $name) {
        $name = strtolower($name);
        if (array_key_exists($name, $this->employees)) {
            return $this->employees[$name]->showEmployeeData();
        }
        return null;
    }

    public function updateSalary(string $name, int $newSalary) {
        $name = strtolower($name);
        if (array_key_exists($name, $this->employees)) {
            $this->employees[$name]->salaryIncrease($newSalary);
            return true;
        }
        return false;
    }

    public function removeEmployee($name) {
        $name = strtolower($name);
        if (array_key_exists($name, $this->employees)) {
            unset($this->employees[$name]);
            return true;
        }
        return false;
    }
}

// Testing the code
$employee1 = new Employee('Ruhin', 'Manager', 5000);
$employee2 = new Employee('Tonmoy', 'CEO', 10000);

$employeeManager = new EmployeeManager();
$employeeManager->addEmployee($employee1);
$employeeManager->addEmployee($employee2);

$employeeManager->printArray($employeeManager->getAllEmployee());
echo '<br/><br/>';
// $employeeManager->removeEmployee('Tonmoy');
// Update salary
$employeeManager->updateSalary('Ruhin', 1500);  // Increase by 1500
$employeeManager->printArray($employeeManager->getAllEmployee());

?>

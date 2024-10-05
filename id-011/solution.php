<?php 

function printArray(?array $array) {
    echo '<pre>';
    print_r($array);
    echo '</pre> <br/>';
}

class Employee {
    private $name;
    private $designation;
    private $salary = 0;

    public function __construct(string $name, string $designation, int $salary) {
        $this->name = $name;
        $this->designation = $designation;
        if($salary > 0) {
            $this->salary = $salary;
        }
    }

    public function getDetails() {
        return ['name' => $this->name, 'designation' => $this->designation, 'salary' => $this->salary];
    }

    public function updateSalary(int $salaryAmount) {
        if ($salaryAmount > 0) {
            $this->salary = $salaryAmount;
            return true;
        }
        return false;
    }
}

class Manager extends Employee {
    private $tasks = [];

    public function assignTask(string $task) {
        $this->tasks[] = $task;
        $employee = $this->getDetails();
        $employee['tasks'] = $this->tasks;
        return $employee;
    }
}

// নতুন ম্যানেজার এবং তাদের টাস্ক অ্যাসাইন করা
$ruhinTask = new Manager('Ruhin', 'Waiter', 15000);
$tonmoyTask = new Manager('Tonmoy', 'Chef', 18000);

// টাস্ক অ্যাসাইন করা এবং ফলাফল দেখানো
printArray($ruhinTask->assignTask('Serve customers'));
printArray($ruhinTask->assignTask('Clean tables'));
printArray($tonmoyTask->assignTask('Prepare dishes'));

?>

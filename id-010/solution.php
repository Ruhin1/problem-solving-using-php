<?php

// Employee ক্লাস ডেফিনিশন
class Employee {
    private $name;
    private $designation;
    private $salary;
    private $workingHours;
    private $hourlyRate;

    // Constructor এর মাধ্যমে প্রোপার্টি সেট করা
    public function __construct(string $name, string $designation, int $hourlyRate) {
        $this->name = $name;
        $this->designation = $designation;
        $this->hourlyRate = $hourlyRate;
        $this->workingHours = 0; // শুরুতে কাজের ঘন্টা ০
    }

    // কাজের ঘন্টা যোগ করা
    public function addWorkingHours(int $hours) {
        if ($hours > 0) {
            $this->workingHours += $hours;
        }
    }

    // মাসিক বেতন ক্যালকুলেশন করা
    public function calculateMonthlySalary() {
        $this->salary = $this->workingHours * $this->hourlyRate;
        return $this->salary;
    }

    // কর্মচারীর বিস্তারিত তথ্য প্রদর্শন
    public function getDetails() {
        return [
            'name' => $this->name,
            'designation' => $this->designation,
            'hourly_rate' => $this->hourlyRate,
            'working_hours' => $this->workingHours,
            'monthly_salary' => $this->salary
        ];
    }
}

// Payroll ক্লাস ডেফিনিশন
class Payroll {
    private $employees = [];

    // কর্মচারী যুক্ত করা
    public function addEmployee(Employee $employee) {
        $this->employees[] = $employee;
    }

    // সকল কর্মচারীর মাসিক বেতন প্রদর্শন করা
    public function generatePayroll() {
        foreach ($this->employees as $employee) {
            $employee->calculateMonthlySalary();
            $details = $employee->getDetails();
            echo "Employee: " . $details['name'] . "<br/>";
            echo "Designation: " . $details['designation'] . "<br/>";
            echo "Working Hours: " . $details['working_hours'] . "<br/>";
            echo "Monthly Salary: $" . $details['monthly_salary'] . "<br/>";
            echo "-------------------------- <br/>";
        }
    }
}

// উদাহরণ কর্মচারী তৈরি করা
$employee1 = new Employee("Rahim", "Waiter", 15); // ঘণ্টায় $15 রেট
$employee2 = new Employee("Karim", "Chef", 25);   // ঘণ্টায় $25 রেট


// কাজের ঘন্টা যোগ করা
$employee1->addWorkingHours(160); // মাসে ১৬০ ঘন্টা কাজ
$employee2->addWorkingHours(180); // মাসে ১৮০ ঘন্টা কাজ

// Payroll তৈরি এবং কর্মচারী যুক্ত করা
$payroll = new Payroll();
$payroll->addEmployee($employee1);
$payroll->addEmployee($employee2);

// সকল কর্মচারীর মাসিক বেতন প্রদর্শন করা
$payroll->generatePayroll();

?>

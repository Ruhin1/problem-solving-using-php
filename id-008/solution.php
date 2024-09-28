<?php

class BankAccount {

    private $accountNumber;
    private $accountHolder;
    private $balance = 0;

    public function __construct(int $accountNumber, string $accountHolder)
    {
        $this->accountNumber = $accountNumber;
        $this->accountHolder = $accountHolder;
    }

    public function checkBalance(){
        return $this->balance;
    }

    public function deposit(int $amount){
        if ($amount > 0){
            $this->balance += $amount;
        }
    }

    public function withdraw(int $amount){
        if ($amount > 0){
            if($this->balance >= $amount){
                $this->balance -= $amount;
                return ['status' => 'Success', 'balance' => $this->balance];
            }
            return ['status' => 'Failed', 'balance' => $this->balance];
        }
    }
}

$obj1 = new BankAccount(456445, 'Ruhin');
$obj1->deposit(100);
$result = $obj1->withdraw(40);
echo $result['status'];
echo " Remaining Balance: " . $obj1->checkBalance();

?>

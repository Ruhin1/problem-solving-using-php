<?php


// function printArray(?array $array) {
//     echo '<pre>';
//     print_r($array);
//     echo '</pre>';
// }
$time_start = microtime(true); 
class DigitalProduct extends Product {
    public function __construct($name, $price) {
        parent::__construct($name, $price, PHP_INT_MAX); // স্টক সীমাহীন করা হচ্ছে
    }

    // স্টক কমানোর মেথড ওভাররাইড
    public function reduceStock($quantity) {
        // ডিজিটাল পণ্য তাই স্টক কমানো হবে না
        return true;
    }
}

class PhysicalProduct extends Product {
    // ইনহেরিটেড মেথডগুলো ব্যবহার করব
}


//----------------------------------//

class Product {
    private $name;
    private $price;
    private $stock;

    // মোট বিক্রয় ট্র্যাক করার জন্য একটি স্ট্যাটিক প্রোপার্টি
    public static $totalSales = 0;

    public function __construct($name, $price, $stock) {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    // পণ্যের স্টক কমানোর মেথড
    public function reduceStock($quantity) {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            return true;
        }
        return false;
    }

    // স্টক, নাম এবং দাম ফেরত দেয়ার জন্য মেথড
    public function getStock() {
        return $this->stock;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    // বিক্রয় আপডেটের জন্য স্ট্যাটিক মেথড
    public static function updateSales($amount) {
        self::$totalSales += $amount;
    }
}


//-----------------------//

class Order {
    private $customerName;
    private $items = [];
    private $totalAmount = 0;

    public function __construct($customerName) {
        $this->customerName = $customerName;
    }

    // অর্ডারে পণ্য যোগ করা হবে এবং মোট মূল্য গণনা করা হবে
    public function addProduct(Product $product, $quantity) {
        if ($product->reduceStock($quantity)) {
            $this->items[] = ['product' => $product, 'quantity' => $quantity];
            $this->totalAmount += $product->getPrice() * $quantity;

            // বিক্রয় আপডেট করা হচ্ছে
            Product::updateSales($product->getPrice() * $quantity);
        } else {
            echo "Not enough stock for " . $product->getName() . "<br/>";
        }
    }

    public function getTotalAmount() {
        return $this->totalAmount;
    }

    public function getOrderDetails() {
        return [
            'customer' => $this->customerName,
            'items' => $this->items,
            'total' => $this->totalAmount
        ];
    }
}

//-----------------------//

class OrderManager {
    private $orders = [];

    // অর্ডার যোগ করার মেথড
    public function addOrder(Order $order) {
        $this->orders[] = $order;
    }

    // সব অর্ডারের বিস্তারিত দেখানোর মেথড
    public function showOrders() {
        foreach ($this->orders as $order) {
            echo "Customer Name: " . $order->getOrderDetails()['customer'] . "<br/>";
            echo "Total Amount: " . $order->getOrderDetails()['total'] . "<br/>";
            echo "Items: <br/>";
    
            foreach ($order->getOrderDetails()['items'] as $item) {
                $product = $item['product'];
                echo "-- Product Name: " . $product->getName() . "<br/>";
                echo "-- Product Price: $" . $product->getPrice() . "<br/>";
                echo "-- Quantity: " . $item['quantity'] . "<br/>";
                echo "-- Remaining Stock: " . $product->getStock() . "<br/><br/>";
            }
    
            echo "<hr/>";
        }
    }
    

    // মোট বিক্রয় দেখানোর মেথড
    public function showTotalSales() {
        echo "Total Sales: " . Product::$totalSales . "<br/>";
    }
}

//-------------------------//



// নতুন পণ্য তৈরি
$physicalProduct1 = new PhysicalProduct('Laptop', 1000, 5);
$digitalProduct1 = new DigitalProduct('Ebook', 20);

// নতুন অর্ডার তৈরি
$order1 = new Order('Ruhin');
$order1->addProduct($physicalProduct1, 2);
$order1->addProduct($digitalProduct1, 1);
// নতুন অর্ডার তৈরি
$order2 = new Order('Tonmoy');
$order2->addProduct($physicalProduct1, 2);
$order2->addProduct($digitalProduct1, 1);
// অর্ডার ম্যানেজার তৈরি এবং অর্ডার যোগ
$orderManager = new OrderManager();
$orderManager->addOrder($order1);
$orderManager->addOrder($order2);


// অর্ডার এবং মোট বিক্রয় দেখানো
$orderManager->showOrders();
$orderManager->showTotalSales();


$time_end = microtime(true);
$execution_time = ($time_end - $time_start) * 1000;
echo '<b>Total Execution Time:</b> '.$execution_time.' ms';






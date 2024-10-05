<?php 

// ধাপ ১: Product নামের অ্যাবস্ট্রাক্ট ক্লাস তৈরি করা
// প্রথমে, আমরা একটি অ্যাবস্ট্রাক্ট ক্লাস তৈরি করবো যার নাম হবে Product। এই ক্লাসে কিছু সাধারণ বৈশিষ্ট্য যেমন প্রোডাক্টের নাম, দাম এবং স্টক থাকবে। এছাড়া, একটি অ্যাবস্ট্রাক্ট মেথড থাকবে যেটা ইনহেরিট করা ক্লাসগুলো আলাদাভাবে ইমপ্লিমেন্ট করবে।



// Abstract Class: Product
abstract class Product {
    protected $name;
    protected $price;
    protected $stock;

    public function __construct($name, $price, $stock) {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    // Abstract Method: To calculate total value
    abstract public function calculateTotalValue();
}



//  ব্যাখ্যা:

// Product হলো অ্যাবস্ট্রাক্ট ক্লাস, যার মধ্যে সাধারণ বৈশিষ্ট্যগুলো ডিফাইন করা হয়েছে।
// অ্যাবস্ট্রাক্ট মেথড calculateTotalValue() কোনো বডি (কোড) নেই, এটা কেবলমাত্র ডিক্লেয়ার করা হয়েছে। এই মেথডটি প্রতিটি চাইল্ড ক্লাসে ইমপ্লিমেন্ট করা হবে। 


// ধাপ ২: PhysicalProduct এবং DigitalProduct ক্লাস তৈরি করা
// এখন আমরা দুটি ক্লাস তৈরি করবো যেগুলো Product ক্লাস থেকে ইনহেরিট করবে। একটি ফিজিক্যাল পণ্য যেমন ল্যাপটপের জন্য এবং অন্যটি ডিজিটাল পণ্যের জন্য।


// Class: PhysicalProduct (Inherits Product)
class PhysicalProduct extends Product {
    private $weight;
    private $shippingCost;

    public function __construct($name, $price, $stock, $weight, $shippingCost) {
        parent::__construct($name, $price, $stock);
        $this->weight = $weight;
        $this->shippingCost = $shippingCost;
    }

    // Implementing abstract method
    public function calculateTotalValue() {
        return $this->price * $this->stock + $this->shippingCost;
    }
}

// Class: DigitalProduct (Inherits Product)
class DigitalProduct extends Product {
    private $fileSize;

    public function __construct($name, $price, $stock, $fileSize) {
        parent::__construct($name, $price, $stock);
        $this->fileSize = $fileSize;
    }

    // Implementing abstract method
    public function calculateTotalValue() {
        return $this->price * $this->stock;
    }
}

// ব্যাখ্যা:

// PhysicalProduct ক্লাসটি ফিজিক্যাল পণ্যের জন্য। এটি Product ক্লাস থেকে ইনহেরিট করা হয়েছে এবং ওজন ও শিপিং খরচের জন্য অতিরিক্ত প্রোপার্টি যোগ করা হয়েছে।
// calculateTotalValue() মেথডে পণ্যের দাম ও স্টক যোগ করে মোট মূল্য নির্ধারণ করা হয়েছে এবং শিপিং খরচও যোগ করা হয়েছে।
// DigitalProduct ক্লাসটি ডিজিটাল পণ্যের জন্য যেখানে শুধুমাত্র পণ্যের দাম ও স্টক ব্যবহার করা হয়েছে, শিপিং খরচের প্রয়োজন নেই।

// ধাপ ৩: InventoryManager ক্লাস তৈরি করা
// এখন আমরা InventoryManager নামের একটি ক্লাস তৈরি করবো যেটি ইনভেন্টরি পরিচালনা করবে। এই ক্লাসে আমরা স্ট্যাটিক মেথড এবং প্রোপার্টি ব্যবহার করবো যাতে একই সাথে একাধিক পণ্য পরিচালনা করা যায়।


// Class: InventoryManager (Using static methods and properties)
class InventoryManager {
    private static $inventory = [];

    public static function addProduct(Product $product) {
        self::$inventory[] = $product;
    }

    public static function showInventory() {
        foreach (self::$inventory as $product) {
            echo "Product: " . get_class($product) . ", Total Value: " . $product->calculateTotalValue() . "<br>";
        }
    }
}

// ব্যাখ্যা:

// InventoryManager ক্লাসটি স্ট্যাটিক প্রোপার্টি এবং মেথড ব্যবহার করে ইনভেন্টরি পরিচালনা করে।
// addProduct() মেথডের মাধ্যমে নতুন পণ্য ইনভেন্টরিতে যোগ করা হয়।
// showInventory() মেথড ব্যবহার করে ইনভেন্টরিতে থাকা সমস্ত পণ্যের তথ্য দেখানো হয়।

// ধাপ ৪: অবজেক্ট তৈরি এবং পণ্য ইনভেন্টরিতে যোগ করা
// শেষে, আমরা PhysicalProduct এবং DigitalProduct ক্লাসের অবজেক্ট তৈরি করে ইনভেন্টরিতে যোগ করবো এবং ইনভেন্টরি প্রদর্শন করবো।


// Adding PhysicalProduct and DigitalProduct to inventory
$laptop = new PhysicalProduct('Laptop', 1000, 50, 2.5, 50);
$ebook = new DigitalProduct('E-book', 15, 1000, 500);

// Add products to inventory
InventoryManager::addProduct($laptop);
InventoryManager::addProduct($ebook);

// Show inventory
InventoryManager::showInventory();

?>
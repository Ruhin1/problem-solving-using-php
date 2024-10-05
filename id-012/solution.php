<?php

interface Discount {
    public function applyDiscount(float $percentage);
}

trait Logger {
    public function log($message) {
        echo "[LOG]: " . $message . "<br/>";
    }
}

class Product {
    protected $name;
    protected $category;
    protected $price;
    protected $stock;

    public function __construct(string $name, string $category, float $price) {
        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
        $this->stock = 100;
    }

    public function updatePrice(float $newPrice) {
        if ($newPrice > 0) {
            $this->price = $newPrice;
        }
    }

    public function updateStock(int $newStock) {
        if ($newStock >= 0) {
            $this->stock = $newStock;
        }
    }

    public function getDetails() {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'stock' => $this->stock
        ];
    }
}

class DigitalProduct extends Product implements Discount {
    public function applyDiscount(float $percentage) {
        $this->price -= $this->price * ($percentage / 100);
    }
}

class PhysicalProduct extends Product implements Discount {
    public function applyDiscount(float $percentage) {
        $this->price -= $this->price * ($percentage / 100);
    }
}

class ProductManager {
    use Logger;

    private $products = [];

    public function addProduct(Product $product) {
        $this->products[] = $product;
        $this->log("Product added: " . $product->getDetails()['name']);
    }

    public function removeProduct(Product $product) {
        foreach ($this->products as $key => $item) {
            if ($item === $product) {
                unset($this->products[$key]);
                $this->log("Product removed: " . $product->getDetails()['name']);
            }
        }
    }

    public function showAllProducts() {
        foreach ($this->products as $product) {
            $details = $product->getDetails();
            echo "Name: {$details['name']}, Category: {$details['category']}, Price: {$details['price']}, Stock: {$details['stock']} <br/>";
        }
    }
}

// Example Usage:
$product1 = new PhysicalProduct("Laptop", "Electronics", 50000);
$product2 = new DigitalProduct("E-Book", "Books", 150);

$productManager = new ProductManager();
$productManager->addProduct($product1);
$productManager->addProduct($product2);
$productManager->showAllProducts();
echo "<br/>";

// Apply discount and update stock
$product1->applyDiscount(10); // Physical Product gets 10% discount
$product2->applyDiscount(5);  // Digital Product gets 5% discount

$productManager->showAllProducts();

?>

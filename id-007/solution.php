<?php

class Product {
    private $id;
    private $name;
    private $price;
    private $stock;

    public function __construct($id, $name, $price, $stock) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getStock() {
        return $this->stock;
    }

    public function setStock($stock) {
        $this->stock = $stock;
    }
}

class Inventory {
    private $products = [];

    public function addProduct(Product $product) {
        $this->products[$product->getId()] = $product;
    }

    public function updateStock($id, $newStock) {
        if (isset($this->products[$id])) {
            $this->products[$id]->setStock($newStock);
        }
    }

    public function showProductDetails($id) {
        if (isset($this->products[$id])) {
            $product = $this->products[$id];
            echo "------------------------- <br/>";
            echo "Product Name: " . $product->getName() . "<br/>";
            echo "Price: " . $product->getPrice() . "<br/>";
            echo "Stock: " . $product->getStock() . "<br/>";
            echo "------------------------- <br/>";

        } else {
            echo "Product not found. <br/>";
        }
    }

    public function removeProduct($id) {
        unset($this->products[$id]);
    }
}

// Example usage:
$inventory = new Inventory();

// Adding products
$product1 = new Product(1, "Laptop", 1200, 10);
$product2 = new Product(2, "Smartphone", 800, 20);

$inventory->addProduct($product1);
$inventory->addProduct($product2);

// Show product details
$inventory->showProductDetails(1);

// Update stock
$inventory->updateStock(1, 15);

// Show updated details
$inventory->showProductDetails(1);

<?php

// MySQL ডাটাবেস কানেকশন সেটআপ
$host = 'localhost';  // সার্ভার নাম
$dbname = 'ppc';  // ডাটাবেসের নাম
$username = 'root';  // ইউজারনেম
$password = '';  // পাসওয়ার্ড

// PDO দিয়ে ডাটাবেস কানেকশন তৈরি
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}



// POST রিকোয়েস্ট থেকে ডেটা নেওয়া
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    // ডেটা যাচাই
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters"]);
        exit;
    }

    // পাসওয়ার্ড হ্যাশ করা
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ডুপ্লিকেট ইমেইল চেক করা
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "User already exists"]);
        exit;
    }

    // নতুন ব্যবহারকারী ইনসার্ট করা
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    
    if ($stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashedPassword])) {
        echo json_encode(["status" => "success", "message" => "User registered successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to register user"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
 
?>

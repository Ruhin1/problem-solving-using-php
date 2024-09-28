<?php
// ডাটাবেস সংযোগের জন্য ভ্যারিয়েবল
$host = 'localhost'; // হোস্ট, সাধারণত 'localhost'
$dbname = 'ppc'; // ডাটাবেসের নাম
$username = 'root'; // ডাটাবেস ইউজারনেম
$password = ''; // ডাটাবেস পাসওয়ার্ড

try {
    // PDO ব্যবহার করে ডাটাবেস সংযোগ করা
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ইনপুট ডেটা পাওয়া (JSON ফরম্যাটে)
    $input = json_decode(file_get_contents('php://input'), true);

    $userName = $input['username'] ?? null;
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;

    // ভ্যালিডেশন চেক
    if (!$userName || !$email || !$password) {
        echo json_encode(['error' => 'All fields are required']);
        exit();
    }

    // পাসওয়ার্ডের দৈর্ঘ্য চেক
    if (strlen($password) < 6) {
        echo json_encode(['error' => 'Password must be at least 6 characters long']);
        exit();
    }
    
    // ইমেল এবং ইউজারনেম এর ইউনিকনেস চেক
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM register WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $userName, 'email' => $email]);
    $exists = $stmt->fetchColumn();

   

    if ($exists) {
        echo json_encode(['error' => 'Username or email already exists']);
        exit();
    }

    // পাসওয়ার্ড হ্যাশ করা
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ডাটাবেসে ইউজারের ডেটা সংরক্ষণ করা
    $stmt = $pdo->prepare("INSERT INTO register (username, email, password) VALUES (:username, :email, :password)");
    $stmt->execute([
        'username' => $userName,
        'email' => $email,
        'password' => $hashedPassword
    ]);

    // সফল রেজিস্ট্রেশন বার্তা
    echo json_encode(['message' => 'User registered successfully']);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

?>

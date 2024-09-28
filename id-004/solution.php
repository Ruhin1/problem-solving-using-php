<?php
require __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
// header("Content-Type: application/json");

$host = 'localhost'; 
$dbname = 'ppc'; 
$username = 'root'; 
$password = ''; 


// jwt tocken janerate
$key = 'ruhin'; 

function generateJWT($user) {
    global $key;
    
    $payload = [
        'iss' => 'localhost', 
        'iat' => time(),      
        'exp' => time() + (60 * 60), 
        'data' => [
            'id' => $user['id'],
            'email' => $user['email']
        ]
    ];
    
    return JWT::encode($payload, $key, 'HS256');
}

//respons formet function
function response(string $status, string $message, array $data = []) {
  echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
  exit();
}

try {

  // PDO সংযোগ তৈরি
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  //user registion
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'register') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['name']) || empty($input['email']) || empty($input['password'])) {
        response('error', 'All fields are required');
    }
    
    $name = $input['name'];
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_DEFAULT); 

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM auth WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        response('error', 'User already exists');
    }

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $password])) {
        response('success', 'User registered successfully');
    } else {
        response('error', 'Registration failed');
    }
  }

  //user login
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['email']) || empty($input['password'])) {
        response('error', 'Email and password are required');
    }

    $email = $input['email'];
    $password = $input['password'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM auth WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        response('error', 'Invalid email or password', []);
    }

    // Generate JWT token
    $token = generateJWT($user);
    response('success', 'Login successful', ['token' => $token]);
  }

  //User logout
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'logout') {
    response('success', 'User logged out successfully');
  }

 

} catch (PDOException $e) {
  response('error', 'Database error: ' . $e->getMessage());
}

?>
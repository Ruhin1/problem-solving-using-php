<?php
require __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
header("Content-Type: application/json");

$key = 'RUHIN_access_key';
$refresh_key = 'RUHIN_refresh_key';


//respons formet function
function response(string $status, string $message, array $data = []) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit();
}

//generateAccessToken
function generateAccessToken($user) {
    global $key;

    $payload = [
        'iss' => 'localhost',  // Issuer
        'iat' => time(),       // Issued at time
        'exp' => time() + (60 * 2),  // 2 minet  expiration for access token
        'data' => [
            'id' => $user['id'],
            'email' => $user['email']
        ]
    ];

    return JWT::encode($payload, $key, 'HS256');
}

//generateRefreshToken
function generateRefreshToken($user) {
    global $refresh_key;

    $payload = [
        'iss' => 'localhost',
        'iat' => time(),
        'exp' => time() + (60 * 60 * 24 * 30), // 30 days expiration for refresh token
        'data' => [
            'id' => $user['id'],
            'email' => $user['email']
        ]
    ];

    return JWT::encode($payload, $refresh_key, 'HS256');
}

// Example of storing blacklisted tokens in an array (use a database in production)
$blacklist = [];

// Token blacklist function
function blacklistToken($token,$type) {
    global $blacklist,$key;

    // Fetch token expiration time from decoded token
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $expires_at = date('Y-m-d H:i:s', $decoded->exp);

    // Store token in blacklist with timestamp
    $blacklist[] = [
        'type' => $type,
        'token' => $token,
        'blacklisted_at' => date('Y-m-d H:i:s'), // Current time
        'expires_at' => $expires_at // Token expiration time
    ];
}

// Check if token is blacklisted
function isTokenBlacklisted($token) {
    global $blacklist;

    foreach ($blacklist as $blacklistedToken) {
        if ($blacklistedToken['token'] === $token) {
            // Check if token is still valid (based on expiration)
            if (strtotime($blacklistedToken['expires_at']) > time()) {
                return true; // Token is blacklisted and valid
            }
        }
    }

    return false; // Token is not blacklisted
}

// verifyAccessToken function
function verifyAccessToken($token, $headers) {
    global $key, $refresh_key;

    // First check if the token is blacklisted
    if (isTokenBlacklisted($token)) {
        return null;
    }

    try {
        // Verify the access token
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        // If the token has expired
        if ($decoded->exp < time()) {
            // Check the refresh token from headers
            if (isset($headers['Refresh-Token'])) {
                $refreshToken = str_replace('Bearer ', '', $headers['Refresh-Token']);

                // Verify the refresh token
                $refreshDecoded = JWT::decode($refreshToken, new Key($refresh_key, 'HS256'));

                // Create new access token
                $newAccessToken = generateAccessToken([
                    'id' => $refreshDecoded->data->id,
                    'email' => $refreshDecoded->data->email
                ]);

                // Return the new token and decoded data
                return [
                    'accessToken' => $newAccessToken,
                    'decoded' => [
                        'id' => $refreshDecoded->data->id,
                        'email' => $refreshDecoded->data->email
                    ]
                ];
            } else {
                // Return null if refresh token is not found
                return null;
            }
        }

        // Return the data if the token is still valid
        return (array) $decoded;

    } catch (Exception $e) {
        // Return null if token validation fails
        return null;
    }
}



/*-----------------------------*******************---------------------------------*/

//loin a user
if($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'login'){
     // Example users for authentication
     $users = [
        'id' => 1, 'email' => 'test@example.com', 'password' => 'password123'
    ];
    if(true){
        $data =[
          'access_token' => generateAccessToken($users),
          'refresh_token' => generateRefreshToken($users),
        ];
        response('succes','login succesfuly',$data);
    }else{
        response('error','Invalid credentials');
    }
}



// Token validation when hitting route
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'verify') {

    $headers = getallheaders(); // Get the headers

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader); // Drop 'Bearer'

        // Verify access token and if necessary generate new access token via refresh token
        $verifiedData = verifyAccessToken($token, $headers);

        if ($verifiedData) {
            // If a new access token is generated, return the data with the new token
            if (isset($verifiedData['accessToken'])) {
                response('success', 'New access token generated', [
                    'access_token' => $verifiedData['accessToken'],
                    'user_data' => $verifiedData['decoded']
                ]);
            } else {
                // Only return the data of the validated access token
                response('success', 'Access granted', $verifiedData);
            }
        } else {
            response('error', 'Invalid or expired access token');
        }
    } else {
        response('error', 'Authorization header not found');
    }
}

// Logout function to invalidate the access and refresh tokens
if($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'logout'){
    $headers = apache_request_headers();

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        $accessToken = str_replace('Bearer ', '', $authHeader);

        // Access token blacklist করুন
        blacklistToken($accessToken,'Access-Token');
    }

    if (isset($headers['Refresh-Token'])) {
        $refreshToken = str_replace('Bearer ', '', $headers['Refresh-Token']);

        // Refresh token blacklist করুন
        blacklistToken($refreshToken,'Refresh-Token');
    }

    // Successful logout response
    response('success', 'Logged out successfully');
}

?>
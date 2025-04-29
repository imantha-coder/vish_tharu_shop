<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Simple admin authentication (hardcoded for demo/development)
$admin_username = "Vishtharu@@";
$admin_password = "5647b2ff0bf307a2530ff41fe428707a801f21978fedcadb820861de9711a08c"; // SHA256 hash of "335678@"
$jwt_secret = "vishtharu-lovely-shop-secret-key";
$token_expiry = 86400; // 24 hours

// Generate JWT token
function generateToken($username, $secret, $expiry) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'username' => $username,
        'exp' => time() + $expiry,
        'role' => 'admin'
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

// Verify JWT token
function verifyToken($token, $secret) {
    if (empty($token)) {
        return false;
    }

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }

    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

    $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));
    $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);

    if (!hash_equals($signature, $expectedSignature)) {
        return false;
    }

    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);

    if (!isset($payload['exp']) || $payload['exp'] < time()) {
        return false;
    }

    return true;
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            // Handle login request
            $rawData = file_get_contents("php://input");
            $data = json_decode($rawData);

            if (json_last_error() !== JSON_ERROR_NONE) {
                sendJsonResponse([
                    "success" => false,
                    "message" => "Invalid JSON data: " . json_last_error_msg()
                ], 400);
            }

            if (!empty($data->username) && !empty($data->password)) {
                // Verify against hardcoded admin credentials
                if ($data->username === $admin_username && $data->password === $admin_password) {
                    // Generate token
                    $token = generateToken($data->username, $jwt_secret, $token_expiry);
                    
                    sendJsonResponse([
                        "success" => true,
                        "message" => "Login successful",
                        "token" => $token
                    ]);
                } else {
                    sendJsonResponse([
                        "success" => false,
                        "message" => "Invalid credentials"
                    ], 401);
                }
            } else {
                sendJsonResponse([
                    "success" => false,
                    "message" => "Username and password are required"
                ], 400);
            }
            break;

        case 'GET':
            // Handle token verification
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

            if (verifyToken($token, $jwt_secret)) {
                sendJsonResponse([
                    "authenticated" => true,
                    "message" => "Token is valid"
                ]);
            } else {
                sendJsonResponse([
                    "authenticated" => false,
                    "message" => "Invalid or expired token"
                ], 401);
            }
            break;

        default:
            sendJsonResponse([
                "success" => false,
                "message" => "Method not allowed"
            ], 405);
            break;
    }
} catch (Exception $e) {
    sendJsonResponse([
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ], 500);
}
?>
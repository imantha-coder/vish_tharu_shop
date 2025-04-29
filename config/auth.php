<?php
require_once 'database.php';
require_once 'session.php';

class Auth {
    private $db;
    private $session;
    private $conn;
    private $secret_key = "vishtharu-lovely-shop-secret-key"; // Secret key for JWT
    private $token_expiry = 86400; // 24 hours

    // Admin credentials (hardcoded for simplicity)
    private $admin_username = "admin";
    private $admin_password = "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918"; // SHA256 hash of "admin"

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
        $this->conn = $this->db->getConnection();
    }

    public function login($username, $password) {
        // Simple admin authentication without database
        if ($username === $this->admin_username && $password === $this->admin_password) {
            $this->session->set('user_id', 1);
            $this->session->set('user_username', $username);
            $this->session->set('user_role', 'admin');
            return true;
        }
        return false;
    }

    public function logout() {
        $this->session->destroy();
    }

    public function isAdmin() {
        return $this->session->get('user_role') === 'admin';
    }

    public function getCurrentUser() {
        if (!$this->session->isLoggedIn()) {
            return null;
        }

        // For hardcoded admin
        if ($this->session->get('user_username') === $this->admin_username) {
            return [
                'id' => 1,
                'username' => $this->admin_username,
                'role' => 'admin'
            ];
        }
        
        return null;
    }

    public function generateToken($username) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'username' => $username,
            'exp' => time() + $this->token_expiry,
            'role' => 'admin'
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function verifyToken($token) {
        if (empty($token)) {
            return false;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

        $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret_key, true);

        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);

        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }

        return true;
    }
}
?>
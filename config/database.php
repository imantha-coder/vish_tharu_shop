<?php
require_once 'config.php';

class Database {
    private $conn = null;

    public function __construct() {
        // Only attempt to connect if actually needed
        // This prevents immediate errors if database doesn't exist
    }

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                    DB_USER,
                    DB_PASS
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                return $this->conn;
            } catch(PDOException $e) {
                // Log error but don't crash
                error_log("Database connection failed: " . $e->getMessage());
                return null;
            }
        }
        return $this->conn;
    }
    
    // Check if connection is successful
    public function isConnected() {
        return $this->getConnection() !== null;
    }
}
?>
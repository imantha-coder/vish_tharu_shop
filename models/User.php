<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'users';

    // User properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new user
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                (username, email, password, first_name, last_name, role)
                VALUES
                (:username, :email, :password, :first_name, :last_name, :role)";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Bind data
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':role', $data['role'] ?? 'customer');

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get user by ID
    public function read($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Get user by email
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Get user by username
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Update user
    public function update($id, $data) {
        $set_clause = [];
        $params = [];

        foreach($data as $key => $value) {
            if($key !== 'id' && $key !== 'created_at' && $key !== 'updated_at') {
                if($key === 'password') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                $set_clause[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        if(empty($set_clause)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " 
                SET " . implode(', ', $set_clause) . "
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $params[':id'] = $id;

        return $stmt->execute($params);
    }

    // Delete user
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Verify password
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Get all users
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
} 
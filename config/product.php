<?php
require_once 'database.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllProducts() {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get products error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductById($id) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get product error: " . $e->getMessage());
            return null;
        }
    }

    public function addProduct($data) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("INSERT INTO products (name, category, price_range, color, description, image, is_available) 
                                  VALUES (:name, :category, :price_range, :color, :description, :image, :is_available)");
            
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':price_range', $data['price_range']);
            $stmt->bindParam(':color', $data['color']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':image', $data['image']);
            $stmt->bindParam(':is_available', $data['is_available']);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Add product error: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $data) {
        try {
            // First get the current product data
            $currentProduct = $this->getProductById($id);
            if (!$currentProduct) {
                return false;
            }

            $conn = $this->db->getConnection();
            
            // Build the update query dynamically based on changed fields
            $query = "UPDATE products SET ";
            $params = array();
            $updates = array();

            // Check each field and add to update if changed
            if (isset($data['name']) && $data['name'] !== $currentProduct['name']) {
                $updates[] = "name = :name";
                $params[':name'] = $data['name'];
            }
            if (isset($data['category']) && $data['category'] !== $currentProduct['category']) {
                $updates[] = "category = :category";
                $params[':category'] = $data['category'];
            }
            if (isset($data['price_range']) && $data['price_range'] !== $currentProduct['price_range']) {
                $updates[] = "price_range = :price_range";
                $params[':price_range'] = $data['price_range'];
            }
            if (isset($data['color']) && $data['color'] !== $currentProduct['color']) {
                $updates[] = "color = :color";
                $params[':color'] = $data['color'];
            }
            if (isset($data['description']) && $data['description'] !== $currentProduct['description']) {
                $updates[] = "description = :description";
                $params[':description'] = $data['description'];
            }
            if (isset($data['image']) && $data['image'] !== $currentProduct['image']) {
                $updates[] = "image = :image";
                $params[':image'] = $data['image'];
            }
            if (isset($data['is_available']) && $data['is_available'] !== $currentProduct['is_available']) {
                $updates[] = "is_available = :is_available";
                $params[':is_available'] = $data['is_available'];
            }

            // If no fields were changed, return true (nothing to update)
            if (empty($updates)) {
                return true;
            }

            // Add the WHERE clause
            $query .= implode(", ", $updates) . " WHERE id = :id";
            $params[':id'] = $id;

            $stmt = $conn->prepare($query);
            
            // Bind all parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update product error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }

    public function getProductsByCategory($category) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM products WHERE category = :category AND is_available = 1 ORDER BY created_at DESC");
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get products by category error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductsByPriceRange($priceRange) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM products WHERE price_range = :price_range AND is_available = 1 ORDER BY created_at DESC");
            $stmt->bindParam(':price_range', $priceRange);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get products by price range error: " . $e->getMessage());
            return [];
        }
    }
}
?> 
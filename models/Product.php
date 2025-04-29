<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $category_id;
    public $category; // Added this
    public $color;
    public $price_range; // Changed from price
    public $image; // Changed from image_path
    public $is_available; // Changed from availability

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
        (name, description, category_id, category, color, price_range, image, is_available)
        VALUES
        (:name, :description, :category_id, :category, :color, :price_range, :image, :is_available)";

$stmt = $this->conn->prepare($query);

$stmt->bindParam(":name", $this->name);
$stmt->bindParam(":description", $this->description);
$stmt->bindParam(":category_id", $this->category_id);
$stmt->bindParam(":category", $this->category);
$stmt->bindParam(":color", $this->color);
$stmt->bindParam(":price_range", $this->price_range);
$stmt->bindParam(":image", $this->image);
$stmt->bindParam(":is_available", $this->is_available);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT p.*, c.name as category_name 
                FROM " . $this->table_name . " p
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        // First get the current product data
        $currentProduct = $this->readOne();
        if (!$currentProduct) {
            return false;
        }

        // Build the update query dynamically based on changed fields
        $query = "UPDATE " . $this->table_name . " SET ";
        $params = array();
        $updates = array();

        // Check each field and add to update if changed
        if ($this->name !== null && $this->name !== $currentProduct['name']) {
            $updates[] = "name = :name";
            $params[':name'] = $this->name;
        }
        if ($this->description !== null && $this->description !== $currentProduct['description']) {
            $updates[] = "description = :description";
            $params[':description'] = $this->description;
        }
        if ($this->category_id !== null && $this->category_id !== $currentProduct['category_id']) {
            $updates[] = "category_id = :category_id";
            $params[':category_id'] = $this->category_id;
        }
        if ($this->color !== null && $this->color !== $currentProduct['color']) {
            $updates[] = "color = :color";
            $params[':color'] = $this->color;
        }
        if ($this->price_range !== null && $this->price_range !== $currentProduct['price_range']) {
            $updates[] = "price_range = :price_range";
            $params[':price_range'] = $this->price_range;
        }
        if ($this->image !== null && $this->image !== $currentProduct['image']) {
            $updates[] = "image = :image";
            $params[':image'] = $this->image;
        }
        if ($this->is_available !== null && $this->is_available !== $currentProduct['is_available']) {
            $updates[] = "is_available = :is_available";
            $params[':is_available'] = $this->is_available;
        }

        // If no fields were changed, return true (nothing to update)
        if (empty($updates)) {
            return true;
        }

        // Add the WHERE clause
        $query .= implode(", ", $updates) . " WHERE id = :id";
        $params[':id'] = $this->id;

        // Prepare and execute the query
        $stmt = $this->conn->prepare($query);
        
        // Bind all parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Add a new method to read a single product
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?> 
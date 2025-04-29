<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $user_id;
    public $total_amount;
    public $status;
    public $shipping_address;
    public $contact_number;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (user_id, total_amount, status, shipping_address, contact_number)
                VALUES
                (:user_id, :total_amount, :status, :shipping_address, :contact_number)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":total_amount", $this->total_amount);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":shipping_address", $this->shipping_address);
        $stmt->bindParam(":contact_number", $this->contact_number);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function read() {
        $query = "SELECT o.*, u.username 
                FROM " . $this->table_name . " o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status
                WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function addOrderItem($order_id, $product_id, $quantity, $price) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":order_id", $order_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":price", $price);

        return $stmt->execute();
    }

    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.name as product_name
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();

        return $stmt;
    }
}
?> 
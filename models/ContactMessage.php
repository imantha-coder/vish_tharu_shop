<?php
class ContactMessage {
    private $conn;
    private $table_name = "contact_messages";

    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (name, email, subject, message)
                VALUES
                (:name, :email, :subject, :message)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":subject", $this->subject);
        $stmt->bindParam(":message", $this->message);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
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
}
?> 
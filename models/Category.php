<?php
namespace Models;
use PDO;
class Category {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }
    public function addNewCategorie($input)
    {
        $stmt = $this->conn->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
        $stmt->bindParam(':name', $input['name']);
        $stmt->bindParam(':description', $input['description']);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to add category'];
        }
    }
 
}

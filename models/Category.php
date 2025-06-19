<?php
class Category {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }
 
}

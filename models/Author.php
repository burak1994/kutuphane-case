<?php
class Author {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

}

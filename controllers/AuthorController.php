<?php
require_once __DIR__ . '/../models/Author.php';
class AuthorController {
    private Author $author;

    public function __construct(PDO $db) {
        $this->author = new Author($db);
    }
 
}
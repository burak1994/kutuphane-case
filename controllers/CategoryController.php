<?php
require_once __DIR__ . '/../models/Category.php';
class CategoryControllerController {
    private Category $category;

    public function __construct(PDO $db) {
        $this->category = new Category($db);
    }
 
}
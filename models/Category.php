<?php

namespace Models;

use PDO;
use PDOException;
use Helpers\LoggerHelpers;

class Category
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }
    public function addNewCategorie($input)
    {
        try {

            $stmt = $this->conn->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
            $stmt->bindParam(':name', $input['name']);
            $stmt->bindParam(':description', $input['description']);

            if ($stmt->execute()) {
                # Log the success message
                LoggerHelpers::info('addNewCategorie@Category Model Category added successfully');
                return ['success' => true];
            } else {
                # Log the error message
                LoggerHelpers::error('addNewCategorie@Category Model Failed to add category');
                return ['success' => false, 'error' => 'Failed to add category'];
            }
        } catch (PDOException $th) {
            $errorInfo = $th->errorInfo;
            # Log the error message
            LoggerHelpers::error('addNewCategorie@Category Model Category adding failed: ' . $errorInfo[2]);
            return [];
        }
    }
}

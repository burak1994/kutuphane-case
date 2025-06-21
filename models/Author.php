<?php

namespace Models;

use PDO;
use PDOException;
use Helpers\AppHelpers;
use Helpers\LoggerHelpers;

class Author
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function addNewAuthor($data): array
    {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO authors (name,email)
                VALUES (:name, :email)
            ");

            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $success = $stmt->execute();
            # Log the success message
            LoggerHelpers::info('addNewAuthor@Author Model Author added successfully');
            return ['success' => $success];
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            # Log the error message
            LoggerHelpers::info('addNewAuthor@Author Model Author adding failed: ' . $errorInfo[2]);

            $driverErrorMessage = AppHelpers::getSqlErrorMessage($errorInfo[1], $errorInfo[2]); // get the error message
            return [
                'success' => false,
                'error' => $driverErrorMessage,

            ];
        }
    }
    public function getAuthorsBooks($id, $perPage, $offset)
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT b.* FROM books b
            JOIN authors a ON b.author_id = a.id
            WHERE a.id = :id
            LIMIT :limit OFFSET :offset
        ");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            # Log the success message
            LoggerHelpers::info('getAuthorsBooks@Author Model Books fetched successfully for author id: ' . $id);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            # Log the error message
            LoggerHelpers::error('getAuthorsBooks@Author Model Books fetching failed for author id: ' . $id . ' Error: ' . $errorInfo[2]);
        }
    }

    public function countAuthorsBooks($id): int
    {
        try {
            $stmt = $this->conn->prepare("
        SELECT COUNT(*) FROM books b
        JOIN authors a ON b.author_id = a.id
        WHERE a.id = :id");

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            # Log the success message
            LoggerHelpers::info('countAuthorsBooks@Author Model Count of books fetched successfully for author id: ' . $id);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            # Log the error message
            LoggerHelpers::error('countAuthorsBooks@Author Model Count of books fetching failed for author id: ' . $id . ' Error: ' . $errorInfo[2]);
            return 0;
        }
    }
}

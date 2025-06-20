<?php
namespace Models;
use PDO;
use PDOException;
use Helpers\AppHelpers;
class Author {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function addNewAuthor($data): array {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO authors (name,email)
                VALUES (:name, :email)
            ");
    
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $success = $stmt->execute();

            return ['success' => $success];
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;

            $driverErrorMessage = AppHelpers::getSqlErrorMessage($errorInfo[1],$errorInfo[2]); // get the error message
            return [
                'success' => false,
                'error' => $driverErrorMessage,
        
            ];
        }
    }
    public function getAuthorsBooks($id,$perPage, $offset)
    {
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAuthorsBooks($id): int {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) FROM books b
            JOIN authors a ON b.author_id = a.id
            WHERE a.id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

}

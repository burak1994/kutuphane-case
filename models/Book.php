<?php
require_once __DIR__ . '/../helpers/Helpers.php';

class Book {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getAll($limit, $offset) {
        $stmt = $this->conn->prepare("SELECT * FROM books LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function countAll() {
        return (int) $this->conn->query("SELECT COUNT(*) FROM books")->fetchColumn();
    }
    public function getById($id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE id =:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }
    public function addNewBook($data): array {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO books (title, isbn, author_id, category_id, publication_year, page_count)
                VALUES (:title, :isbn, :author_id, :category_id, :publication_year, :page_count)
            ");
    
            $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindValue(':isbn', $data['isbn'], PDO::PARAM_STR);
            $stmt->bindValue(':author_id', $data['author_id'], PDO::PARAM_INT);
            $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
            $stmt->bindValue(':publication_year', $data['publication_year'], PDO::PARAM_INT);
            $stmt->bindValue(':page_count', $data['page_count'], PDO::PARAM_INT);
    
            $success = $stmt->execute();
    
            return ['success' => $success];
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;

            $driverErrorMessage = AppHelpers::getSqlErrorMessage($errorInfo[1]); // get the error message
            return [
                'success' => false,
                'error' => $driverErrorMessage,
        
            ];
        }
    }
    
    
}

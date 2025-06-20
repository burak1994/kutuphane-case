<?php
 namespace Models;
 use PDO;
 use PDOException;
use Helpers\AppHelpers;
class Book {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }


    
    public function getById($id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE id =:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    public function searchBooks($query, $limit, $offset): array {
        $stmt = $this->conn->prepare("
            SELECT * FROM books 
            WHERE title =:query OR isbn =:query 
            LIMIT :limit OFFSET :offset
        ");
         $stmt->bindValue(':query', $query, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
 

    public function updateBook(int $id, array $data): array {
        try {
            $stmt = $this->conn->prepare("
                UPDATE books 
                SET title = :title,
                    isbn = :isbn,
                    author_id = :author_id,
                    category_id = :category_id,
                    publication_year = :publication_year,
                    page_count = :page_count
                WHERE id = :id
            ");
    
            $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindValue(':isbn', $data['isbn'], PDO::PARAM_STR);
            $stmt->bindValue(':author_id', $data['author_id'], PDO::PARAM_INT);
            $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
            $stmt->bindValue(':publication_year', $data['publication_year'], PDO::PARAM_INT);
            $stmt->bindValue(':page_count', $data['page_count'], PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT); 
    
            $success = $stmt->execute();
    
            return ['success' => $success];
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            $driverErrorMessage = AppHelpers::getSqlErrorMessage($errorInfo[1]);
    
            return [
                'success' => false,
                'error' => $driverErrorMessage
            ];
        }
    }
    

    public function deleteBook(int $id): array {
        try {
            $stmt = $this->conn->prepare("DELETE FROM books WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();
    
            if ($success && $stmt->rowCount() === 0) {
                return ['success' => false, 'error' => 'No record found to delete'];
            }
    
            return ['success' => $success];
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            $driverErrorMessage = AppHelpers::getSqlErrorMessage($errorInfo[1]);
    
            return [
                'success' => false,
                'error' => $driverErrorMessage
            ];
        }
    }
    
    
}

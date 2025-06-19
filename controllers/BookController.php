<?php
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../helpers/Helpers.php';
class BookController
{
    private Book $book;

    public function __construct(PDO $db)
    {
        $this->book = new Book($db);
    }

    public function getAll()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 10;
        $offset = ($page - 1) * $perPage;

        $books = $this->book->getAll($perPage, $offset);
        $total = $this->book->countAll();

        $pagination = [
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total_items' => $total
        ];
        # return paginated data
        AppHelpers::paginated($books, $pagination);
    }


    public function getById($id)
    {
        $checkId = AppHelpers::cleanString($id);
        $book = $this->book->getById($checkId);
        if ($book) {
            echo json_encode(['success' => true, 'data' => $book]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Book not found']);
        }
    }

    public function addNewBook()
    {
        $input = json_decode(file_get_contents("php://input"), true);
    
        # check if inputs are  valid
        if (!isset($input['title'], $input['isbn'], $input['author_id'], $input['category_id'], $input['publication_year'], $input['page_count'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }
    
        # ISBN check 
        if (!preg_match('/^\d{13}$/', $input['isbn'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ISBN must be 13 digits']);
            return;
        }
    
        # Nuember fields check
        if (
            !is_numeric($input['author_id']) || 
            !is_numeric($input['category_id']) || 
            !is_numeric($input['publication_year']) || 
            !is_numeric($input['page_count'])
        ) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Author ID, Category ID, Publication Year and Page Count must be numeric']);
            return;
        }
    
        # publication year check
        if (!preg_match('/^\d{4}$/', $input['publication_year'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Publication Year must be a 4-digit year']);
            return;
        }
    
        # inputs cleaning
        $input = AppHelpers::cleanArray($input);
        $success = $this->book->addNewBook($input);
    
        echo json_encode([
            'success' => $success['success'],
            'message' => $success['success'] ? 'Book Added' : $success['error']
        ]);
    }
    
}

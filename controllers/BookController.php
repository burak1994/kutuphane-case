<?php
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../helpers/Helpers.php';
require_once __DIR__ . '/../helpers/BookHelpers.php';
require_once __DIR__ . '/../models/Base.php';

class BookController extends Base
{
    private Book $book;

    public function __construct(PDO $db)
    {
        $this->book = new Book($db);
        parent::__construct($db, 'books');
    }

    public function getAll()
    {
        $page    =  1;
        $perPage =  10;
        $offset  = ($page - 1) * $perPage;
        # get all books with pagination from base model
        $books   =  parent::getAllData($perPage, $offset);
        # get the total count of books
        $total   = parent::countAll();

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
        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
            http_response_code(400);
            echo json_encode($validId);
            return;
        }

         $book = $this->book->getById($validId['id']);
        if ($book) {
            echo json_encode(['success' => true, 'data' => $book]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Book not found']);
        }
    }

    public function addNewData()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        # check if the input is valid JSON
        if (is_null($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON format']);
            return;
        }
        # filter the input data
        $filteredData = BookHelpers::filterTheData($input);
        if (!$filteredData['success']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $filteredData['message']]);
            return;
        }
        # clean the input data
        $input = AppHelpers::cleanArray($input);
        # add the new book
        $success = $this->book->addNewBook($input);

        echo json_encode([
            'success' => $success['success'] ?? false,
            'message' => $success['success'] ? 'Book Added' : ($success['error'] ?? 'An error occurred')
        ]);
    }



    public function updateData($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);

        # check if the input is valid JSON
        if (is_null($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON format']);
            return;
        }
        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
            http_response_code(400);
            echo json_encode($validId);
            return;
        }
        # filter the input data
        $filteredData = BookHelpers::filterTheData($input);
        if (!$filteredData['success']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $filteredData['message']]);
            return;
        }
        # clean the input data
        $input = AppHelpers::cleanArray($input);

        # add the new book
        $success = $this->book->updateBook($validId['id'], $input);

        echo json_encode([
            'success' => $success['success'] ?? false,
            'message' => $success['success'] ? 'Book Updated' : ($success['error'] ?? 'An error occurred')
        ]);
    }

    public function deleteData($id)
    {
        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
            http_response_code(400);
            echo json_encode($validId);
            return;
        }

        # remove the book
        $result = $this->book->deleteBook($validId['id']);

        echo json_encode([
            'success' => $result['success'] ?? false,
            'message' => $result['success'] ? 'Book Deleted' : ($result['error'] ?? 'An error occurred')
        ]);
    }

    public function searchByQuery($query)
    {
        $query = AppHelpers::cleanString($query);

        $page    =  1;
        $perPage =  10;
        $offset  = ($page - 1) * $perPage;

        $books   = $this->book->searchBooks($query,$perPage, $offset);
        # get the total count of books
        $total   = parent::countAll('title =:query OR isbn =:query ', [':query' => $query]);

        $pagination = [
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total_items' => $total
        ];
        # return paginated data
        AppHelpers::paginated($books, $pagination);


        if ($books) {
            echo json_encode(['success' => true, 'data' => $books]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No books found']);
        }
    }
}

<?php
namespace Controllers;
use PDO;
use Models\Book;
use Models\Base;
use Helpers\AppHelpers;
use Helpers\BookHelpers;


class BookController extends Base
{
    private Book $book;

    public function __construct(PDO $db)
    {
        $this->book = new Book($db);
        parent::__construct($db, 'books');
    }
    # get all books with pagination
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
        return AppHelpers::paginated($books, $pagination);
    }

    # get a book by ID
    public function getById($id): array
    {
        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
            return ['status' => 400, 'body' => $validId];
        
        }

        $book = $this->book->getById($validId['id']);
        if ($book) {
            return ['status' => 200,'body' =>['success' => true, 'data' => $book,'message' => 'book fetched successfully']];
        } else {
            return ['status' => 200,'body' =>['success' => false, 'message' => 'Book not found']];
        }
    }
    # add a new book
    public function addNewData()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        # check if the input is valid JSON
        if (is_null($input)) {
             return ['status' => 400,'body' =>['success' => false, 'message' => 'Invalid JSON format']];
        }
        # filter the input data
        $filteredData = BookHelpers::filterTheData($input);
        if (!$filteredData['success']) {
            return  ['status' => 400,'body' =>['success' => false, 'message' => $filteredData['message']]];
           
        }
        # clean the input data
        $input = AppHelpers::cleanArray($input);
        # add the new book
        $success = $this->book->addNewBook($input);

        return ['status' => 400,'body' =>
            ['success' => $success['success'] ?? false,
            'message' => $success['success'] ? 'Book Added' : ($success['error'] ?? 'An error occurred')
        ]];
    }


    # update a book by ID
    public function updateData($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);

        # check if the input is valid JSON
        if (is_null($input)) {
           return ['status' =>400,'body' =>['success' => false, 'message' => 'Invalid JSON format']];
        }
        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
             return ['status' => 400,'body' =>$validId];
            }
        # filter the input data
        $filteredData = BookHelpers::filterTheData($input);
        if (!$filteredData['success']) {
              return ['status' => 400, 'body' => ['success' => false, 'message' => $filteredData['message']]];

        }
        # clean the input data
        $input = AppHelpers::cleanArray($input);

        # add the new book
        $success = $this->book->updateBook($validId['id'], $input);

        return ['status'=>200,'body' =>[
            'success' => $success['success'] ?? false,
            'message' => $success['success'] ? 'Book Updated' : ($success['error'] ?? 'An error occurred')
        ]];
    }

    # delete a book by ID
    public function deleteData($id)
    {
        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
            http_response_code(400);
            return ['status' => 400, 'body' => $validId];
        }

        # remove the book
        $result = $this->book->deleteBook($validId['id']);

        return ['status' => 200, 'body' =>[
            'success' => $result['success'] ?? false,
            'message' => $result['success'] ? 'Book Deleted' : ($result['error'] ?? 'An error occurred')
        ]];
    }

    # search books by query ( title or ISBN )
    public function searchByQuery($query)
    {
        $query = AppHelpers::cleanString($query);

        $page    =  1;
        $perPage =  10;
        $offset  = ($page - 1) * $perPage;

        $books   = $this->book->searchBooks($query, $perPage, $offset);
        # get the total count of books
        $total   = parent::countAll('title =:query OR isbn =:query ', [':query' => $query]);

        $pagination = [
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total_items' => $total
        ];
        # return paginated data
        return AppHelpers::paginated($books, $pagination);


        if ($books) {
            return ['status' => 200, 'body' =>['success' => true, 'data' => $books]];
        } else {
             return ['status' => 404,'body' =>['success' => false, 'message' => 'No books found']];
        }
    }
}

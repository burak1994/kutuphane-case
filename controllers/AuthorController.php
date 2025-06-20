<?php
require_once __DIR__ . '/../models/Author.php';
require_once __DIR__ . '/../helpers/Helpers.php';
require_once __DIR__ . '/../helpers/AuthorHelpers.php';
require_once __DIR__ . '/../models/Base.php';
class AuthorController extends Base{
    private Author $author;

    public function __construct(PDO $db) {
        $this->author = new Author($db);
        parent::__construct($db, 'authors');
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
        $filteredData = AuthorHelpers::filterTheData($input);
        if (!$filteredData['success']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $filteredData['message']]);
            return;
        }
        # clean the input data
        $input = AppHelpers::cleanArray($input);
        # add the new book
        $success = $this->author->addNewAuthor($input);

        echo json_encode([
            'success' => $success['success'] ?? false,
            'message' => $success['success'] ? 'Author Added' : ($success['error'] ?? 'An error occurred')
        ]);
    }

    public function  getAuthorsBooks($id)
    {

        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
            http_response_code(400);
            echo json_encode($validId);
            return;
        }

        $id = $validId['id'];
        $page    =  1;
        $perPage =  10;
        $offset  = ($page - 1) * $perPage;
        # get author with pagination from authors model
        $authors   =  $this->author->getAuthorsBooks($id,$perPage, $offset);
        # get the total count of authors
        $total   = $this->author->countAuthorsBooks($id);

        $pagination = [
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total_items' => $total
        ];
        # return paginated data
        AppHelpers::paginated($authors, $pagination);
    }

}
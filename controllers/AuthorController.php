<?php

namespace Controllers;

use Helpers;
use PDO;
use Models\Author;
use Models\Base;
use Helpers\AppHelpers;
use Helpers\AuthorHelpers;

class AuthorController extends Base
{
    private Author $author;

    public function __construct(PDO $db)
    {
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
        return AppHelpers::paginated($books, $pagination);
    }

    public function addNewData()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        # check if the input is valid JSON
        if (is_null($input)) {

 
            return ['status' => 400, 'body' => ['success' => false, 'message' => 'Invalid JSON format']];
        }
        # filter the input data
        $filteredData = AuthorHelpers::filterTheData($input);
        if (!$filteredData['success']) {
            return  ['status' => 400, 'body' => ['success' => false, 'message' => $filteredData['message']]];
        }
        # clean the input data
        $input = AppHelpers::cleanArray($input);
        # add the new book
        $success = $this->author->addNewAuthor($input);
         
        return ['status' => 200, 'body' =>
        [
            'success' => $success['success'] ?? false,
            'message' => $success['success'] ? 'Author Added' : ($success['error'] ?? 'An error occurred')
        ]];
    }

    public function  getAuthorsBooks($id)
    {

        # validate the ID
        $validId = AppHelpers::isValidId($id);
        if (!$validId['success']) {
            return ['status' => 400, 'body' => $validId];
        }

        $id = $validId['id'];
        $page    =  1;
        $perPage =  10;
        $offset  = ($page - 1) * $perPage;
        # get author with pagination from authors model
        $authors   =  $this->author->getAuthorsBooks($id, $perPage, $offset);
        # get the total count of authors
        $total   = $this->author->countAuthorsBooks($id);

        $pagination = [
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total_items' => $total
        ];
        # return paginated data
        return AppHelpers::paginated($authors, $pagination);
    }
}

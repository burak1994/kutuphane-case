<?php
namespace Controllers;
use PDO;
use Models\Category;
use Models\Base;
use Helpers\AppHelpers;
use Helpers\CategoryHelpers;

class CategoryController extends Base{
    private Category $category;

    public function __construct(PDO $db) {
        $this->category = new Category($db);
        parent::__construct($db, 'categories');

    }


    public function getAll()
    {
        $page    =  1;
        $perPage =  10;
        $offset  = ($page - 1) * $perPage;
        # get all categories  with pagination from base model
        $books   =  parent::getAllData($perPage, $offset);
        # get the total count of categories
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
            return ['status' => 400,'body' =>['success' => false, 'message' => 'Invalid JSON format']];
         }
        # filter the input data
        $filteredData = CategoryHelpers::filterTheData($input);
        if (!$filteredData['success']) {
             return  ['status' => 400,'body' =>['success' => false, 'message' => $filteredData['message']]];
            
        }
        # clean the input data
        $input = AppHelpers::cleanArray($input);
        # add the new book
        $success = $this->category->addNewCategorie($input);

        return ['status' => $success['success'] ? 200 : 400,'body' =>
        ['success' => $success['success'] ?? false,
        'message' => $success['success'] ? 'Category Added' : ($success['error'] ?? 'An error occurred')
    ]];
    }

 
}
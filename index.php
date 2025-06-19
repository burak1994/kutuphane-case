<?php
// api/index.php

require_once 'config/Database.php';
require_once 'helpers/Helpers.php';
require_once 'controllers/BookController.php';
require_once 'controllers/AuthorController.php';
require_once 'controllers/CategoryController.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");


$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace('/kutuphane-case/', '', $uri);
$uri = trim($uri, '/');
$segments = explode('/', $uri);
$method = $_SERVER['REQUEST_METHOD'];

# Connect to the database
$db = (new Database())->getConnection(); 

 if ($segments[0] !== 'api') {
    AppHelpers::json(['success' => false,'message' => 'Invalid endpoint'], 404);
}

# Check the ID and sub parameters
$id = $segments[2] ?? null;
$sub = $segments[3] ?? null;
$resource = $segments[1] ?? null;

switch ($resource) {
    case 'books':
        $controller = new BookController($db);
        if ($id != '' && $method == 'GET') {
            $controller->getById($id);
        } else {
            AppHelpers::routeHandler($controller, $method, $id);
        }
        break;

    case 'authors':
        $controller = new AuthorController($db);
        if ($sub === 'books' && $id) {
            //$controller->booksByAuthor($id);
        } else {
            AppHelpers::routeHandler($controller, $method, $id);
        }
        break;

    case 'categories':
       // $controller = new CategoryController($db);
       AppHelpers::routeHandler($controller, $method, $id);
        break;

    default:
    AppHelpers::json(['success' => false,'message' => 'Resource not found'], 404);
}


?>
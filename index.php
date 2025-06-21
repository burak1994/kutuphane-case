<?php
#index.php
use Helpers\AppHelpers;
use Helpers\LoggerHelpers;

require_once 'vendor/autoload.php';
require_once 'config/Database.php';


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

# Get the request URI and parse it
$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace('/kutuphane-case/', '', $uri);
$path = parse_url($uri, PHP_URL_PATH);
$path = trim($path, '/');
$segments = explode('/', $path);
$method = $_SERVER['REQUEST_METHOD'];




# Connect to the database
$db = (new Database())->getConnection();

if ($segments[0] !== 'api') {
    AppHelpers::json(['success' => false, 'message' => 'Invalid endpoint'], 404);
}
# Check the ID and sub parameters
$id = $segments[2] ?? null;
$sub = $segments[3] ?? null;
$resource = $segments[1] ?? null;

# Define endpoint that do not require authentication
$publicEndpoints = ['login'];

# Check token if the resource is in the login endpoints
if (!in_array($resource, $publicEndpoints) && $_ENV['APP_ENV'] == 'production') {
    AppHelpers::checkJWT();
}


$res = null;

switch ($resource) {
    case 'books':
        $controller = new Controllers\BookController($db);
        #  GET requests for books
        if ($id != 'search' && $id != ''  && $method == 'GET') {
           $res = $controller->getById($id);
        } else {
            AppHelpers::routeHandler($controller, $method, $id, $_GET);
        }
        break;
    case 'authors':
        $controller = new Controllers\AuthorController($db);
        #  GET requests for authors
        if ($id != '' && $sub == 'books' && $method == 'GET') {
            $controller->getAuthorsBooks($id);
        } elseif ($id == '') {
            AppHelpers::routeHandler($controller, $method, $id, $_GET);
        } else {
            AppHelpers::json(['success' => false, 'message' => 'Invalid Request'], 404);
        }
        break;

    case 'categories':
        $controller = new Controllers\CategoryController($db);
        #  GET requests for categories
        AppHelpers::routeHandler($controller, $method, $id);
        break;
    case 'login':
        if ($method == 'POST') {
            $controller = new Controllers\LoginController();
            #  POST requests for login
            $controller->login();
        }
        break;

    default:
        LoggerHelpers::info('' . $resource . ' endpoint not found');
        AppHelpers::json(['success' => false, 'message' => 'Resource not found'], 404);
        break;
}
if ($res !== null) {
    http_response_code($res['status']);
    echo json_encode($res['body']);
}
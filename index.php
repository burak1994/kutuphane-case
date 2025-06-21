<?php
#index.php
use Helpers\AppHelpers;
use Helpers\LoggerHelpers;
use Controllers\BookController;
use Controllers\AuthorController;
use Controllers\CategoryController;
use Controllers\LoginController;

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
$id       = $segments[2] ?? null;
$sub      = $segments[3] ?? null;
$resource = $segments[1] ?? null;

# Define endpoint that do not require authentication
$publicEndpoints = ['login'];

# Check token if the resource is in the login endpoints and the environment is production
if (!in_array($resource, $publicEndpoints) && $_ENV['APP_ENV'] == 'production') {
    $jwtCheck = AppHelpers::checkJWT();
    if ($jwtCheck['status'] ?? null) {
        http_response_code($jwtCheck['status']);
        echo json_encode($jwtCheck['body']);
        exit;
    }
}

# Define the controller map
$controllerMap = [
    'books' => BookController::class,
    'authors' => AuthorController::class,
    'categories' => CategoryController::class,
    'login' => LoginController::class,
];

if (array_key_exists($resource, $controllerMap)) {
    $controller = new $controllerMap[$resource]($db);
    $res = null;

    switch ($resource) {
        case 'books':
            #  GET requests for books
            # If the request is for a specific book by ID
            if ($id != 'search' && $id != ''  && $method == 'GET') {
                $res = $controller->getById($id);
            } else {
                # If the request is for all books or search via isbn or title
                AppHelpers::routeHandler($controller, $method, $id, $_GET);
            }
            break;
        case 'authors':
            #  GET requests for authors
            if ($id != '' && $sub == 'books' && $method == 'GET') {
               $res = $controller->getAuthorsBooks($id);
            } elseif ($id == '') {
                AppHelpers::routeHandler($controller, $method, $id, $_GET);
            } else {
                AppHelpers::json(['success' => false, 'message' => 'Invalid Request'], 404);
            }
            break;

        case 'categories':
            #  GET requests for categories
            AppHelpers::routeHandler($controller, $method, $id);
            break;
        case 'login':
            if ($method == 'POST') {
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
}

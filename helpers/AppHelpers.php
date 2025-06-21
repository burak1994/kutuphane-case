<?php
# helpers/Helpers.php
namespace Helpers;
use Helpers\JwtHelpers;
use Helpers\LoggerHelpers;

class AppHelpers {
    
     # Removes HTML tags and converts special characters to HTML entities
    public static function cleanArray(array $data): array {
        foreach ($data as $key => $val) {
            if (is_string($val)) {
                $data[$key] = htmlspecialchars(strip_tags($val), ENT_QUOTES);
            }
        }
        return $data;
    }

    # Clean single string value by sanitizing to prevent XSS attacks
     public static function cleanString(string $val): string {
        return htmlspecialchars(strip_tags($val), ENT_QUOTES);
    }

    # Send JSON response with specified HTTP status code and terminate script execution
    public static function json($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }

    # Send paginated JSON response with data, pagination info and message
     public static function paginated($data, $pagination, $message = 'success', $status = 200): array {
        return  ['status'=>$status,'body'=>[
            'success' => true,
            'data' => $data,
            'message' => $message,
            'pagination' => $pagination
        ]];
     }

    # Handle HTTP routing based on request method (GET, POST, PUT, DELETE)
    public static function routeHandler($controller, $method, $id, $queryParams = []): void {
        $res = null;
    
        switch ($method) {
            case 'GET':
                # If the path is 'search' and query parameter 'q' is provided search by query, if the path is empty get all data
                $res = ($id == 'search' && !empty($queryParams['q']))
                    ? $controller->searchByQuery($queryParams['q'])
                    : $controller->getAll();
                break;
    
            case 'POST':
                $res = $controller->addNewData();
                break;
    
            case 'PUT':
                if (!empty($id)) {
                    $res = $controller->updateData($id);
                } else {
                    $res = ['status' => 400, 'body' => ['success' => false, 'message' => 'ID required']];
                    LoggerHelpers::warning('updateData@AppHelpers ID required for DELETE request');

                }
                break;
    
            case 'DELETE':
                if (!empty($id)) {
                    $res = $controller->deleteData($id);
                } else {
                    $res = ['status' => 400, 'body' => ['message' => 'ID required']];
                    LoggerHelpers::warning('deleteData@AppHelpers ID required for DELETE request');

                }
                break;
    
            default:
                LoggerHelpers::warning('@AppHelpers Method not allowed: ' . $method);
                $res = ['status' => 405, 'body' => ['message' => 'Method Not Allowed']];
        }
    
        if ($res !== null) {
            http_response_code($res['status']);
            echo json_encode($res['body']);
        }
    }
    
    
    # Convert MySQL error codes to user-friendly error messages
     private static function setTheErrorMessage(int $code,string $message): string
    {
        switch ($code) {
            case 1062:
                return $message;
            case 1451:
                return 'Foreign key constraint fails.';
            case 1452:
                return 'Author or category not found.';
            case 1048:
                return 'Column cannot be null.';
            case 1049:
                return 'Unknown database.';
            case 1146:
                return 'Table does not exist.';
            case 1054:
                return 'Unknown column.';
            case 1264:
                return 'Data too long for column. (Check Page Count, ISBN, Title, etc.)';
            default:
                return 'Unknown SQL error.';
        }
    }

    # Get user-readable error message from SQL error code
     public static function getSqlErrorMessage(int $code,string $message =''): string
    {
        LoggerHelpers::error('getSqlErrorMessage@AppHelpers SQL Error Code: ' . $code . ' Message: ' . $message);
        return self::setTheErrorMessage($code,$message) ?? 'Unknown column.'.$code;
    }

    # Validate if provided ID is a positive integer
     public static function isValidId($id): array
    {
        $id = (string)$id;
    
        if (!ctype_digit($id) || (int)$id <= 0) {
            LoggerHelpers::error('isValidId@AppHelpers Invalid ID: ' . $id);
            return ['success' => false, 'message' => 'Invalid ID', 'id' => null];
        }
        return ['success' => true, 'id' => (int)$id];
    }

    # Validate JWT token from Authorization header
    public static function checkJWT(): bool
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authorization header missing']);
            LoggerHelpers::error('checkJWT@AppHelpers Authorization header missing');

            exit;
        }
    
        $jwt = explode(' ', $headers['Authorization'])[1] ?? '';
        if (empty($jwt)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token missing']);
            LoggerHelpers::error('checkJWT@AppHelpers Token missing');

            exit;
        }
    
        $decoded = JwtHelpers::validateToken($jwt);
    
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
            LoggerHelpers::error('checkJWT@AppHelpers Invalid or expired token');

            exit;
        }
    
        return true;
    }

    
}
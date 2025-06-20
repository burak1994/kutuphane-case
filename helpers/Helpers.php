<?php
class AppHelpers {
    // check the request method (array) 
    public static function cleanArray(array $data): array {
        foreach ($data as $key => $val) {
            if (is_string($val)) {
                $data[$key] = htmlspecialchars(strip_tags($val), ENT_QUOTES);
            }
        }
        return $data;
    }

    // check the request method (string)
    public static function cleanString(string $val): string {
        return htmlspecialchars(strip_tags($val), ENT_QUOTES);
    }

    public static function json($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
    public static function paginated($data, $pagination, $message = 'success', $status = 200): void {
        http_response_code($status);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'pagination' => $pagination
        ]);
        exit();
    }

    public static function routeHandler($controller, $method, $id,  $queryParams = []): void {
        switch ($method) {
            case 'GET':
                 # if the path is 'search' and query parameter 'q' is provided search by query,  # if the path is empty get all data
                $id == 'search' && !empty($queryParams['q']) ? $controller->searchByQuery($queryParams['q']): $controller->getAll();
                break;
            case 'POST':
                $controller->addNewData();
                break;
    
            case 'PUT':
                !empty($id) ?  $controller->updateData($id) :self::json(['success' => false, 'message' => 'ID required'], 400);
                break;
    
            case 'DELETE':
                (!empty($id)) ? $controller->deleteData($id) :self::json(['message' => 'ID required'], 400);
                break;
    
            default:
                self::json(['message' => 'Method Not Allowed'], 405);
        }
    }
    

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

     public static function getSqlErrorMessage(int $code,string $message =''): string
     {
         return self::setTheErrorMessage($code,$message) ?? 'Unknown column.'.$code;
     }

     public static function isValidId($id): array
     {
         $id = (string)$id;
     
         if (!ctype_digit($id) || (int)$id <= 0) {
             return ['success' => false, 'message' => 'Invalid ID', 'id' => null];
         }
         return ['success' => true, 'id' => (int)$id];
     }
     
}

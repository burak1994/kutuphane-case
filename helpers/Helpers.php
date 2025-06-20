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
    

    private static array $sqlErrorCodes = [
        1062 => 'Duplicate entry. ISBN must be unique.',
        1451 => 'Foreign key constraint fails.',
        1452 => 'Author  or category not found',
        1048 => 'Column cannot be null.',
        1049 => 'Unknown database.',
        1146 => 'Table doesn’t exist.',
        1054 => 'Unknown column.',
        1264 => 'Data too long for column. ( check the length of the data *Page Count, ISBN, Title)',
     ];

     public static function getSqlErrorMessage(int $code): string
     {
         return self::$sqlErrorCodes[$code] ?? 'Unknown column.'.$code;
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

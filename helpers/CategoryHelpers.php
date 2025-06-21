<?php
# helpers/CategoryHelpers.php
namespace Helpers;
use Helpers\LoggerHelpers;
 class CategoryHelpers
{
    public static function filterTheData(array $input): array {
        $response = ['success' => true];
        
        if (!isset($input['name']) || !isset($input['description']) ||
            empty(trim($input['name'])) || empty(trim($input['description']))) {
            $response = ['success' => false, 'message' => 'All fields are required'];
            LoggerHelpers::warning('filterTheData@CategoryHelpers All fields are required');

        }
        
        return $response;
    }
    
}
 ?>
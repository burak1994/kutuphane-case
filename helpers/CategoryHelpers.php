<?php
# helpers/CategoryHelpers.php
class CategoryHelpers
{
    public static function filterTheData(array $input) : array{
        $response = ['success' => true];
        # check if inputs are  valid
        if (($input['name'] == '' ||  $input['description'] == '')) {
            $response = ['success' => false, 'message' => 'All fields are required'];
            
        }
        return $response;
    
    }
    
}

?>
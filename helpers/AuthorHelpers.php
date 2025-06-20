<?php 
# helpers/AuthorHelpers.php
namespace Helpers;
class AuthorHelpers
{

    public static function filterTheData(array $input) : array{
        $response = ['success' => true];
        # check if inputs are  valid
        if ($input['name'] == '' || $input['email'] == '') {
            $response = ['success' => false, 'message' => 'All fields are required'];
            
        }
    
        #  email check 
        if (!filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL) ) {
            $response = ['success' => false, 'message' => 'Email must be a valid email address'];
            
        }
        return $response;
    
    }
    
}

?>
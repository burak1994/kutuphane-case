<?php 
class BookHelpers
{

    public static function filterTheData(array $input) : array{
        $response = ['success' => true];
        # check if inputs are  valid
        if (!isset($input['title'], $input['isbn'], $input['author_id'], $input['category_id'], $input['publication_year'], $input['page_count'])) {
            $response = ['success' => false, 'message' => 'All fields are required'];
            
        }
    
        # ISBN check 
        if (!preg_match('/^\d{13}$/', $input['isbn'])) {
            $response = ['success' => false, 'message' => 'ISBN must be 13 digits'];
            
        }
    
        # Nuember fields check
        if (
            !is_numeric($input['author_id']) || 
            !is_numeric($input['category_id']) || 
            !is_numeric($input['publication_year']) || 
            !is_numeric($input['page_count'])
        ) {
            $response = ['success' => false, 'message' => 'Author ID, Category ID, Publication Year and Page Count must be numeric'];
           
        }
    
        # publication year check
        if (!preg_match('/^\d{4}$/', $input['publication_year'])) {
            $response = ['success' => false, 'message' => 'Publication Year must be a 4-digit year'];
           
        }
        return $response;
    
    }
    


}

?>
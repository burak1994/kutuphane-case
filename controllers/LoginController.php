<?php
require_once __DIR__ . '/../helpers/JWTHelpers.php';
use Dotenv\Dotenv;


class LoginController
{
 
    public function __construct()
    {
       
         $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
         $dotenv->load();
 
    }

    public function login()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        $filteredData = AppHelpers::cleanArray($input);

        if ($filteredData['username'] == $_ENV['APP_ADMIN'] && $filteredData['password'] == $_ENV['APP_PASS']) {
            $payload = [
                'user_id' => 1,
                'username' => $filteredData['username']
            ];
            echo json_encode([
                'success' => true,
                'token' => JwtHelper::createToken($payload)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    }
}

?>
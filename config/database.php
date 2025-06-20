<?php

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Database
{
    private $pdo;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // get .env values 
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'];
 
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        # PDO configs
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            # Handle the error based on the environment
            if ($_ENV['APP_ENV'] === 'development') {
                die("Error : " . $e->getMessage());
            } else {
                error_log("DB Error: " . $e->getMessage());  
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed. Please try again later.'
                ]);
                die();
            }
         }
    }

    // get the pdo conenction
    public function getConnection()
    {
        return $this->pdo;
    }
}

<?php
# helpers/JwtHelper.php
namespace Helpers;

 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class JwtHelpers
{
    private static string $secretKey;

    # Initialize the secret key from the .env file
    public static function init(): void
    {
        if (!isset(self::$secretKey)) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            self::$secretKey = $_ENV['APP_SECRET'];
        }
    }

    # Create a JWT Token
    public static function createToken(array $payload, int $expireInSeconds = 3600): string
    {
        self::init();  

        $now = time();
        $payload = array_merge($payload, [
            'iat' => $now,
            'exp' => $now + $expireInSeconds
        ]);

        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    public static function validateToken(string $token)
    {
        self::init();  

        try {
            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            return false;
        }
    }
}
 
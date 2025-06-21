<?php
# helpers/JwtHelper.php
namespace Helpers;

 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;
use Helpers\LoggerHelpers;

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

    public static function validateToken(string $token): array
    {
        self::init();

        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            return ['success' => true, 'payload' => $decoded];
        } catch (\Firebase\JWT\ExpiredException $e) {
            LoggerHelpers::error('validateToken@JwtHelpers Token expired: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Token expired'];
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            LoggerHelpers::error('validateToken@JwtHelpers Invalid signature: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Invalid token signature'];
        } catch (\Firebase\JWT\BeforeValidException $e) {
            LoggerHelpers::error('validateToken@JwtHelpers Token not yet valid: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Token not yet valid'];
        } catch (\Exception $e) {
            LoggerHelpers::error('validateToken@JwtHelpers Validation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Invalid token'];
        }
    }
}
 
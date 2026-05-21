<?php
/**
 * MBSL Insurance - PDO Database Connection
 * Singleton pattern for efficient connection management.
 */
declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;

    // ── Connection Settings ──────────────────────────────────
    private static string $host    = 'localhost';
    private static string $dbName = 'mbsl_new_db';
    private static string $user    = 'root';      // Change to your DB user
    private static string $pass    = '';           // Change to your DB password
    private static string $charset = 'utf8mb4';

    /**
     * Returns the singleton PDO instance.
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::$host,
                self::$dbName,
                self::$charset
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_FOUND_ROWS   => true,
            ];
            try {
                self::$instance = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                // Return a JSON error without exposing credentials
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed. Please contact the system administrator.'
                ]);
                exit;
            }
        }
        return self::$instance;
    }

    // Prevent instantiation and cloning
    private function __construct() {}
    private function __clone() {}
}

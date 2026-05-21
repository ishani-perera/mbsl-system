<?php
/**
 * MBSL Insurance - Authentication Controller
 * Handles: Login, Logout, Check-Auth
 * Returns: JSON responses
 */
declare(strict_types=1);

// ── Turn off error output so it cannot break JSON ───────────
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// ── CORS Headers ─────────────────────────────────────────────
header('Content-Type: application/json; charset=UTF-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ── Session ───────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    // Ensure session cookie is sent correctly in all environments
    session_set_cookie_params([
        'lifetime' => 86400,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ── Load Dependencies ─────────────────────────────────────────
require_once __DIR__ . '/../config/Database.php';

// ── Route Request ─────────────────────────────────────────────
$action = $_GET['action'] ?? '';

match ($action) {
    'login'        => Auth::login(),
    'logout'       => Auth::logout(),
    'check'        => Auth::checkAuth(),
    'reset_limit'  => Auth::resetRateLimit(),
    default        => Auth::respond(false, 'Unknown action.', 400)
};

// ── Auth Controller Class ─────────────────────────────────────
class Auth {

    /**
     * POST /api/controllers/Auth.php?action=login
     * Body: { "email": "...", "password": "..." }
     */
    public static function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::respond(false, 'Method not allowed.', 405);
        }

        // Read JSON body
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);

        // Fallback to $_POST if JSON is not sent
        $email    = trim((string)($data['email']    ?? $_POST['email']    ?? ''));
        $password = (string)($data['password'] ?? $_POST['password'] ?? '');

        // ── Validation ──────────────────────────────────────
        if (empty($email) || empty($password)) {
            self::respond(false, 'Email and password are required.', 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::respond(false, 'Invalid email format.', 422);
        }

        // ── Rate Limiting ────────────────────────────────────
        // Auto-reset if the lockout window (60 seconds) has already passed
        $lockoutWindow = 60;  // seconds before counter resets
        $maxAttempts   = 10;  // attempts before temporary lockout

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt']   = time();
        }

        // Reset counter if lockout window has expired
        if ((time() - (int)($_SESSION['last_attempt'] ?? 0)) >= $lockoutWindow) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt']   = time();
        }

        if ((int)$_SESSION['login_attempts'] >= $maxAttempts) {
            $elapsed = time() - (int)$_SESSION['last_attempt'];
            $wait    = $lockoutWindow - $elapsed;
            if ($wait > 0) {
                self::respond(false, "Too many attempts. Please wait {$wait} seconds.", 429);
            } else {
                // Window passed — reset and allow
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_attempt']   = time();
            }
        }

        // ── Database Lookup ──────────────────────────────────
        try {
            $pdo  = Database::getConnection();
            $stmt = $pdo->prepare(
                "SELECT id, name, email, password_hash, role, profile_pic
                 FROM users
                 WHERE email = :email
                 LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            self::respond(false, 'Database error. Please try again.', 500);
        }

        // ── Verify Password ──────────────────────────────────
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt'] = time();
            self::respond(false, 'Invalid email or password.', 401);
        }

        // ── Success: Create Session ──────────────────────────
        $_SESSION['login_attempts'] = 0;
        session_regenerate_id(true); // Prevent session fixation

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
        // Ensure session is written immediately
        session_write_close();
        session_start();
        self::respond(true, 'Login successful.', 200, [
            'user' => [
                'id'          => (int)$user['id'],
                'name'        => $user['name'],
                'email'       => $user['email'],
                'role'        => $user['role'],
                'profile_pic' => $user['profile_pic'],
            ]
        ]);
    }

    /**
     * POST /api/controllers/Auth.php?action=logout
     */
    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        self::respond(true, 'Logged out successfully.', 200);
    }

    /**
     * GET /api/controllers/Auth.php?action=check
     */
    public static function checkAuth(): void {
        if (isset($_SESSION['user'])) {
            self::respond(true, 'Authenticated.', 200, ['user' => $_SESSION['user']]);
        } else {
            self::respond(false, 'Not authenticated.', 401);
        }
    }

    /**
     * GET /api/controllers/Auth.php?action=reset_limit
     * Clears the rate-limit counter (useful during development/testing)
     */
    public static function resetRateLimit(): void {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt']   = time();
        self::respond(true, 'Rate limit counter has been reset. You can log in now.', 200);
    }

    /**
     * Outputs a JSON response and terminates.
     */
    public static function respond(
        bool   $success,
        string $message,
        int    $httpCode = 200,
        array  $data = []
    ): never {
        http_response_code($httpCode);
        $payload = ['success' => $success, 'message' => $message];
        if (!empty($data)) {
            $payload = array_merge($payload, $data);
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

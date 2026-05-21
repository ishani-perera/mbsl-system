<?php
/**
 * MBSL Insurance - Admin Setup Script
 * Run this ONCE after importing database.sql
 * URL: http://yourdomain.com/setup_admin.php
 * DELETE this file after running it!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ── Database Configuration ──────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'insurance_policy_db');
define('DB_USER', 'root');       // Change to your DB username
define('DB_PASS', '');           // Change to your DB password
define('DB_CHARSET', 'utf8mb4');

// ── Security Token (prevents accidental re-runs) ────────────
$token = $_GET['token'] ?? '';
$expected = 'mbsl_setup_2024'; // Change this token

if ($token !== $expected) {
    http_response_code(403);
    die('<h2 style="color:red">403 Forbidden</h2><p>Provide the correct setup token as a GET parameter: <code>?token=mbsl_setup_2024</code></p>');
}

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // Check if admin already exists
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->execute(['admin@mbsl.com']);
    if ($check->fetch()) {
        echo '<h2 style="color:orange">⚠️ Admin Already Exists</h2><p>An admin account with email <strong>admin@mbsl.com</strong> already exists. No changes made.</p>';
        echo '<p><a href="index.html">Go to Login</a></p>';
        exit;
    }

    // Securely hash the password using PHP's password_hash()
    $plainPassword = 'admin123';
    $passwordHash  = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);

    // Insert the admin user
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, role, created_at)
        VALUES (:name, :email, :hash, 'Admin', NOW())
    ");
    $stmt->execute([
        ':name'  => 'Admin User',
        ':email' => 'admin@mbsl.com',
        ':hash'  => $passwordHash,
    ]);

    $adminId = $pdo->lastInsertId();

    // Insert sample policies
    $policies = [
        ['Comprehensive Auto Shield',    'Motor',    1240.00, 'Full comprehensive motor coverage with roadside assistance.',  'Active'],
        ['Family Health Platinum',        'Health',   4800.00, 'Global family health coverage with specialist consultations.',  'Active'],
        ['Universal Life Protection',     'Life',     2100.00, 'Whole life protection with investment component.',              'Active'],
        ['Motorcycle Essential',          'Motor',     450.00, 'Essential motorcycle coverage including third-party liability.', 'Active'],
        ['Executive Health Guard',        'Health',   7200.00, 'Premium corporate health plan for executives.',                 'Active'],
        ['Luxury Estate Premium Coverage','Property', 12450.00,'Comprehensive property coverage for high-value estates.',       'Active'],
        ['Cyber Shield Enterprise',       'Cyber',    3800.00, 'Enterprise-grade cyber liability and data breach coverage.',    'Active'],
        ['Global Travel Elite',           'Travel',    890.00, 'Worldwide travel insurance with emergency evacuation.',        'Draft'],
    ];

    $policyStmt = $pdo->prepare("
        INSERT INTO policies (title, category, premium_amount, description, status, user_id)
        VALUES (:title, :cat, :amount, :desc, :status, :uid)
    ");
    foreach ($policies as $p) {
        $policyStmt->execute([
            ':title'  => $p[0],
            ':cat'    => $p[1],
            ':amount' => $p[2],
            ':desc'   => $p[3],
            ':status' => $p[4],
            ':uid'    => $adminId,
        ]);
    }

    echo '<!DOCTYPE html><html><head><title>MBSL Setup Complete</title>
    <style>
      body{font-family:Segoe UI,sans-serif;background:#f4f7fe;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
      .box{background:#fff;border-radius:1rem;padding:40px;max-width:500px;box-shadow:0 8px 30px rgba(0,0,0,0.1);text-align:center;}
      h2{color:#10b981;font-size:1.5rem;margin-bottom:12px}
      p{color:#64748b;margin-bottom:8px;font-size:0.92rem}
      .creds{background:#f0f9ff;border:1px solid #bae6fd;border-radius:0.6rem;padding:14px;margin:20px 0;text-align:left;}
      .creds div{font-size:0.88rem;color:#0369a1;margin-bottom:4px}
      .btn{display:inline-block;padding:12px 28px;background:#0a2558;color:#fff;border-radius:0.6rem;text-decoration:none;font-weight:600;margin-top:16px}
      .warn{background:#fef2f2;border:1px solid #fecaca;border-radius:0.6rem;padding:12px;color:#dc2626;font-size:0.85rem;margin-top:16px}
    </style></head><body>
    <div class="box">
      <h2>✅ Setup Complete!</h2>
      <p>Admin account created successfully.</p>
      <p><strong>' . count($policies) . '</strong> sample policies inserted.</p>
      <div class="creds">
        <div><strong>Email:</strong> admin@mbsl.com</div>
        <div><strong>Password:</strong> admin123</div>
        <div><strong>Admin ID:</strong> #' . $adminId . '</div>
      </div>
      <div class="warn">⚠️ <strong>Security Warning:</strong> Delete or rename this file (setup_admin.php) immediately after setup!</div>
      <a href="index.html" class="btn">Go to Login →</a>
    </div></body></html>';

} catch (PDOException $e) {
    http_response_code(500);
    echo '<h2 style="color:red">Database Error</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p>Check your DB_HOST, DB_USER, DB_PASS, and DB_NAME constants at the top of this file.</p>';
}

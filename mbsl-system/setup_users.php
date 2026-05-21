<?php
/**
 * MBSL Insurance - User & Sample Data Setup Script
 * Creates:
 *  - Admin: admin@insurance.com (Password: admin123)
 *  - User: user@insurance.com (Password: user123)
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/api/config/Database.php';

try {
    $pdo = Database::getConnection();

    // Delete existing test users to prevent duplicate email issues and ensure clean state
    $emails = ['admin@insurance.com', 'user@insurance.com'];
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
    foreach ($emails as $email) {
        $deleteStmt->execute([$email]);
    }

    // Insert Admin User
    $adminPasswordHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    $userInsertStmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, role, created_at)
        VALUES (:name, :email, :hash, :role, NOW())
    ");
    
    $userInsertStmt->execute([
        ':name'  => 'Admin User',
        ':email' => 'admin@insurance.com',
        ':hash'  => $adminPasswordHash,
        ':role'  => 'Admin'
    ]);
    $adminId = $pdo->lastInsertId();

    // Insert Normal User
    $normalPasswordHash = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);
    $userInsertStmt->execute([
        ':name'  => 'Normal User',
        ':email' => 'user@insurance.com',
        ':hash'  => $normalPasswordHash,
        ':role'  => 'User'
    ]);
    $normalUserId = $pdo->lastInsertId();

    // Insert sample policies for Admin
    $adminPolicies = [
        ['Comprehensive Auto Shield',    'Motor',    1240.00, 'Full comprehensive motor coverage with roadside assistance.',  'Active'],
        ['Family Health Platinum',        'Health',   4800.00, 'Global family health coverage with specialist consultations.',  'Active'],
        ['Universal Life Protection',     'Life',     2100.00, 'Whole life protection with investment component.',              'Active'],
        ['Luxury Estate Premium Coverage','Property', 12450.00,'Comprehensive property coverage for high-value estates.',       'Active'],
        ['Cyber Shield Enterprise',       'Cyber',    3800.00, 'Enterprise-grade cyber liability and data breach coverage.',    'Active'],
    ];

    // Insert sample policies for Normal User
    $normalPolicies = [
        ['Motorcycle Essential',          'Motor',     450.00, 'Essential motorcycle coverage including third-party liability.', 'Active'],
        ['Executive Health Guard',        'Health',   7200.00, 'Premium corporate health plan for executives.',                 'Active'],
        ['Global Travel Elite',           'Travel',    890.00, 'Worldwide travel insurance with emergency evacuation.',        'Draft'],
    ];

    $policyStmt = $pdo->prepare("
        INSERT INTO policies (title, category, premium_amount, description, status, user_id)
        VALUES (:title, :cat, :amount, :desc, :status, :uid)
    ");

    foreach ($adminPolicies as $p) {
        $policyStmt->execute([
            ':title'  => $p[0],
            ':cat'    => $p[1],
            ':amount' => $p[2],
            ':desc'   => $p[3],
            ':status' => $p[4],
            ':uid'    => $adminId,
        ]);
    }

    foreach ($normalPolicies as $p) {
        $policyStmt->execute([
            ':title'  => $p[0],
            ':cat'    => $p[1],
            ':amount' => $p[2],
            ':desc'   => $p[3],
            ':status' => $p[4],
            ':uid'    => $normalUserId,
        ]);
    }

    echo '<!DOCTYPE html><html><head><title>MBSL User Setup Complete</title>
    <style>
      body{font-family:"Segoe UI",sans-serif;background:#f4f7fe;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
      .box{background:#fff;border-radius:1rem;padding:40px;width:100%;max-width:550px;box-shadow:0 8px 30px rgba(0,0,0,0.08);text-align:center;}
      h2{color:#10b981;font-size:1.5rem;margin-bottom:16px}
      p{color:#64748b;margin-bottom:12px;font-size:0.92rem}
      .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:20px 0;}
      .creds{background:#f0f9ff;border:1px solid #bae6fd;border-radius:0.6rem;padding:16px;text-align:left;}
      .creds.user{background:#f0fdf4;border-color:#bbf7d0;}
      .creds h3{font-size:0.95rem;margin-top:0;margin-bottom:10px;color:#0369a1;}
      .creds.user h3{color:#15803d;}
      .creds div{font-size:0.85rem;color:#0369a1;margin-bottom:4px}
      .creds.user div{color:#15803d;}
      .btn{display:inline-block;padding:12px 28px;background:#0a2558;color:#fff;border-radius:0.6rem;text-decoration:none;font-weight:600;margin-top:16px;transition:all 0.2s}
      .btn:hover{background:#1565c0;}
    </style></head><body>
    <div class="box">
      <h2>✅ Setup Complete!</h2>
      <p>Both Admin and Normal User accounts have been configured successfully.</p>
      <div class="grid">
        <div class="creds">
          <h3>Administrator</h3>
          <div><strong>Email:</strong> admin@insurance.com</div>
          <div><strong>Password:</strong> admin123</div>
          <div><strong>ID:</strong> #' . $adminId . '</div>
        </div>
        <div class="creds user">
          <h3>Normal User</h3>
          <div><strong>Email:</strong> user@insurance.com</div>
          <div><strong>Password:</strong> user123</div>
          <div><strong>ID:</strong> #' . $normalUserId . '</div>
        </div>
      </div>
      <p>Inserted ' . count($adminPolicies) . ' policies for Admin and ' . count($normalPolicies) . ' policies for User.</p>
      <a href="index.html" class="btn">Go to Login →</a>
    </div></body></html>';

} catch (PDOException $e) {
    http_response_code(500);
    echo '<h2 style="color:red">Database Error</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}

<?php
/**
 * MBSL Insurance - Fix Demo Credentials
 * Run this script to reset demo user passwords
 * URL: http://yourdomain.com/fix-demo-credentials.php
 * DELETE this file after running successfully!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/api/config/Database.php';

try {
    $pdo = Database::getConnection();

    // Generate correct password hashes for demo credentials
    $adminPasswordHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    $userPasswordHash = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);

    // Update Admin User
    $stmt = $pdo->prepare("
        UPDATE users 
        SET password_hash = :hash 
        WHERE email = :email
    ");
    
    $stmt->execute([
        ':email' => 'admin@insurance.com',
        ':hash' => $adminPasswordHash
    ]);
    $adminUpdated = $stmt->rowCount();

    // Update Normal User
    $stmt->execute([
        ':email' => 'user@insurance.com',
        ':hash' => $userPasswordHash
    ]);
    $userUpdated = $stmt->rowCount();

    echo '<div style="font-family:Arial;max-width:600px;margin:50px auto;padding:20px;background:#f0fdf4;border:2px solid #10b981;border-radius:8px;">';
    echo '<h2 style="color:#10b981;margin-top:0">✓ Credentials Fixed Successfully!</h2>';
    echo '<p><strong>Admin User Updated:</strong> ' . ($adminUpdated > 0 ? 'YES' : 'No changes (already correct)') . '</p>';
    echo '<p><strong>Normal User Updated:</strong> ' . ($userUpdated > 0 ? 'YES' : 'No changes (already correct)') . '</p>';
    echo '<hr style="border:none;border-top:1px solid #10b981;margin:20px 0">';
    echo '<h3 style="margin-top:20px">Demo Credentials:</h3>';
    echo '<p><strong>Admin Email:</strong> admin@insurance.com<br>';
    echo '<strong>Admin Password:</strong> admin123<br><br>';
    echo '<strong>User Email:</strong> user@insurance.com<br>';
    echo '<strong>User Password:</strong> user123</p>';
    echo '<hr style="border:none;border-top:1px solid #10b981;margin:20px 0">';
    echo '<p style="color:#7c2d12;background:#fef2f2;padding:12px;border-radius:4px;border:1px solid #fca5a5">';
    echo '<strong>⚠️ Security Warning:</strong> DELETE this file (fix-demo-credentials.php) immediately after use!';
    echo '</p>';
    echo '<p><a href="index.html" style="display:inline-block;padding:10px 20px;background:#10b981;color:white;text-decoration:none;border-radius:4px;margin-top:15px">Go to Login</a></p>';
    echo '</div>';

} catch (Exception $e) {
    echo '<div style="font-family:Arial;max-width:600px;margin:50px auto;padding:20px;background:#fef2f2;border:2px solid #ef4444;border-radius:8px;">';
    echo '<h2 style="color:#ef4444;margin-top:0">✗ Error Fixing Credentials</h2>';
    echo '<p style="color:#991b1b"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p style="color:#7c2d12">Make sure:</p>';
    echo '<ul style="color:#7c2d12">';
    echo '<li>Database server (MySQL) is running</li>';
    echo '<li>Database credentials in api/config/Database.php are correct</li>';
    echo '<li>Database and users table have been created</li>';
    echo '</ul>';
    echo '</div>';
}
?>

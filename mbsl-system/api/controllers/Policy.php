<?php

declare(strict_types=1);

// Errors must NOT be displayed — they break JSON output. Log them instead.
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
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ── Session ───────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/../config/Database.php';

$action = $_GET['action'] ?? 'list';

try {

    $pdo = Database::getConnection();

    switch ($action) {

        case 'list':

            $search = trim($_GET['q'] ?? '');
            $category = trim($_GET['category'] ?? '');
            $limit = (int)($_GET['limit'] ?? 10);
            $page = (int)($_GET['page'] ?? 1);
            $offset = ($page - 1) * $limit;

            $sql = "
                SELECT 
                    p.id,
                    p.title,
                    p.holder_name,
                    p.category,
                    p.premium_amount,
                    p.description,
                    p.status,
                    p.created_at
                FROM policies p
                WHERE 1=1
            ";

            $params = [];

            if ($search !== '') {
                $sql .= "
                    AND (
                        p.title LIKE :search_title
                        OR p.holder_name LIKE :search_user
                        OR p.category LIKE :search_category
                    )
                ";
                $params[':search_title'] = '%' . $search . '%';
                $params[':search_user'] = '%' . $search . '%';
                $params[':search_category'] = '%' . $search . '%';
            }

            if ($category !== '') {
                $sql .= " AND p.category = :filter_category ";
                $params[':filter_category'] = $category;
            }

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM policies p WHERE 1=1";
            if ($search !== '') {
                $countSql .= " AND (p.title LIKE :search_title OR p.holder_name LIKE :search_user OR p.category LIKE :search_category)";
            }
            if ($category !== '') {
                $countSql .= " AND p.category = :filter_category";
            }

            $countStmt = $pdo->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $totalCount = (int)$countStmt->fetch()['total'];

            $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset ";

            $stmt = $pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            $policies = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Policies retrieved successfully',
                'policies' => $policies,
                'total' => $totalCount,
                'page' => $page,
                'limit' => $limit
            ], JSON_UNESCAPED_UNICODE);

            break;

        case 'get':

            $id = (int)($_GET['id'] ?? 0);

            if ($id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid policy ID'
                ]);
                exit;
            }

            $stmt = $pdo->prepare("
                SELECT 
                    p.id,
                    p.title,
                    p.holder_name,
                    p.category,
                    p.premium_amount,
                    p.description,
                    p.status,
                    p.user_id,
                    p.created_at,
                    p.updated_at
                FROM policies p
                WHERE p.id = :id
            ");

            $stmt->execute([':id' => $id]);
            $policy = $stmt->fetch();

            if (!$policy) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Policy not found'
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'data' => $policy
            ]);

            break;

        case 'update':

            $id = (int)($_GET['id'] ?? 0);

            if ($id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid policy ID'
                ]);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                $data = $_POST;
            }

            $title = trim($data['title'] ?? '');
            $holder = trim($data['holder_name'] ?? '');
            $category = trim($data['category'] ?? '');
            $premium = (float)($data['premium_amount'] ?? 0);
            $description = trim($data['description'] ?? '');
            $status = trim($data['status'] ?? 'Active');

            if ($title === '' || $category === '') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Title and category are required'
                ]);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE policies
                SET
                    title = :title,
                    holder_name = :holder,
                    category = :category,
                    premium_amount = :premium,
                    description = :description,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id
            ");

            $stmt->execute([
                ':title' => $title,
                ':holder' => $holder,
                ':category' => $category,
                ':premium' => $premium,
                ':description' => $description,
                ':status' => $status,
                ':id' => $id
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Policy updated successfully'
            ]);

            break;

        case 'create':

            // Ensure user is authenticated
            if (!isset($_SESSION['user']['id'])) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'User not authenticated. Please log in first.'
                ]);
                exit;
            }

            // Detect if request is JSON or FormData (multipart)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $data = json_decode(file_get_contents("php://input"), true) ?? [];
            } else {
                // FormData / multipart — data is in $_POST
                $data = $_POST;
            }

            $title = trim($data['title'] ?? '');
            $holder = trim($data['holder_name'] ?? 'Admin User');
            $category = trim($data['category'] ?? '');
            $premium = (float)($data['premium_amount'] ?? 0);
            $description = trim($data['description'] ?? '');
            $status = trim($data['status'] ?? 'Active');

            // Use user_id from session
            $user_id = (int)$_SESSION['user']['id'];

            if ($title === '' || $category === '') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Title and category are required'
                ]);
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO policies
                (
                    title,
                    holder_name,
                    category,
                    premium_amount,
                    description,
                    status,
                    user_id,
                    created_at
                )
                VALUES
                (
                    :title,
                    :holder,
                    :category,
                    :premium,
                    :description,
                    :status,
                    :user_id,
                    NOW()
                )
            ");

            $stmt->execute([
                ':title' => $title,
                ':holder' => $holder,
                ':category' => $category,
                ':premium' => $premium,
                ':description' => $description,
                ':status' => $status,
                ':user_id' => $user_id
            ]);

            $policy_id = $pdo->lastInsertId();

            // Handle file uploads if present
            if (!empty($_FILES['images'])) {
                $uploadDir = __DIR__ . '/../uploads/';
                
                // Create uploads directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $files = $_FILES['images'];
                $fileCount = count($files['name']);

                for ($i = 0; $i < $fileCount; $i++) {
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                        continue; // Skip files with upload errors
                    }

                    $tmpFile = $files['tmp_name'][$i];
                    $fileName = $files['name'][$i];
                    $fileSize = $files['size'][$i];

                    // Validate file size (max 10MB)
                    if ($fileSize > 10 * 1024 * 1024) {
                        continue;
                    }

                    // Generate unique filename
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFileName = 'policy_' . $policy_id . '_' . time() . '_' . uniqid() . '.' . $ext;
                    $uploadPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmpFile, $uploadPath)) {
                        // Save image record to database
                        $imgStmt = $pdo->prepare("
                            INSERT INTO policy_images
                            (policy_id, image_path, sort_order, created_at)
                            VALUES
                            (:policy_id, :image_path, :sort_order, NOW())
                        ");

                        $imgStmt->execute([
                            ':policy_id' => $policy_id,
                            ':image_path' => 'api/uploads/' . $newFileName,
                            ':sort_order' => $i
                        ]);
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Policy created successfully',
                'policy_id' => $policy_id
            ]);

            break;

        case 'stats':

            $totalPolicies = (int)$pdo
                ->query("SELECT COUNT(*) FROM policies")
                ->fetchColumn();

            $totalPremium = (float)$pdo
                ->query("SELECT COALESCE(SUM(premium_amount),0) FROM policies")
                ->fetchColumn();

            $totalCategories = (int)$pdo
                ->query("SELECT COUNT(DISTINCT category) FROM policies")
                ->fetchColumn();

            // Get category breakdown
            $categoryStmt = $pdo->query("
                SELECT 
                    category,
                    COUNT(*) AS count,
                    SUM(premium_amount) AS total_premium
                FROM policies
                GROUP BY category
                ORDER BY count DESC
            ");
            $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get monthly breakdown
            $monthlyStmt = $pdo->query("
                SELECT 
                    DATE_FORMAT(created_at, '%b %Y') AS label,
                    COUNT(*) AS count,
                    SUM(premium_amount) AS total_premium
                FROM policies
                GROUP BY YEAR(created_at), MONTH(created_at)
                ORDER BY created_at DESC
                LIMIT 12
            ");
            $monthly = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);
            $monthly = array_reverse($monthly);

            echo json_encode([
                'success' => true,
                'total_policies' => $totalPolicies,
                'total_premium' => $totalPremium,
                'total_categories' => $totalCategories,
                'total_users' => 2,
                'categories' => $categories,
                'monthly' => $monthly
            ]);

            break;

        case 'delete':

            $id = (int)($_GET['id'] ?? 0);

            $stmt = $pdo->prepare("
                DELETE FROM policies
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $id
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Policy deleted'
            ]);

            break;

        default:

            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }

} catch (Throwable $e) {
    
    // වෙනස්කම 2: Database Error එකක් ආවොත්, ඒක JavaScript වලට පැහැදිලිව අඳුරගන්න 500 status එකක් යවනවා
    // ඒ වගේම මොකක්ද වැරැද්ද කියලා සම්පූර්ණයෙන්ම යවනවා.
    http_response_code(500); 
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage(),
        'error_line' => $e->getLine()
    ]);
}
<?php
/**
 * Database Seeder - Add 10 Sample Policies
 */

require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();

    // Sample policies data
    $policies = [
        [
            'title' => 'Comprehensive Vehicle Shield Pro',
            'holder_name' => 'John Mitchell',
            'category' => 'Motor',
            'premium_amount' => 1500.00,
            'description' => 'Full comprehensive motor coverage with roadside assistance, theft protection, and accidental damage.',
            'status' => 'Active'
        ],
        [
            'title' => 'Family Health Guardian Plus',
            'holder_name' => 'Sarah Williams',
            'category' => 'Health',
            'premium_amount' => 950.00,
            'description' => 'Family health insurance with hospital, specialist, and emergency room coverage up to $500,000.',
            'status' => 'Active'
        ],
        [
            'title' => 'Home Security & Protection',
            'holder_name' => 'Michael Johnson',
            'category' => 'Property',
            'premium_amount' => 1200.00,
            'description' => 'Complete home and property protection against fire, theft, and natural disasters.',
            'status' => 'Active'
        ],
        [
            'title' => 'Business Liability Complete',
            'holder_name' => 'Tech Solutions Inc.',
            'category' => 'Business',
            'premium_amount' => 2500.00,
            'description' => 'Business liability and operational risk insurance with coverage up to $1M.',
            'status' => 'Active'
        ],
        [
            'title' => 'International Travel Safe',
            'holder_name' => 'Emma Davis',
            'category' => 'Travel',
            'premium_amount' => 580.00,
            'description' => 'International travel insurance with emergency medical, evacuation, and baggage coverage.',
            'status' => 'Active'
        ],
        [
            'title' => 'Cyber Security Defense',
            'holder_name' => 'Digital Systems Ltd.',
            'category' => 'Cyber',
            'premium_amount' => 2200.00,
            'description' => 'Comprehensive cyber risk protection for data breaches, ransomware, and cyber attacks.',
            'status' => 'Active'
        ],
        [
            'title' => 'Premium Life Insurance',
            'holder_name' => 'Robert Taylor',
            'category' => 'Life',
            'premium_amount' => 3500.00,
            'description' => 'Long-term life insurance with investment benefits and family protection coverage.',
            'status' => 'Active'
        ],
        [
            'title' => 'Two-Wheeler Essential Plan',
            'holder_name' => 'Alex Kumar',
            'category' => 'Motor',
            'premium_amount' => 650.00,
            'description' => 'Essential motorcycle and two-wheeler coverage including third-party liability.',
            'status' => 'Active'
        ],
        [
            'title' => 'Premium Health Plus',
            'holder_name' => 'Jennifer Brown',
            'category' => 'Health',
            'premium_amount' => 1350.00,
            'description' => 'Premium health coverage with dental, vision, and wellness programs included.',
            'status' => 'Active'
        ],
        [
            'title' => 'Student Travel & Accident',
            'holder_name' => 'University of Excellence',
            'category' => 'Travel',
            'premium_amount' => 320.00,
            'description' => 'Specialized coverage for students including travel, accident, and educational programs.',
            'status' => 'Active'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO policies
        (title, holder_name, category, premium_amount, description, status, user_id, created_at)
        VALUES
        (:title, :holder, :category, :premium, :description, :status, :user_id, DATE_SUB(NOW(), INTERVAL RAND()*90 DAY))
    ");

    foreach ($policies as $policy) {
        $stmt->execute([
            ':title' => $policy['title'],
            ':holder' => $policy['holder_name'],
            ':category' => $policy['category'],
            ':premium' => $policy['premium_amount'],
            ':description' => $policy['description'],
            ':status' => $policy['status'],
            ':user_id' => 1
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Successfully added 10 sample policies to database'
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

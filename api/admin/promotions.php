<?php
require_once '../../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = getDB();
    
    switch ($method) {
        case 'GET':
            handleGet($pdo, $action);
            break;
        case 'POST':
            handlePost($pdo, $action);
            break;
        case 'PUT':
            handlePut($pdo, $action);
            break;
        case 'DELETE':
            handleDelete($pdo, $action);
            break;
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGet($pdo, $action) {
    switch ($action) {
        case 'list':
            getPromotions($pdo);
            break;
        case 'validate':
            validatePromotion($pdo);
            break;
        default:
            getPromotions($pdo);
    }
}

function handlePost($pdo, $action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'create':
            createPromotion($pdo, $input);
            break;
        default:
            throw new Exception('Invalid action');
    }
}

function handlePut($pdo, $action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'update':
            updatePromotion($pdo, $input);
            break;
        default:
            throw new Exception('Invalid action');
    }
}

function handleDelete($pdo, $action) {
    switch ($action) {
        case 'delete':
            deletePromotion($pdo);
            break;
        default:
            throw new Exception('Invalid action');
    }
}

function getPromotions($pdo) {
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 20;
    $offset = ($page - 1) * $limit;
    
    $sql = "SELECT * FROM promotions ORDER BY expire_date DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit, $offset]);
    $promotions = $stmt->fetchAll();
    
    // Get total count
    $countStmt = $pdo->query("SELECT COUNT(*) FROM promotions");
    $total = $countStmt->fetchColumn();
    
    // Mark expired promotions
    foreach ($promotions as &$promotion) {
        $promotion['is_expired'] = strtotime($promotion['expire_date']) < time();
    }
    
    echo json_encode([
        'promotions' => $promotions,
        'total' => $total,
        'page' => $page,
        'pages' => ceil($total / $limit)
    ]);
}

function validatePromotion($pdo) {
    $code = $_GET['code'] ?? '';
    
    if (empty($code)) {
        throw new Exception('Please enter a promotion code');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM promotions WHERE code = ? AND expire_date > datetime('now')");
    $stmt->execute([$code]);
    $promotion = $stmt->fetch();
    
    if (!$promotion) {
        throw new Exception('Invalid or expired promotion code');
    }
    
    echo json_encode([
        'valid' => true,
        'promotion' => $promotion,
        'message' => "Promotion applied: {$promotion['discount']}% off"
    ]);
}

function createPromotion($pdo, $input) {
    $code = $input['code'] ?? '';
    $discount = $input['discount'] ?? 0;
    $expireDate = $input['expire_date'] ?? '';
    
    if (empty($code) || $discount <= 0 || $discount > 100 || empty($expireDate)) {
        throw new Exception('Please provide valid code, discount (1-100), and expiry date');
    }
    
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $expireDate);
    if (!$date || $date->format('Y-m-d') !== $expireDate) {
        throw new Exception('Invalid date format (expected YYYY-MM-DD)');
    }
    
    if (strtotime($expireDate) <= time()) {
        throw new Exception('Expire date must be in the future');
    }
    
    // Check if code already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM promotions WHERE code = ?");
    $stmt->execute([$code]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('This promotion code already exists');
    }
    
    $stmt = $pdo->prepare("INSERT INTO promotions (code, discount, expire_date) VALUES (?, ?, ?)");
    $stmt->execute([$code, $discount, $expireDate]);
    
    $promotionId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Promotion created successfully',
        'promotion_id' => $promotionId
    ]);
}

function updatePromotion($pdo, $input) {
    $id = $input['id'] ?? 0;
    $code = $input['code'] ?? '';
    $discount = $input['discount'] ?? 0;
    $expireDate = $input['expire_date'] ?? '';
    
    if (!$id || empty($code) || $discount <= 0 || $discount > 100 || empty($expireDate)) {
        throw new Exception('Please provide valid code, discount (1-100), and expiry date');
    }
    
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $expireDate);
    if (!$date || $date->format('Y-m-d') !== $expireDate) {
        throw new Exception('Invalid date format (expected YYYY-MM-DD)');
    }
    
    // Check if code already exists (excluding current promotion)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM promotions WHERE code = ? AND id != ?");
    $stmt->execute([$code, $id]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('This promotion code already exists');
    }
    
    $stmt = $pdo->prepare("UPDATE promotions SET code = ?, discount = ?, expire_date = ? WHERE id = ?");
    $stmt->execute([$code, $discount, $expireDate, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Promotion updated successfully'
    ]);
}

function deletePromotion($pdo) {
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        throw new Exception('Promotion ID is required');
    }
    
    $stmt = $pdo->prepare("DELETE FROM promotions WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Promotion deleted successfully'
    ]);
}
?>
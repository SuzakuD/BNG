<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function sendResponse($success, $data = null, $message = '') {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

switch ($action) {
    case 'getStats':
        getDashboardStats();
        break;
    case 'getProducts':
        getProducts();
        break;
    case 'addProduct':
        addProduct();
        break;
    case 'updateProduct':
        updateProduct();
        break;
    case 'deleteProduct':
        deleteProduct();
        break;
    case 'getUsers':
        getUsers();
        break;
    case 'addUser':
        addUser();
        break;
    case 'updateUser':
        updateUser();
        break;
    case 'deleteUser':
        deleteUser();
        break;
    case 'getOrders':
        getOrders();
        break;
    default:
        sendResponse(false, null, 'Invalid action');
}

function getDashboardStats() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products");
        $stmt->execute();
        $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(grand_total) as revenue FROM orders");
        $stmt->execute();
        $orderStats = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalOrders = $orderStats['total'];
        $totalRevenue = $orderStats['revenue'] ?? 0;
        
        $stmt = $conn->prepare("
            SELECT o.*, u.username 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT 5
        ");
        $stmt->execute();
        $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse(true, [
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalUsers' => $totalUsers,
                'totalOrders' => $totalOrders,
                'totalRevenue' => $totalRevenue
            ],
            'recentOrders' => $recentOrders
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error fetching dashboard stats: ' . $e->getMessage());
    }
}

function getProducts() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse(true, ['products' => $products]);
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error fetching products: ' . $e->getMessage());
    }
}

function addProduct() {
    global $conn;
    
    try {
        requireAdminAction();
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
        $imageFileName = null;

        if (isset($_FILES['image']) && is_array($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg','jpeg','png','gif','webp'];
            $originalName = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) {
                sendResponse(false, null, 'Invalid image type');
            }
            $uploadDir = __DIR__ . '/images/products';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                    sendResponse(false, null, 'Failed to create upload directory');
                }
            }
            $imageFileName = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetPath = $uploadDir . '/' . $imageFileName;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                sendResponse(false, null, 'Failed to upload image');
            }
        }
        
        if (empty($name) || $price <= 0 || $stock < 0) {
            sendResponse(false, null, 'Please fill all required fields correctly');
        }
        
        $stmt = $conn->prepare("
            INSERT INTO products (name, description, price, stock, image, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([$name, $description, $price, $stock, $imageFileName]);
        
        if ($result) {
            sendResponse(true, null, 'Product added successfully');
        } else {
            sendResponse(false, null, 'Error adding product');
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error adding product: ' . $e->getMessage());
    }
}

function updateProduct() {
    global $conn;
    
    try {
        requireAdminAction();
        $id = intval($_POST['id']);
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
        $newImageFileName = null;

        if (isset($_FILES['image']) && is_array($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg','jpeg','png','gif','webp'];
            $originalName = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) {
                sendResponse(false, null, 'Invalid image type');
            }
            $uploadDir = __DIR__ . '/images/products';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                    sendResponse(false, null, 'Failed to create upload directory');
                }
            }
            $newImageFileName = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetPath = $uploadDir . '/' . $newImageFileName;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                sendResponse(false, null, 'Failed to upload image');
            }
        }
        
        if (empty($id) || empty($name) || $price <= 0 || $stock < 0) {
            sendResponse(false, null, 'Please fill all required fields correctly');
        }
        
        if ($newImageFileName) {
            $stmt = $conn->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, stock = ?, image = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([$name, $description, $price, $stock, $newImageFileName, $id]);
        } else {
            $stmt = $conn->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, stock = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([$name, $description, $price, $stock, $id]);
        }
        
        if ($result) {
            sendResponse(true, null, 'Product updated successfully');
        } else {
            sendResponse(false, null, 'Error updating product');
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error updating product: ' . $e->getMessage());
    }
}

function deleteProduct() {
    global $conn;
    
    try {
        requireAdminAction();
        $id = intval($_POST['id']);
        
        if (empty($id)) {
            sendResponse(false, null, 'Invalid product ID');
        }
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            sendResponse(true, null, 'Product deleted successfully');
        } else {
            sendResponse(false, null, 'Error deleting product');
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error deleting product: ' . $e->getMessage());
    }
}

function getUsers() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse(true, ['users' => $users]);
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error fetching users: ' . $e->getMessage());
    }
}

function addUser() {
    global $conn;
    
    try {
        requireAdminAction();
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            sendResponse(false, null, 'Username and password are required');
        }
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            sendResponse(false, null, 'Username already exists');
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("
            INSERT INTO users (username, email, password, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([$username, $email, $hashedPassword]);
        
        if ($result) {
            sendResponse(true, null, 'User added successfully');
        } else {
            sendResponse(false, null, 'Error adding user');
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error adding user: ' . $e->getMessage());
    }
}

function updateUser() {
    global $conn;
    
    try {
        requireAdminAction();
        $id = intval($_POST['id']);
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($id) || empty($username)) {
            sendResponse(false, null, 'User ID and username are required');
        }
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $id]);
        if ($stmt->fetch()) {
            sendResponse(false, null, 'Username already exists');
        }
        
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("
                UPDATE users 
                SET username = ?, email = ?, password = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([$username, $email, $hashedPassword, $id]);
        } else {
            $stmt = $conn->prepare("
                UPDATE users 
                SET username = ?, email = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([$username, $email, $id]);
        }
        
        if ($result) {
            sendResponse(true, null, 'User updated successfully');
        } else {
            sendResponse(false, null, 'Error updating user');
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error updating user: ' . $e->getMessage());
    }
}

function deleteUser() {
    global $conn;
    
    try {
        requireAdminAction();
        $id = intval($_POST['id']);
        
        if (empty($id)) {
            sendResponse(false, null, 'Invalid user ID');
        }
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
        $stmt->execute([$id]);
        $orderCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($orderCount > 0) {
            sendResponse(false, null, 'Cannot delete user with existing orders');
        }
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            sendResponse(true, null, 'User deleted successfully');
        } else {
            sendResponse(false, null, 'Error deleting user');
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error deleting user: ' . $e->getMessage());
    }
}

function getOrders() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT o.*, u.username 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendResponse(true, ['orders' => $orders]);
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Error fetching orders: ' . $e->getMessage());
    }
}

function requireAdminAction() {
    if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
        sendResponse(false, null, 'Unauthorized');
    }
}
?>

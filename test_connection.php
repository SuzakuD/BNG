<?php
require_once 'db_connect.php';

try {
    // Test basic connection
    echo "Database connection successful!\n";
    
    // Test products table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products");
    $stmt->execute();
    $productCount = $stmt->fetch()['count'];
    echo "Products in database: $productCount\n";
    
    // Test users table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $userCount = $stmt->fetch()['count'];
    echo "Users in database: $userCount\n";
    
    // Test orders table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders");
    $stmt->execute();
    $orderCount = $stmt->fetch()['count'];
    echo "Orders in database: $orderCount\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

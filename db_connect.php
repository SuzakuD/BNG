<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "fishing_store";
$charset = "utf8mb4";

try {
    $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $conn = new PDO($dsn, $username, $password, $options);
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

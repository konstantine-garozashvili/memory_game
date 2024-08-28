<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    echo "Connected successfully to the database.";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
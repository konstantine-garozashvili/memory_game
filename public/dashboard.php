<?php
// public/dashboard.php

session_start();


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$database = new Database();
$db = $database->getConnection();

// Fetch user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, profile_picture FROM users WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch all users for the user list
$all_users_sql = "SELECT id, username FROM users WHERE id != ?";
$all_users_stmt = $db->prepare($all_users_sql);
$all_users_stmt->bind_param("i", $user_id);
$all_users_stmt->execute();
$all_users_result = $all_users_stmt->get_result();

// Include the view
require_once __DIR__ . '/../views/dashboard.php'; ?>


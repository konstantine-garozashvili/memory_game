<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the start of the script execution
error_log("check_updates.php started");

require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Log the user ID
error_log("User ID: " . $user_id);

try {
    // Fetch pending invitations
    $sql = "SELECT i.*, u.username FROM invitations i JOIN users u ON i.sender_id = u.id WHERE i.receiver_id = ? AND i.status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pending_invitations = $result->fetch_all(MYSQLI_ASSOC);

    // Log the number of invitations found
    error_log("Number of pending invitations: " . count($pending_invitations));

    $response = [
        'pending_invitations' => $pending_invitations
    ];

    // Log the response
    error_log("Response: " . json_encode($response));

    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred']);
}

// Log the end of the script execution
error_log("check_updates.php finished");

<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch active games
$sql = "SELECT * FROM games WHERE (player1_id = ? OR player2_id = ?) AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$active_games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch pending invitations
$sql = "SELECT i.*, u.username FROM invitations i JOIN users u ON i.sender_id = u.id WHERE i.receiver_id = ? AND i.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_invitations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'active_games' => $active_games,
    'pending_invitations' => $pending_invitations
]);

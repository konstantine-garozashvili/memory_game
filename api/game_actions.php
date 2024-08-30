<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data['action'] === 'game_over') {
    $game_id = $data['game_id'];
    $player_id = $data['player_id'];

    // Update the game status and set the winner
    $sql = "UPDATE games SET status = 'finished', winner_id = ? WHERE id = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['user_id'], $game_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // This player was the first to finish
        echo json_encode(['winner' => $player_id]);
    } else {
        // The game was already finished by the other player
        $sql = "SELECT u.username FROM games g JOIN users u ON g.winner_id = u.id WHERE g.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $winner = $result->fetch_assoc();
        echo json_encode(['winner' => $winner['username']]);
    }
} else {
    echo json_encode(['error' => 'Invalid action']);
}

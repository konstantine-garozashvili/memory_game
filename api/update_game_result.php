<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$game_id = $data['game_id'] ?? null;
$result = $data['result'] ?? null;

if (!$game_id || !$result) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE user_scores SET wins = CASE WHEN ? = 'wins' THEN wins + 1 ELSE wins END, 
                                           losses = CASE WHEN ? = 'losses' THEN losses + 1 ELSE losses END, 
                                           draws = CASE WHEN ? = 'draws' THEN draws + 1 ELSE draws END 
                      WHERE user_id = ?");
$stmt->bind_param("sssi", $result, $result, $result, $_SESSION['user_id']);


    if ($stmt->error) {
        throw new Exception("Database error: " . $stmt->error);
    }

    // Delete the finished game
    $stmt = $conn->prepare("DELETE FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();

    if ($stmt->error) {
        throw new Exception("Database error: " . $stmt->error);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Error in update_game_result.php: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}

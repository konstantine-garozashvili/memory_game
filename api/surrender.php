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
$game_id = $data['game_id'];
$player_id = $data['player_id'];

try {
    $conn->begin_transaction();

    // Fetch game details
    $stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $game = $stmt->get_result()->fetch_assoc();

    if (!$game) {
        throw new Exception('Game not found');
    }

    // Determine the winner (opponent of the surrendering player)
    $winner_id = ($game['player1_id'] == $player_id) ? $game['player2_id'] : $game['player1_id'];

    // Update game status and set winner
    $stmt = $conn->prepare("UPDATE games SET status = 'finished', winner_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $winner_id, $game_id);
    $stmt->execute();

    // Update player scores (assuming you have a scores table)
    $stmt = $conn->prepare("UPDATE users SET score = score + 1 WHERE id = ?");
    $stmt->bind_param("i", $winner_id);
    $stmt->execute();

    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

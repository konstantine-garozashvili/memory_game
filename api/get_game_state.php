<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['game_id'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$user_id = $_SESSION['user_id'];
$game_id = $_GET['game_id'];

try {
    // Fetch game details
    $stmt = $conn->prepare("SELECT * FROM games WHERE id = ? AND (player1_id = ? OR player2_id = ?)");
    $stmt->bind_param("iii", $game_id, $user_id, $user_id);
    $stmt->execute();
    $game = $stmt->get_result()->fetch_assoc();

    if (!$game) {
        throw new Exception('Game not found');
    }

    $is_player1 = ($game['player1_id'] == $user_id);
    $opponent_id = $is_player1 ? $game['player2_id'] : $game['player1_id'];

    // Retrieve card positions from the database
    $cards = json_decode($game['card_positions'], true);
    if (!$cards) {
        throw new Exception('Invalid card positions');
    }

    $current_turn = $game['current_turn_id'];
    $your_matches = $is_player1 ? $game['player1_matches'] : $game['player2_matches'];
    $opponent_matches = $is_player1 ? $game['player2_matches'] : $game['player1_matches'];

    // Retrieve last moves
    $stmt = $conn->prepare("SELECT * FROM game_moves WHERE game_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $moves = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'cards' => $cards,
        'current_turn' => $current_turn,
        'your_matches' => $your_matches,
        'opponent_matches' => $opponent_matches,
        'game_over' => $game['status'] === 'finished',
        'winner' => $game['winner_id'],
        'is_your_turn' => $current_turn == $user_id,
        'moves' => $moves
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

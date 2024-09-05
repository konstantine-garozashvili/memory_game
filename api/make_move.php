<?php
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}
session_start();
require_once '../includes/db.php';

ini_set('log_errors', 1);
ini_set('error_log', $logDir . '/php_error_log');
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$game_id = $data['game_id'] ?? null;
$player_id = $data['player_id'] ?? null;
$action = $data['action'] ?? null;

if (!$game_id || !$player_id || !$action) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    $conn->begin_transaction();

    // Fetch game details
    $stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    if ($stmt->error) {
        throw new Exception("Database error: " . $stmt->error);
    }
    $game = $stmt->get_result()->fetch_assoc();

    if (!$game) {
        throw new Exception('Game not found');
    }

    if ($game['current_turn_id'] != $player_id) {
        throw new Exception('Not your turn');
    }

    $is_player1 = ($game['player1_id'] == $player_id);
    $match_column = $is_player1 ? 'player1_matches' : 'player2_matches';
    $opponent_id = $is_player1 ? $game['player2_id'] : $game['player1_id'];

    if ($action === 'flip') {
        $card_index = $data['card_index'];
        $stmt = $conn->prepare("INSERT INTO game_moves (game_id, player_id, card_index, action) VALUES (?, ?, ?, 'flip')");
        $stmt->bind_param("iii", $game_id, $player_id, $card_index);
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("Database error: " . $stmt->error);
        }
    } elseif ($action === 'check_match') {
        $index1 = $data['index1'];
        $index2 = $data['index2'];
        $is_match = $data['is_match'];

        if ($is_match) {
            // Record the match
            $stmt = $conn->prepare("INSERT INTO game_moves (game_id, player_id, card_index, action) VALUES (?, ?, ?, 'match'), (?, ?, ?, 'match')");
            $stmt->bind_param("iiiiii", $game_id, $player_id, $index1, $game_id, $player_id, $index2);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Database error: " . $stmt->error);
            }

            // Increment match count
            $stmt = $conn->prepare("UPDATE games SET $match_column = $match_column + 1 WHERE id = ?");
            $stmt->bind_param("i", $game_id);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Database error: " . $stmt->error);
            }
        } else {
            // Record the unflip
            $stmt = $conn->prepare("INSERT INTO game_moves (game_id, player_id, card_index, action) VALUES (?, ?, ?, 'unflip'), (?, ?, ?, 'unflip')");
            $stmt->bind_param("iiiiii", $game_id, $player_id, $index1, $game_id, $player_id, $index2);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Database error: " . $stmt->error);
            }

            // Switch turns only if it's not a match
            $stmt = $conn->prepare("UPDATE games SET current_turn_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $opponent_id, $game_id);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Database error: " . $stmt->error);
            }
        }

        // Check if the game is over
        $stmt = $conn->prepare("SELECT player1_matches, player2_matches FROM games WHERE id = ?");
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("Database error: " . $stmt->error);
        }
        $result = $stmt->get_result()->fetch_assoc();
        $total_matches = $result['player1_matches'] + $result['player2_matches'];
        $total_pairs = ($game['game_mode'] === 'hidden_memory') ? 8 : 25;
        
        $game_is_over = false;
        if ($total_matches == $total_pairs) {
            $is_draw = ($result['player1_matches'] == $result['player2_matches']);
            if ($is_draw) {
                $stmt = $conn->prepare("UPDATE games SET status = 'finished', is_draw = TRUE WHERE id = ?");
                $stmt->bind_param("i", $game_id);
            } else {
                $winner_id = ($result['player1_matches'] > $result['player2_matches']) ? $game['player1_id'] : $game['player2_id'];
                $stmt = $conn->prepare("UPDATE games SET status = 'finished', winner_id = ? WHERE id = ?");
                $stmt->bind_param("ii", $winner_id, $game_id);
            }
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Database error: " . $stmt->error);
            }
            $game_is_over = true;
        
            // Update user scores
            $stmt = $conn->prepare("CALL update_user_scores(?, ?, ?)");
            $stmt->bind_param("iii", $game_id, $game['player1_id'], $game['player2_id']);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Database error: " . $stmt->error);
            }
        }
        
        // At the end of the try block, update the $game_status:
        $game_status = [
            'status' => $game['status'],
            'is_draw' => $is_draw ?? false,
            'winner_id' => $winner_id ?? null,
            'game_over' => $game_is_over
        ];
        echo json_encode(['success' => true, 'game_status' => $game_status]); 
    }

    $conn->commit();

    
} catch (Exception $e) {
    $conn->rollback();
    error_log('Error in make_move.php: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}

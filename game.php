<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$game_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Fetch game details
$sql = "SELECT * FROM games WHERE id = ? AND (player1_id = ? OR player2_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $game_id, $user_id, $user_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

if (!$game) {
    header("Location: dashboard.php");
    exit();
}

$is_player1 = ($game['player1_id'] == $user_id);
$opponent_id = $is_player1 ? $game['player2_id'] : $game['player1_id'];

$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $opponent_id);
$stmt->execute();
$opponent = $stmt->get_result()->fetch_assoc();

// Fetch current user's username
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_user = $stmt->get_result()->fetch_assoc();

// Define the number of pairs in the game
$total_pairs = 10; // Adjust this number based on your game design
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game #<?php echo $game_id; ?> - Memory Card Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Game #<?php echo $game_id; ?></h2>
    <p>Playing against: <?php echo htmlspecialchars($opponent['username']); ?></p>
    <p>Your username: <?php echo htmlspecialchars($current_user['username']); ?></p>

    <div id="game-board" class="game-board">
        <!-- Game board will be populated by JavaScript -->
    </div>

    <div id="game-info">
        <p>Your turn: <span id="is-your-turn">Waiting...</span></p>
        <p>Your matches: <span id="your-matches">0</span></p>
        <p>Opponent's matches: <span id="opponent-matches">0</span></p>
    </div>

    <script>
    const gameId = <?php echo json_encode($game_id); ?>;
    const playerId = <?php echo json_encode($user_id); ?>;
    const isPlayer1 = <?php echo json_encode($is_player1); ?>;
    const totalPairs = <?php echo json_encode($total_pairs); ?>;
    const opponentName = <?php echo json_encode($opponent['username']); ?>;
    </script>
    <script src="js/game.js"></script>
</body>
</html>

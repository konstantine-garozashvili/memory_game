<?php
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

// Delete game if requested
if (isset($_GET['delete_game_id'])) {
    $delete_game_id = $_GET['delete_game_id'];
    $stmt = $conn->prepare("DELETE FROM games WHERE id = ?");
    $stmt->bind_param("i", $delete_game_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

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

// Get the game mode
$game_mode = $game['game_mode'];

// Define the number of pairs based on the game mode
$total_pairs = ($game_mode === 'visible_memory') ? 25 : 8; // 50 cards for visible memory, 16 for hidden

// Function to get a human-readable game mode name
function getGameModeName($mode) {
    switch ($mode) {
        case 'hidden_memory':
            return 'Hidden Memory Game';
        case 'visible_memory':
            return '50-Card Memory Game';
        default:
            return 'Unknown Game Mode';
    }
}

$game_mode_name = getGameModeName($game_mode);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game #<?php echo $game_id; ?> - <?php echo htmlspecialchars($game_mode_name); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Game #<?php echo $game_id; ?> - <?php echo htmlspecialchars($game_mode_name); ?></h2>
    <p>Playing against: <?php echo htmlspecialchars($opponent['username']); ?></p>
    <p>Your username: <?php echo htmlspecialchars($current_user['username']); ?></p>
    <p>Game Mode: <?php echo htmlspecialchars($game_mode_name); ?></p>

    <div id="game-info">
        <p>Your turn: <span id="is-your-turn">Waiting...</span></p>
        <p>Your matches: <span id="your-matches">0</span></p>
        <p>Opponent's matches: <span id="opponent-matches">0</span></p>
    </div>

    <div id="game-board" class="game-board">
        <!-- Game board will be populated by JavaScript -->
    </div>

    <script>
    const gameId = <?php echo json_encode($game_id); ?>;
    const playerId = <?php echo json_encode($user_id); ?>;
    const isPlayer1 = <?php echo json_encode($is_player1); ?>;
    const totalPairs = <?php echo json_encode($total_pairs); ?>;
    const opponentName = <?php echo json_encode($opponent['username']); ?>;
    const gameMode = <?php echo json_encode($game_mode); ?>;
    </script>
    <script src="js/game.js"></script>
</body>
</html>

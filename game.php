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

$opponent_id = ($game['player1_id'] == $user_id) ? $game['player2_id'] : $game['player1_id'];
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
?>
<!-- ... (previous PHP code remains the same) ... -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game - Memory Card Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Game #<?php echo $game_id; ?></h2>
    <p>Playing against: <?php echo $opponent['username']; ?></p>
    <p>Your username: <?php echo $current_user['username']; ?></p>

    <div id="game-board" class="game-board">
        <p>If you can see this, the game board div exists but hasn't been populated by JavaScript.</p>
    </div>

    <script>
        const gameId = '<?php echo $game_id; ?>';
        const playerId = '<?php echo $current_user['username']; ?>';
        console.log("Game ID:", gameId);
        console.log("Player ID:", playerId);
    </script>
    <script src="js/game.js?v=2"></script>
</body>
</html>




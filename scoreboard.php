<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


// Fetch user's score
$stmt = $conn->prepare("SELECT wins, losses, draws FROM user_scores WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_score = $result->fetch_assoc();

if (!$user_score) {
    $user_score = ['wins' => 0, 'losses' => 0, 'draws' => 0];
}

// Fetch top 10 players
$stmt = $conn->prepare("
    SELECT u.username, us.wins, us.losses, us.draws
    FROM users u
    LEFT JOIN user_scores us ON u.id = us.user_id
    ORDER BY us.wins DESC, us.draws DESC
    LIMIT 10
");
$stmt->execute();
$top_players = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Scoreboard</h1>
    
    <h2>Your Score</h2>
    <p>Wins: <?php echo $user_score['wins']; ?></p>
    <p>Losses: <?php echo $user_score['losses']; ?></p>
    <p>Draws: <?php echo $user_score['draws']; ?></p>

    <h2>Top 10 Players</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Draws</th>
        </tr>
        <?php foreach ($top_players as $player): ?>
        <tr>
            <td><?php echo htmlspecialchars($player['username']); ?></td>
            <td><?php echo $player['wins'] ?? 0; ?></td>
            <td><?php echo $player['losses'] ?? 0; ?></td>
            <td><?php echo $player['draws'] ?? 0; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

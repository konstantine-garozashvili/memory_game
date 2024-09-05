<?php
require_once 'includes/db.php';  // Assurez-vous que ce fichier contient les informations de connexion à la base de données
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérez l'ID du jeu depuis les paramètres GET ou définissez une valeur par défaut
$game_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Vérifiez que l'ID du jeu est valide
if ($game_id <= 0) {
    echo "Jeu invalide.";
    exit();
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'username', 'password', 'memory_game');
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérez les détails du jeu
$sql = "SELECT * FROM games WHERE id = ? AND (player1_id = ? OR player2_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $game_id, $user_id, $user_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

if (!$game) {
    echo "Jeu non trouvé ou vous n'êtes pas autorisé à voir ce jeu.";
    exit();
}

// Récupérez les cartes pour ce jeu
$sql = "SELECT id, card_value, is_revealed FROM cards WHERE game_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$cards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Affichez les cartes pour débogage
echo "<h2>Détails des Cartes pour le Jeu #$game_id</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Valeur</th><th>Révélée</th></tr>";
foreach ($cards as $card) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($card['id']) . "</td>";
    echo "<td>" . htmlspecialchars($card['card_value']) . "</td>";
    echo "<td>" . ($card['is_revealed'] ? 'Oui' : 'Non') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Fermez la connexion à la base de données
$conn->close();
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
    const cards = <?php echo json_encode($cards); ?>;
    const firstRevealedCard = <?php echo json_encode($first_revealed_card); ?>;
</script>

    <script src="js/game.js"></script>
</body>
</html>

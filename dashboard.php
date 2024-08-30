<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch active games
$sql = "SELECT * FROM games WHERE (player1_id = ? OR player2_id = ?) AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$active_games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch pending invitations
$sql = "SELECT i.*, u.username FROM invitations i JOIN users u ON i.sender_id = u.id WHERE i.receiver_id = ? AND i.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_invitations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Memory Card Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    
    <h3>Active Games</h3>
    <ul id="active-games-list">
        <?php if (empty($active_games)): ?>
            <li>No active games.</li>
        <?php else: ?>
            <?php foreach ($active_games as $game): ?>
                <li><a href="game.php?id=<?php echo $game['id']; ?>">Game #<?php echo $game['id']; ?></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <h3>Pending Invitations</h3>
    <ul id="pending-invitations-list">
        <?php if (empty($pending_invitations)): ?>
            <li>No pending invitations.</li>
        <?php else: ?>
            <?php foreach ($pending_invitations as $invitation): ?>
                <li>
                    Invitation from <?php echo $invitation['username']; ?>
                    <form action="invite.php" method="POST" style="display: inline;">
                        <input type="hidden" name="invitation_id" value="<?php echo $invitation['id']; ?>">
                        <button type="submit" name="action" value="accept">Accept</button>
                        <button type="submit" name="action" value="decline">Decline</button>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <h3>Invite a Player</h3>
    <form action="invite.php" method="POST">
        <input type="text" name="username" placeholder="Enter username" required>
        <button type="submit">Send Invitation</button>
    </form>

    <a href="logout.php">Logout</a>

    <script>
    function checkForUpdates() {
        fetch('api/check_updates.php')
            .then(response => response.json())
            .then(data => {
                updateActiveGames(data.active_games);
                updatePendingInvitations(data.pending_invitations);
            })
            .catch(error => console.error('Error checking for updates:', error));
    }

    function updateActiveGames(games) {
        const gamesList = document.getElementById('active-games-list');
        gamesList.innerHTML = '';
        if (games.length === 0) {
            gamesList.innerHTML = '<li>No active games.</li>';
        } else {
            games.forEach(game => {
                gamesList.innerHTML += `<li><a href="game.php?id=${game.id}">Game #${game.id}</a></li>`;
            });
        }
    }

    function updatePendingInvitations(invitations) {
        const invitationsList = document.getElementById('pending-invitations-list');
        invitationsList.innerHTML = '';
        if (invitations.length === 0) {
            invitationsList.innerHTML = '<li>No pending invitations.</li>';
        } else {
            invitations.forEach(invitation => {
                invitationsList.innerHTML += `
                    <li>
                        Invitation from ${invitation.username}
                        <form action="invite.php" method="POST" style="display: inline;">
                            <input type="hidden" name="invitation_id" value="${invitation.id}">
                            <button type="submit" name="action" value="accept">Accept</button>
                            <button type="submit" name="action" value="decline">Decline</button>
                        </form>
                    </li>
                `;
            });
        }
    }

    // Check for updates every 10 seconds
    setInterval(checkForUpdates, 10000);

    // Also check immediately when the page loads
    checkForUpdates();
    </script>
</body>
</html>

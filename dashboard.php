<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Clean up finished games
$sql = "DELETE FROM games WHERE status = 'finished'";
$conn->query($sql);
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
                    <button onclick="acceptInvitation(<?php echo $invitation['id']; ?>)">Accept</button>
                    <button onclick="declineInvitation(<?php echo $invitation['id']; ?>)">Decline</button>
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
                console.log('Parsed data:', data);
                if (data.pending_invitations) {
                    updatePendingInvitations(data.pending_invitations);
                }
                if (data.active_games) {
                    updateActiveGames(data.active_games);
                }
            })
            .catch(error => console.error('Error checking for updates:', error));
    }

    function updatePendingInvitations(invitations) {
        const invitationsList = document.getElementById('pending-invitations-list');
        if (invitations.length === 0) {
            invitationsList.innerHTML = '<li>No pending invitations.</li>';
        } else {
            invitationsList.innerHTML = '';
            invitations.forEach(invitation => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    Invitation from ${invitation.username}
                    <button onclick="acceptInvitation(${invitation.id})">Accept</button>
                    <button onclick="declineInvitation(${invitation.id})">Decline</button>
                `;
                invitationsList.appendChild(listItem);
            });
        }
        console.log('Updated invitations:', invitations);
    }

    function updateActiveGames(games) {
        const gamesList = document.getElementById('active-games-list');
        if (games.length === 0) {
            gamesList.innerHTML = '<li>No active games.</li>';
        } else {
            gamesList.innerHTML = '';
            games.forEach(game => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<a href="game.php?id=${game.id}">Game #${game.id}</a>`;
                gamesList.appendChild(listItem);
            });
        }
    }

    function acceptInvitation(invitationId) {
    fetch('api/accept_invitation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ invitation_id: invitationId })
    })
    .then(response => response.text())  // Change this line from response.json() to response.text()
    .then(text => {
        console.log('Raw server response:', text);  // Log the raw response
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert('Invitation accepted. Redirecting to game...');
                window.location.href = `game.php?id=${data.game_id}`;
            } else {
                console.error('Error accepting invitation:', data.error);
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
        }
    })
    .catch(error => console.error('Fetch error:', error));
}

    function declineInvitation(invitationId) {
        fetch('api/decline_invitation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ invitation_id: invitationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkForUpdates(); // Refresh the invitations list
            } else {
                console.error('Error declining invitation:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Check for updates every 5 seconds
    setInterval(checkForUpdates, 5000);


    function checkForGameStart() {
    fetch('api/check_game_start.php')
        .then(response => response.json())
        .then(data => {
            if (data.game_id) {
                window.location.href = `game.php?id=${data.game_id}`;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Check for game start every 5 seconds
setInterval(checkForGameStart, 5000);


    // Also check immediately when the page loads
    checkForUpdates();
    </script>
</body>
</html>

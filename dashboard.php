<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
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

// Define game modes
$game_modes = [
    'hidden_memory' => 'Hidden Memory Game',
    'visible_memory' => '50-Card Memory Game'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Memory Card Game</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #007BFF;
            color: #fff;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 1.5rem;
            color: #333;
            margin-top: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        li button:hover {
            background-color: #0056b3;
        }

        form {
            margin-top: 20px;
        }

        form select,
        form input[type="text"],
        form button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            margin-right: 10px;
        }

        form select,
        form input[type="text"] {
            width: calc(100% - 150px); /* Adjust width to fit button */
            display: inline-block;
        }

        form button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        form button:hover {
            background-color: #218838;
        }

        a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin.php">Admin Dashboard</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        
        <h3>Active Games</h3>
        <ul id="active-games-list">
            <?php if (empty($active_games)): ?>
                <li>No active games.</li>
            <?php else: ?>
                <?php foreach ($active_games as $game): ?>
                    <li><a href="game.php?id=<?php echo htmlspecialchars($game['id']); ?>">Game #<?php echo htmlspecialchars($game['id']); ?></a></li>
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
                        Invitation from <?php echo htmlspecialchars($invitation['username']); ?>
                        <button onclick="acceptInvitation(<?php echo $invitation['id']; ?>)">Accept</button>
                        <button onclick="declineInvitation(<?php echo $invitation['id']; ?>)">Decline</button>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        
        <h3>Invite a Player</h3>
        <form id="invite-form">
            <select name="game_mode" required>
                <option value="hidden_memory">Hidden Memory Game</option>
                <option value="visible_memory">50-Card Memory Game</option>
            </select>
            <input type="text" name="username" placeholder="Enter username" required>
            <button type="submit">Send Invitation</button>
        </form>
    </div>

    <script>
    function checkForUpdates() {
        fetch('api/check_updates.php')
            .then(response => response.text())
            .then(text => {
                console.log('Raw response:', text);
                const data = JSON.parse(text);
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
        .then(response => response.text())
        .then(text => {
            console.log('Raw server response:', text);
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

    document.getElementById('invite-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('api/send_invitation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Invitation sent successfully!');
            } else {
                alert('Error sending invitation: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });

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

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS stylesheet -->
</head>
<body>
    <header>
        <h1>Welcome to Memory Game</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>About the Game</h2>
        <p>Welcome to our Memory Matching Pairs Game! Challenge your memory skills and compete with friends.</p>
        <p>To start playing, you need to <a href="register.php">register</a> or <a href="login.php">log in</a> if you already have an account.</p>
        <iframe src="https://www.yeschat.ai/i/gpts-ZxX7eSA7-ResearchGPT" width="800" height="500" style="max-width: 100%;"></iframe>
        <?php if (isset($_SESSION['user_id'])): ?>
            <h3>Game Invites</h3>
            <p>Click <a href="game_invite.php">here</a> to invite friends to play a game.</p>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2024 Memory Game. All rights reserved.</p>
    </footer>
</body>
</html>

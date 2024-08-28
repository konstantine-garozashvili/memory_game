<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Memory Game</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .profile { background: #f4f4f4; padding: 20px; margin-bottom: 20px; }
        .profile img { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; }
        .user-list { background: #f4f4f4; padding: 20px; }
        .user-list ul { list-style-type: none; padding: 0; }
        .user-list li { padding: 10px 0; border-bottom: 1px solid #ddd; }
        .button { display: inline-block; background: #333; color: #fff; padding: 10px 20px; text-decoration: none; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Memory Game, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        
        <div class="profile">
            <h2>Your Profile</h2>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <a href="edit_profile.php" class="button">Edit Profile</a>
        </div>

        <div class="user-list">
            <h2>Other Players</h2>
            <ul>
            <?php while ($other_user = $all_users_result->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($other_user['username']); ?>
                    <a href="invite.php?user_id=<?php echo $other_user['id']; ?>" class="button">Invite to Game</a>
                </li>
            <?php endwhile; ?>
            </ul>
        </div>

        <a href="game.php" class="button">Start New Game</a>
        <a href="logout.php" class="button">Logout</a>
    </div>
</body>
</html>
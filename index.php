<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Card Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Welcome to Memory Card Game</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
        <a href="dashboard.php">Go to Dashboard</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</body>
</html>

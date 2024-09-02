<?php

require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'])) {
        // Send invitation
        $username = $_POST['username'];
        
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($receiver = $result->fetch_assoc()) {
            $receiver_id = $receiver['id'];
            $sql = "INSERT INTO invitations (sender_id, receiver_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $receiver_id);
            $stmt->execute();
            
            $_SESSION['message'] = "Invitation sent successfully!";
        } else {
            $_SESSION['error'] = "User not found.";
        }
    } elseif (isset($_POST['invitation_id']) && isset($_POST['action'])) {
        // Handle invitation response
        $invitation_id = $_POST['invitation_id'];
        $action = $_POST['action'];
        
        if ($action == 'accept') {
            $sql = "UPDATE invitations SET status = 'accepted' WHERE id = ? AND receiver_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $invitation_id, $user_id);
            $stmt->execute();
            
            // Create a new game
            $sql = "SELECT sender_id FROM invitations WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $invitation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $sender = $result->fetch_assoc();
            
            $sql = "INSERT INTO games (player1_id, player2_id, status) VALUES (?, ?, 'active')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $sender['sender_id'], $user_id);
            $stmt->execute();
            
            $game_id = $conn->insert_id;
            $_SESSION['message'] = "Invitation accepted. Game started!";
            header("Location: game.php?id=$game_id");
            exit();
        } elseif ($action == 'decline') {
            $sql = "UPDATE invitations SET status = 'declined' WHERE id = ? AND receiver_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $invitation_id, $user_id);
            $stmt->execute();
            
            $_SESSION['message'] = "Invitation declined.";
        }
    }
}

header("Location: dashboard.php");
exit();

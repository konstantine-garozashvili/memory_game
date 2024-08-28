<?php
// controllers/auth_controller.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

class AuthController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function register() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $this->db->real_escape_string($_POST['username']);
            $email = $this->db->real_escape_string($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                return "Registration successful. You can now log in.";
            } else {
                return "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    public function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $this->db->real_escape_string($_POST['email']);
            $password = $_POST['password'];

            $sql = "SELECT id, username, password FROM users WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    return "Login successful. Redirecting to dashboard...";
                } else {
                    return "Invalid email or password.";
                }
            } else {
                return "Invalid email or password.";
            }

            $stmt->close();
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        return "You have been logged out.";
    }
}
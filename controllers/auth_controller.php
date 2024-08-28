<?php
// controllers/auth_controller.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
            $verification_token = bin2hex(random_bytes(16));

            $sql = "INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $password, $verification_token);

            if ($stmt->execute()) {
                $this->sendVerificationEmail($email, $username, $verification_token);
                return "Registration successful. Please check your email to verify your account.";
            } else {
                return "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    private function sendVerificationEmail($email, $username, $verification_token) {
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port = SMTP_PORT;

            $mail->setFrom('noreply@memorygame.com', 'Memory Game');
            $mail->addAddress($email, $username);
            $mail->isHTML(true);
            $mail->Subject = 'Verify your email';
            $mail->Body = "Click this link to verify your email: " . SITE_URL . "/verify.php?token=$verification_token";

            $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send verification email. Error: {$mail->ErrorInfo}");
        }
    }

    // Other authentication methods (login, logout, etc.) would go here
}
<?php
// public/login.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../controllers/auth_controller.php';

$authController = new AuthController();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $authController->login();
    if (strpos($message, "successful") !== false) {
        header("Location: dashboard.php");
        exit();
    }
}

// Include the view
require_once __DIR__ . '/../views/login.php';
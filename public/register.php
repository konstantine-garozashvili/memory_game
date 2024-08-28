<?php
// public/register.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../controllers/auth_controller.php';

$authController = new AuthController();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $authController->register();
}

// Include the view
require_once __DIR__ . '/../views/register.php';
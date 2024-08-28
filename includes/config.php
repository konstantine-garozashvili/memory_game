<?php
// includes/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'memoryG');
define('DB_PASS', 'Laplateforme');
define('DB_NAME', 'konstantine-garozashvili_memory_game_db');

// SMTP settings - these need to be filled in with your actual SMTP details
define('SMTP_HOST', ''); // You need to provide this
define('SMTP_USERNAME', ''); // You need to provide this
define('SMTP_PASSWORD', ''); // You need to provide this
define('SMTP_PORT', 587); // This is a common port, but you may need to change it

define('SITE_URL', 'https://konstantine-garozashvili.students-laplateforme.io');

// Set the default timezone
date_default_timezone_set('Europe/Paris'); // Assuming you're in France, adjust if necessary
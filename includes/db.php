<?php
// includes/db.php

class Database {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli('localhost', 'memoryG', 'your_password', 'memory_game');

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Set the charset to utf8mb4
        $this->conn->set_charset("utf8mb4");
    }

    public function getConnection() {
        return $this->conn;
    }
}
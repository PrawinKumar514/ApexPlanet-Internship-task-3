<?php
/**
 * Database configuration and connection
 * Using MySQLi with prepared statements
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'user_management_system');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is empty

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
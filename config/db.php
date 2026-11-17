<?php
/**
 * Database Connection Configuration
 * Project: Community Portal for Village Democracy
 * Database: csp
 */

// Database credentials
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "csp";

// Connect to MySQL
// In this XAMPP setup, MySQL/MariaDB runs on port 3307; fallback to default port (3306) if needed
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, 3307);

if (!$conn) {
    // Fallback to default port
    $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
}

// Check if connection succeeded
if (!$conn) {
    die("Database Connection Error: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>


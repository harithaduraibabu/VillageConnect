<?php
/**
 * Citizen Logout Handler
 * Destroys active citizen session
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all citizen session variables
unset($_SESSION['citizen_id']);
unset($_SESSION['citizen_name']);
unset($_SESSION['citizen_email']);
unset($_SESSION['citizen_village']);

// Clear all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>


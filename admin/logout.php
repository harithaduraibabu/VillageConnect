<?php
/**
 * Administrator Logout Handler
 * Destroys admin session and redirects to admin login page
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);

header("Location: login.php");
exit();
?>


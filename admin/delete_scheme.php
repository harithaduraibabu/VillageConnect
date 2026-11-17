<?php
/**
 * Delete Government Scheme Handler
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM government_schemes WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_success'] = "Government scheme #{$id} deleted successfully.";
    }
    mysqli_stmt_close($stmt);
}

header("Location: manage_schemes.php");
exit();
?>


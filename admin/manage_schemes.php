<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once "../config/db.php";

$admin_username = $_SESSION['admin_username'] ?? 'admin';
$success_msg = "";
$error_msg = "";

// Check for session flash messages
if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// Handle deletion if requested via GET
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id > 0) {
        $del_sql = "DELETE FROM government_schemes WHERE id = ?";
        $del_stmt = mysqli_prepare($conn, $del_sql);
        mysqli_stmt_bind_param($del_stmt, "i", $delete_id);
        if (mysqli_stmt_execute($del_stmt)) {
            $success_msg = "Government scheme #{$delete_id} deleted successfully.";
        } else {
            $error_msg = "Failed to delete scheme.";
        }
        mysqli_stmt_close($del_stmt);
    }
}

// Fetch all government schemes
$query = "SELECT * FROM government_schemes ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schemes - VillageConnect</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Header Section -->
    <header class="top-header" style="background-color: #0d381e;">
        <div class="container header-content">
            <div>
                <a href="dashboard.php" class="brand-title">
                    VillageConnect
                </a>
                <span class="brand-subtitle">Administrator Dashboard & Governance Console</span>
            </div>
            <div>
                <span style="color: #c8e6c9; font-size: 0.9rem;">
                    Admin: <strong><?php echo htmlspecialchars($admin_username); ?></strong>
                </span>
            </div>
        </div>
    </header>

    <!-- Admin Navigation Bar -->
    <nav class="navbar" style="background-color: #1b5e20;">
        <div class="container">
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_citizens.php">Citizens</a></li>
                <li><a href="manage_grievances.php">Grievances</a></li>
                <li><a href="manage_announcements.php">Announcements</a></li>
                <li><a href="manage_schemes.php" class="active">Government Schemes</a></li>
                <li><a href="manage_feedback.php">Feedback</a></li>
                <li><a href="../index.php" target="_blank">View Public Site</a></li>
                <li class="nav-right"><a href="logout.php" class="nav-highlight">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
                <div>
                    <h1 class="section-title" style="margin-bottom: 5px;">Manage Government Schemes</h1>
                    <p class="section-desc" style="margin-bottom: 0;">
                        Add and maintain informative guides on rural welfare, financial aid, and infrastructure schemes.
                    </p>
                </div>
                <div>
                    <a href="add_scheme.php" class="btn btn-primary">Add New Scheme</a>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_msg); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <!-- Schemes Table -->
            <div class="card">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px;"># ID</th>
                                    <th style="width: 240px;">Scheme Name</th>
                                    <th>Overview</th>
                                    <th>Eligibility Summary</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td><strong style="color: #0277bd;"><?php echo htmlspecialchars($row['scheme_name']); ?></strong></td>
                                        <td>
                                            <div style="font-size: 0.88rem; color: #455a64; line-height: 1.5;">
                                                <?php 
                                                $desc = htmlspecialchars($row['description']);
                                                echo (strlen($desc) > 90) ? substr($desc, 0, 90) . '...' : $desc;
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.85rem; color: #2e7d32;">
                                                <?php 
                                                $elg = htmlspecialchars($row['eligibility']);
                                                echo (strlen($elg) > 80) ? substr($elg, 0, 80) . '...' : $elg;
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-links">
                                                <a href="edit_scheme.php?id=<?php echo $row['id']; ?>" class="action-edit">
                                                    Edit
                                                </a>
                                                <a href="manage_schemes.php?delete=<?php echo $row['id']; ?>" 
                                                   class="action-delete" 
                                                   onclick="return confirmDelete('government scheme');">
                                                    Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 35px 0;">
                        <p style="color: #78909c;">No government schemes found.</p>
                        <a href="add_scheme.php" class="btn btn-primary btn-sm" style="margin-top: 10px;">Add First Scheme</a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container text-center">
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Community Portal for Village Democracy | B.Tech CSE College Demonstration Project</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
</body>
</html>


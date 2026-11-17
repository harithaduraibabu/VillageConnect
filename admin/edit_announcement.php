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
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: manage_announcements.php");
    exit();
}

$error_msg = "";

// Fetch existing record
$select_sql = "SELECT * FROM announcements WHERE id = ?";
$select_stmt = mysqli_prepare($conn, $select_sql);
mysqli_stmt_bind_param($select_stmt, "i", $id);
mysqli_stmt_execute($select_stmt);
$res = mysqli_stmt_get_result($select_stmt);
$announcement = mysqli_fetch_assoc($res);
mysqli_stmt_close($select_stmt);

if (!$announcement) {
    die("Announcement not found. <a href='manage_announcements.php'>Return to List</a>");
}

$title = $announcement['title'];
$description = $announcement['description'];
$published_date = $announcement['published_date'];

// Handle form update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title          = trim($_POST["title"] ?? "");
    $description    = trim($_POST["description"] ?? "");
    $published_date = trim($_POST["published_date"] ?? "");

    if (empty($title) || empty($description) || empty($published_date)) {
        $error_msg = "All fields are required.";
    } else {
        $update_sql = "UPDATE announcements SET title = ?, description = ?, published_date = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sssi", $title, $description, $published_date, $id);

        if (mysqli_stmt_execute($update_stmt)) {
            mysqli_stmt_close($update_stmt);
            $_SESSION['flash_success'] = "Announcement #{$id} updated successfully!";
            header("Location: manage_announcements.php");
            exit();
        } else {
            $error_msg = "Failed to update announcement. Please try again.";
            mysqli_stmt_close($update_stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement #<?php echo $id; ?> - VillageConnect</title>
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
                <li><a href="manage_announcements.php" class="active">Announcements</a></li>
                <li><a href="manage_schemes.php">Government Schemes</a></li>
                <li><a href="manage_feedback.php">Feedback</a></li>
                <li><a href="../index.php" target="_blank">View Public Site</a></li>
                <li class="nav-right"><a href="logout.php" class="nav-highlight">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div style="margin-bottom: 20px;">
                <a href="manage_announcements.php" style="color: #2e7d32; font-weight: 600; text-decoration: none;">&larr; Back to Announcements List</a>
            </div>

            <div class="form-box" style="max-width: 700px;">
                <h2 style="color: #1b5e20; margin-bottom: 6px;">Edit Announcement #<?php echo $id; ?></h2>
                <p style="color: #546e7a; font-size: 0.92rem; margin-bottom: 25px;">
                    Modify the announcement title, publication date, or notice description.
                </p>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="edit_announcement.php?id=<?php echo $id; ?>" method="POST">
                    <div class="form-group">
                        <label for="title">Announcement Title <span class="req">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="<?php echo htmlspecialchars($title); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="published_date">Published Date <span class="req">*</span></label>
                        <input type="date" id="published_date" name="published_date" class="form-control" 
                               value="<?php echo htmlspecialchars($published_date); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Detailed Description <span class="req">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="6" required><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 25px;">
                        <button type="submit" class="btn btn-primary" style="flex: 2; padding: 12px;">
                            Save Changes
                        </button>
                        <a href="manage_announcements.php" class="btn btn-outline" style="flex: 1; padding: 12px;">
                            Cancel
                        </a>
                    </div>
                </form>
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

</body>
</html>


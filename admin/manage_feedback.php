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

// Fetch all feedback with citizen details
$query = "SELECT f.id, f.subject, f.message, f.created_at, 
                 c.full_name as citizen_name, c.email as citizen_email, c.phone as citizen_phone, c.village_name 
          FROM feedback f 
          JOIN citizens c ON f.citizen_id = c.id 
          ORDER BY f.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - VillageConnect</title>
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
                <li><a href="manage_schemes.php">Government Schemes</a></li>
                <li><a href="manage_feedback.php" class="active">Feedback</a></li>
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
                    <h1 class="section-title" style="margin-bottom: 5px;">Citizen Feedback & Suggestions</h1>
                    <p class="section-desc" style="margin-bottom: 0;">
                        Read community opinions, constructive suggestions, and feedback submitted by registered residents.
                    </p>
                </div>
            </div>

            <!-- Feedback List / Cards -->
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="card" style="border-left: 5px solid #7b1fa2;">
                            <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <h2 class="card-title" style="color: #7b1fa2; font-size: 1.25rem;">
                                        <?php echo htmlspecialchars($row['subject']); ?>
                                    </h2>
                                    <span style="font-size: 0.85rem; color: #546e7a;">
                                        By <strong><?php echo htmlspecialchars($row['citizen_name']); ?></strong> (Village: <?php echo htmlspecialchars($row['village_name']); ?>) | 
                                        Email: <?php echo htmlspecialchars($row['citizen_email']); ?> | 
                                        Phone: <?php echo htmlspecialchars($row['citizen_phone']); ?>
                                    </span>
                                </div>
                                <div>
                                    <span style="font-size: 0.85rem; background: #f3e5f5; color: #7b1fa2; padding: 4px 10px; border-radius: 12px; font-weight: 600;">
                                        <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?>
                                    </span>
                                </div>
                            </div>

                            <div style="background-color: #fafafa; border: 1px solid #eeeeee; border-radius: 6px; padding: 15px; margin-top: 10px;">
                                <p style="color: #263238; line-height: 1.7; font-size: 0.95rem; margin-bottom: 0; white-space: pre-line;">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 40px;">
                    <h3 style="color: #78909c;">No Feedback Submissions Yet</h3>
                    <p style="color: #90a4ae; margin-top: 8px;">No citizen feedback has been submitted to the administration yet.</p>
                </div>
            <?php endif; ?>
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


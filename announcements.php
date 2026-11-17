<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once "config/db.php";

// Fetch all announcements ordered by published date
$query = "SELECT * FROM announcements ORDER BY published_date DESC, id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Announcements - VillageConnect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header Section -->
    <header class="top-header">
        <div class="container header-content">
            <div>
                <a href="index.php" class="brand-title">
                    VillageConnect
                </a>
                <span class="brand-subtitle">Community Portal for Village Democracy</span>
            </div>
            <div>
                <?php if (isset($_SESSION['citizen_id'])): ?>
                    <span style="color: #c8e6c9; font-size: 0.9rem;">Namaste, <?php echo htmlspecialchars($_SESSION['citizen_name']); ?></span>
                <?php elseif (isset($_SESSION['admin_id'])): ?>
                    <span style="color: #c8e6c9; font-size: 0.9rem;">Admin Panel (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="announcements.php" class="active">Announcements</a></li>
                <li><a href="schemes.php">Government Schemes</a></li>

                <?php if (isset($_SESSION['citizen_id'])): ?>
                    <li><a href="citizen/dashboard.php">Citizen Dashboard</a></li>
                    <li><a href="citizen/submit_grievance.php">Submit Grievance</a></li>
                    <li><a href="citizen/my_grievances.php">My Grievances</a></li>
                    <li><a href="citizen/feedback.php">Feedback</a></li>
                    <li class="nav-right"><a href="logout.php" class="nav-highlight">Logout</a></li>
                <?php elseif (isset($_SESSION['admin_id'])): ?>
                    <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
                    <li class="nav-right"><a href="admin/logout.php" class="nav-highlight">Admin Logout</a></li>
                <?php else: ?>
                    <li class="nav-right"><a href="login.php">Citizen Login</a></li>
                    <li><a href="register.php" class="nav-highlight">Citizen Register</a></li>
                    <li><a href="admin/login.php">Admin Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div style="margin-bottom: 25px;">
                <h1 class="section-title">Public Announcements & Community Notices</h1>
                <p class="section-desc">
                    Official announcements, public health drives, Gram Sabha schedules, and infrastructure advisories issued for the village.
                </p>
            </div>

            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <article class="announcement-item" style="border-left-width: 6px;">
                            <div class="item-meta">
                                <span style="background: #e8f5e9; color: #1b5e20; padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                                    Published Date: <?php echo date("l, F d, Y", strtotime($row['published_date'])); ?>
                                </span>
                            </div>
                            <h2 class="item-title" style="font-size: 1.3rem; margin-top: 5px;">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h2>
                            <p class="item-desc" style="font-size: 1rem; line-height: 1.7; color: #37474f;">
                                <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                            </p>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 40px;">
                    <h3 style="color: #78909c;">No Announcements Published</h3>
                    <p style="color: #90a4ae; margin-top: 8px;">There are no active public notices at this moment. Please check back later.</p>
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


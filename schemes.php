<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once "config/db.php";

// Fetch all government schemes
$query = "SELECT * FROM government_schemes ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Government Schemes - VillageConnect</title>
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
                <li><a href="announcements.php">Announcements</a></li>
                <li><a href="schemes.php" class="active">Government Schemes</a></li>

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
                <h1 class="section-title">Government Schemes & Rural Welfare Initiatives</h1>
                <p class="section-desc">
                    Comprehensive awareness guide on rural development schemes, housing grants, clean water missions, and agricultural power programs.
                </p>
            </div>

            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <article class="scheme-item" style="border-left: 6px solid #0288d1;">
                            <h2 class="item-title" style="color: #0277bd; font-size: 1.35rem; margin-bottom: 10px;">
                                <?php echo htmlspecialchars($row['scheme_name']); ?>
                            </h2>
                            <p class="item-desc" style="font-size: 1rem; color: #37474f; line-height: 1.7;">
                                <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                            </p>

                            <div class="scheme-details">
                                <p>
                                    <strong>Eligibility Criteria:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($row['eligibility'])); ?>
                                </p>
                                <p style="margin-top: 10px;">
                                    <strong>Application Procedure:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($row['procedure_info'])); ?>
                                </p>
                                <?php if (!empty($row['additional_info'])): ?>
                                    <p style="margin-top: 10px;">
                                        <strong>Additional Notes & Support:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($row['additional_info'])); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 40px;">
                    <h3 style="color: #78909c;">No Schemes Listed Currently</h3>
                    <p style="color: #90a4ae; margin-top: 8px;">Welfare scheme guidelines are being updated by the village administration.</p>
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


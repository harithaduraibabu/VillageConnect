<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once "config/db.php";

// Fetch latest 3 announcements for homepage
$announcements_query = "SELECT * FROM announcements ORDER BY published_date DESC LIMIT 3";
$announcements_res = mysqli_query($conn, $announcements_query);

// Fetch latest 3 government schemes for homepage
$schemes_query = "SELECT * FROM government_schemes ORDER BY id DESC LIMIT 3";
$schemes_res = mysqli_query($conn, $schemes_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VillageConnect – Community Portal for Village Democracy</title>
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
                    <span style="color: #c8e6c9; font-size: 0.9rem;">Logged in as Admin (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="announcements.php">Announcements</a></li>
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

    <!-- Welcome Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <h1>Empowering Rural Citizens Through Digital Democracy</h1>
            <p>
                A unified community web portal bridging communication between village citizens and local authorities. 
                Register grievances, stay informed with public announcements, explore welfare schemes, and share valuable feedback.
            </p>
            <div class="hero-actions">
                <a href="citizen/submit_grievance.php" class="btn btn-primary">Submit a Grievance</a>
                <a href="schemes.php" class="btn btn-secondary">Explore Welfare Schemes</a>
                <a href="announcements.php" class="btn btn-outline">Read Announcements</a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">

            <!-- About Section -->
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">About the Village Community Portal</h2>
                </div>
                <p style="margin-bottom: 15px; color: #37474f;">
                    The <strong>Community Portal for Village Democracy</strong> is an academic web-based platform designed to facilitate transparent, accountable, and accessible local governance. In traditional village administration, citizens often need to travel long distances to Panchayat offices to report simple issues like broken streetlights or water pipe leakages.
                </p>
                <div class="grid-4" style="margin-top: 20px;">
                    <div style="background: #e8f5e9; padding: 15px; border-radius: 6px; text-align: center;">
                        <h3 style="color: #1b5e20; font-size: 1.1rem; margin-bottom: 8px;">Public Notices</h3>
                        <p style="font-size: 0.88rem; color: #455a64;">Instant access to Gram Sabha meetings, health camps, and maintenance schedules.</p>
                    </div>
                    <div style="background: #e1f5fe; padding: 15px; border-radius: 6px; text-align: center;">
                        <h3 style="color: #0277bd; font-size: 1.1rem; margin-bottom: 8px;">Grievance Redressal</h3>
                        <p style="font-size: 0.88rem; color: #455a64;">Report water, sanitation, road, and power issues with real-time status tracking.</p>
                    </div>
                    <div style="background: #fff8e1; padding: 15px; border-radius: 6px; text-align: center;">
                        <h3 style="color: #f57f17; font-size: 1.1rem; margin-bottom: 8px;">Welfare Schemes</h3>
                        <p style="font-size: 0.88rem; color: #455a64;">Clear details on eligibility and procedures for rural development schemes.</p>
                    </div>
                    <div style="background: #f3e5f5; padding: 15px; border-radius: 6px; text-align: center;">
                        <h3 style="color: #7b1fa2; font-size: 1.1rem; margin-bottom: 8px;">Citizen Feedback</h3>
                        <p style="font-size: 0.88rem; color: #455a64;">Direct channel for suggestions to improve local infrastructure and services.</p>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <a href="citizen/submit_grievance.php" class="btn btn-primary btn-sm">Report a Grievance</a>
                    <a href="citizen/my_grievances.php" class="btn btn-outline btn-sm" style="margin-left: 8px;">View My Grievances</a>
                </div>
            </section>

            <!-- Latest Announcements & Featured Schemes Grid -->
            <div class="grid-2">

                <!-- Latest Announcements Section -->
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Latest Announcements</h2>
                        <a href="announcements.php" class="btn btn-outline btn-sm">View All &rarr;</a>
                    </div>
                    <p style="margin-bottom: 15px; color: #555555;">
                        Check important notices, village meetings, health checkup camps, and work schedules published by the village administration.
                    </p>

                    <?php if ($announcements_res && mysqli_num_rows($announcements_res) > 0): ?>
                        <?php while ($ann = mysqli_fetch_assoc($announcements_res)): ?>
                            <div class="announcement-item" style="margin-bottom: 12px; padding: 12px;">
                                <div class="item-meta">
                                    <span>Published: <?php echo date("F d, Y", strtotime($ann['published_date'])); ?></span>
                                </div>
                                <h3 class="item-title" style="font-size: 1.05rem;"><?php echo htmlspecialchars($ann['title']); ?></h3>
                                <p class="item-desc" style="margin-bottom: 0;">
                                    <?php 
                                    $desc = htmlspecialchars($ann['description']);
                                    echo (strlen($desc) > 130) ? substr($desc, 0, 130) . "..." : $desc;
                                    ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #78909c;">No public announcements have been posted yet.</p>
                    <?php endif; ?>
                </section>

                <!-- Featured Government Schemes Section -->
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Government Schemes</h2>
                        <a href="schemes.php" class="btn btn-outline btn-sm">View All &rarr;</a>
                    </div>
                    <p style="margin-bottom: 15px; color: #555555;">
                        Find simple awareness information about rural welfare schemes, eligibility rules, and application procedures.
                    </p>

                    <?php if ($schemes_res && mysqli_num_rows($schemes_res) > 0): ?>
                        <?php while ($scheme = mysqli_fetch_assoc($schemes_res)): ?>
                            <div class="scheme-item" style="margin-bottom: 12px; padding: 12px;">
                                <h3 class="item-title" style="font-size: 1.05rem;"><?php echo htmlspecialchars($scheme['scheme_name']); ?></h3>
                                <p class="item-desc" style="margin-bottom: 8px;">
                                    <?php 
                                    $sdesc = htmlspecialchars($scheme['description']);
                                    echo (strlen($sdesc) > 130) ? substr($sdesc, 0, 130) . "..." : $sdesc;
                                    ?>
                                </p>
                                <div class="scheme-details" style="padding: 8px 10px; font-size: 0.85rem;">
                                    <strong>Eligibility:</strong> <?php echo htmlspecialchars(substr($scheme['eligibility'], 0, 95)) . "..."; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #78909c;">No government schemes are currently listed.</p>
                    <?php endif; ?>
                </section>

            </div>

            <!-- Citizen Grievance Redressal Banner -->
            <section class="card" style="background: linear-gradient(to right, #ffffff, #e8f5e9); margin-top: 25px; border-left: 6px solid #2e7d32;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h3 style="color: #1b5e20; font-size: 1.3rem; margin-bottom: 6px;">Have a Problem in Your Village?</h3>
                        <p style="color: #455a64; margin-bottom: 0;">
                            Report issues with drinking water, street lighting, drainage, or road damages directly to local administration.
                        </p>
                    </div>
                    <div>
                        <a href="citizen/submit_grievance.php" class="btn btn-primary">File a Grievance Now</a>
                    </div>
                </div>
            </section>

            <!-- Citizen Feedback Section -->
            <section class="card" style="margin-top: 25px;">
                <div class="card-header">
                    <h2 class="card-title">Citizen Feedback</h2>
                </div>
                <p style="margin-bottom: 15px; color: #555555;">
                    Have suggestions to improve village roads, water management, or local facilities? Share your feedback directly with village authorities.
                </p>
                <div>
                    <a href="citizen/feedback.php" class="btn btn-primary btn-sm">Submit Feedback</a>
                </div>
            </section>

        </div>
    </main>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col" style="flex: 2; min-width: 260px;">
                    <h4>VillageConnect – Community Portal for Village Democracy</h4>
                    <p>
                        A digital civic engagement platform designed to empower village citizens and promote democratic accountability through online grievance redressal and transparent information sharing.
                    </p>
                </div>
                <div class="footer-col" style="flex: 1; min-width: 180px;">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="announcements.php">Public Announcements</a></li>
                        <li><a href="schemes.php">Government Schemes</a></li>
                        <li><a href="citizen/submit_grievance.php">Grievance Reporting</a></li>
                    </ul>
                </div>
                <div class="footer-col" style="flex: 1; min-width: 180px;">
                    <h4>Portal Access</h4>
                    <ul>
                        <li><a href="login.php">Citizen Sign In</a></li>
                        <li><a href="register.php">Citizen Register</a></li>
                        <li><a href="admin/login.php">Admin Login</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> VillageConnect – Community Portal for Village Democracy | B.Tech CSE College Demonstration Project | Academic Purpose Only</p>
                <p style="margin-top: 4px; font-size: 0.78rem; color: #c8e6c9;">Note: This is a demonstration student project and is not connected to official government servers.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>

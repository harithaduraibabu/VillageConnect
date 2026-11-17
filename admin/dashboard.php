<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check: Admin session required
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once "../config/db.php";

$admin_username = $_SESSION['admin_username'] ?? 'admin';
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

// 1. Total Registered Citizens
$cit_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM citizens");
$total_citizens = mysqli_fetch_assoc($cit_res)['count'] ?? 0;

// 2. Grievance Statistics
$total_grievances = 0;
$pending_grievances = 0;
$inprogress_grievances = 0;
$resolved_grievances = 0;

$grv_res = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM grievances GROUP BY status");
while ($r = mysqli_fetch_assoc($grv_res)) {
    $total_grievances += $r['count'];
    if ($r['status'] === 'Pending') {
        $pending_grievances = $r['count'];
    } elseif ($r['status'] === 'In Progress') {
        $inprogress_grievances = $r['count'];
    } elseif ($r['status'] === 'Resolved') {
        $resolved_grievances = $r['count'];
    }
}

// 3. Total Announcements
$ann_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM announcements");
$total_announcements = mysqli_fetch_assoc($ann_res)['count'] ?? 0;

// 4. Total Government Schemes
$sch_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM government_schemes");
$total_schemes = mysqli_fetch_assoc($sch_res)['count'] ?? 0;

// 5. Total Feedback Submissions
$fb_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM feedback");
$total_feedback = mysqli_fetch_assoc($fb_res)['count'] ?? 0;

// Fetch Recent Grievances with Citizen Details
$recent_query = "SELECT g.id, g.title, g.category, g.status, g.created_at, c.full_name as citizen_name, c.village_name 
                 FROM grievances g 
                 JOIN citizens c ON g.citizen_id = c.id 
                 ORDER BY g.created_at DESC 
                 LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VillageConnect</title>
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
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_citizens.php">Citizens</a></li>
                <li><a href="manage_grievances.php">Grievances</a></li>
                <li><a href="manage_announcements.php">Announcements</a></li>
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

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
                <div>
                    <h1 class="section-title" style="margin-bottom: 5px;">Administrative Overview</h1>
                    <p class="section-desc" style="margin-bottom: 0;">
                        Monitor village operations, grievance redressal metrics, and public service notices.
                    </p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="add_announcement.php" class="btn btn-primary btn-sm">Add Announcement</a>
                    <a href="add_scheme.php" class="btn btn-secondary btn-sm">Add Scheme</a>
                </div>
            </div>

            <!-- Key Metric Cards Grid -->
            <div class="grid-4" style="margin-bottom: 25px;">
                <div class="stat-card">
                    <div class="stat-label">Registered Citizens</div>
                    <div class="stat-value"><?php echo $total_citizens; ?></div>
                    <a href="manage_citizens.php" style="color: #2e7d32; font-size: 0.82rem; font-weight: 600;">View Citizens &rarr;</a>
                </div>

                <div class="stat-card orange">
                    <div class="stat-label">Pending Grievances</div>
                    <div class="stat-value"><?php echo $pending_grievances; ?></div>
                    <a href="manage_grievances.php?status=Pending" style="color: #f57c00; font-size: 0.82rem; font-weight: 600;">Take Action &rarr;</a>
                </div>

                <div class="stat-card blue">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value"><?php echo $inprogress_grievances; ?></div>
                    <a href="manage_grievances.php?status=In Progress" style="color: #0288d1; font-size: 0.82rem; font-weight: 600;">Track Progress &rarr;</a>
                </div>

                <div class="stat-card teal">
                    <div class="stat-label">Resolved Grievances</div>
                    <div class="stat-value"><?php echo $resolved_grievances; ?></div>
                    <a href="manage_grievances.php?status=Resolved" style="color: #00897b; font-size: 0.82rem; font-weight: 600;">View Resolved &rarr;</a>
                </div>
            </div>

            <div class="grid-3" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <div class="stat-label">Total Grievances (All)</div>
                    <div class="stat-value"><?php echo $total_grievances; ?></div>
                    <a href="manage_grievances.php" style="color: #2e7d32; font-size: 0.82rem; font-weight: 600;">Manage All Grievances &rarr;</a>
                </div>

                <div class="stat-card blue">
                    <div class="stat-label">Announcements Published</div>
                    <div class="stat-value"><?php echo $total_announcements; ?></div>
                    <a href="manage_announcements.php" style="color: #0288d1; font-size: 0.82rem; font-weight: 600;">Manage Notices &rarr;</a>
                </div>

                <div class="stat-card teal">
                    <div class="stat-label">Government Schemes Listed</div>
                    <div class="stat-value"><?php echo $total_schemes; ?></div>
                    <a href="manage_schemes.php" style="color: #00897b; font-size: 0.82rem; font-weight: 600;">Manage Schemes &rarr;</a>
                </div>
            </div>

            <!-- Recent Grievance Submissions Table -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Latest Grievances Requiring Administrative Action</h2>
                    <a href="manage_grievances.php" class="btn btn-outline btn-sm">View All Grievances</a>
                </div>

                <?php if ($recent_result && mysqli_num_rows($recent_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Citizen & Village</th>
                                    <th>Grievance Title</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($recent_result)): ?>
                                    <tr>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['citizen_name']); ?></strong><br>
                                            <span style="font-size: 0.82rem; color: #78909c;">Village: <?php echo htmlspecialchars($row['village_name']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td>
                                            <span style="font-size: 0.82rem; background: #e0f2f1; color: #00695c; padding: 2px 6px; border-radius: 4px;">
                                                <?php echo htmlspecialchars($row['category']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <?php 
                                            $s = $row['status'];
                                            if ($s === 'Pending') {
                                                echo '<span class="badge badge-pending">Pending</span>';
                                            } elseif ($s === 'In Progress') {
                                                echo '<span class="badge badge-in-progress">In Progress</span>';
                                            } elseif ($s === 'Resolved') {
                                                echo '<span class="badge badge-resolved">Resolved</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="update_grievance.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                Review / Update
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: #78909c; text-align: center; padding: 25px 0;">No grievances recorded in the database yet.</p>
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

</body>
</html>


<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check: Ensure citizen is logged in
if (!isset($_SESSION['citizen_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once "../config/db.php";

$citizen_id = $_SESSION['citizen_id'];
$citizen_name = $_SESSION['citizen_name'] ?? 'Citizen';
$village_name = $_SESSION['citizen_village'] ?? 'Village';

// Fetch Grievance Statistics for this citizen
$stats = [
    'total'       => 0,
    'pending'     => 0,
    'in_progress' => 0,
    'resolved'    => 0
];

$stat_sql = "SELECT status, COUNT(*) as count FROM grievances WHERE citizen_id = ? GROUP BY status";
$stmt = mysqli_prepare($conn, $stat_sql);
mysqli_stmt_bind_param($stmt, "i", $citizen_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($res)) {
    $stats['total'] += $row['count'];
    if ($row['status'] === 'Pending') {
        $stats['pending'] = $row['count'];
    } elseif ($row['status'] === 'In Progress') {
        $stats['in_progress'] = $row['count'];
    } elseif ($row['status'] === 'Resolved') {
        $stats['resolved'] = $row['count'];
    }
}
mysqli_stmt_close($stmt);

// Fetch recent 5 grievances of this citizen
$recent_sql = "SELECT id, title, category, status, admin_remark, created_at 
               FROM grievances 
               WHERE citizen_id = ? 
               ORDER BY created_at DESC 
               LIMIT 5";
$recent_stmt = mysqli_prepare($conn, $recent_sql);
mysqli_stmt_bind_param($recent_stmt, "i", $citizen_id);
mysqli_stmt_execute($recent_stmt);
$recent_grievances = mysqli_stmt_get_result($recent_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Dashboard - VillageConnect</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Header Section -->
    <header class="top-header">
        <div class="container header-content">
            <div>
                <a href="../index.php" class="brand-title">
                    VillageConnect
                </a>
                <span class="brand-subtitle">Community Portal for Village Democracy</span>
            </div>
            <div>
                <span style="color: #c8e6c9; font-size: 0.92rem;">
                    Citizen: <strong><?php echo htmlspecialchars($citizen_name); ?></strong> | Village: <strong><?php echo htmlspecialchars($village_name); ?></strong>
                </span>
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="dashboard.php" class="active">Citizen Dashboard</a></li>
                <li><a href="submit_grievance.php">Submit Grievance</a></li>
                <li><a href="my_grievances.php">My Grievances</a></li>
                <li><a href="../announcements.php">Announcements</a></li>
                <li><a href="../schemes.php">Government Schemes</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li class="nav-right"><a href="../logout.php" class="nav-highlight">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">

            <!-- Welcome Greeting Box -->
            <div class="card" style="background: linear-gradient(135deg, #e8f5e9 0%, #e1f5fe 100%); border-left: 6px solid #2e7d32; margin-bottom: 25px;">
                <h1 style="color: #1b5e20; font-size: 1.6rem; margin-bottom: 6px;">
                    Welcome back, <?php echo htmlspecialchars($citizen_name); ?>!
                </h1>
                <p style="color: #37474f; margin-bottom: 0;">
                    You are connected to the <strong><?php echo htmlspecialchars($village_name); ?></strong> Village Civic Administration. 
                    From this dashboard, you can track reported issues, submit new grievances, and participate in local governance.
                </p>
            </div>

            <!-- Grievance Statistics Counter Cards -->
            <div class="grid-4" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <div class="stat-label">Total Grievances</div>
                    <div class="stat-value"><?php echo $stats['total']; ?></div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-label">Pending Review</div>
                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                </div>

                <div class="stat-card blue">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value"><?php echo $stats['in_progress']; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value"><?php echo $stats['resolved']; ?></div>
                </div>
            </div>

            <!-- Quick Service Actions Grid -->
            <h2 class="section-title">Citizen Services</h2>
            <div class="grid-3" style="margin-bottom: 30px;">
                <div class="card" style="border-top: 4px solid #2e7d32;">
                    <h3 style="color: #1b5e20; font-size: 1.15rem; margin-bottom: 8px;">Report Grievance</h3>
                    <p style="color: #546e7a; font-size: 0.9rem; margin-bottom: 15px;">
                        Report an issue with drinking water, roads, electricity, or sanitation in your street.
                    </p>
                    <a href="submit_grievance.php" class="btn btn-primary btn-sm btn-block">New Grievance</a>
                </div>

                <div class="card" style="border-top: 4px solid #0288d1;">
                    <h3 style="color: #0277bd; font-size: 1.15rem; margin-bottom: 8px;">My Grievances</h3>
                    <p style="color: #546e7a; font-size: 0.9rem; margin-bottom: 15px;">
                        Track the ongoing progress, resolution status, and remarks from village officials.
                    </p>
                    <a href="my_grievances.php" class="btn btn-secondary btn-sm btn-block">View History</a>
                </div>

                <div class="card" style="border-top: 4px solid #7b1fa2;">
                    <h3 style="color: #7b1fa2; font-size: 1.15rem; margin-bottom: 8px;">Citizen Feedback</h3>
                    <p style="color: #546e7a; font-size: 0.9rem; margin-bottom: 15px;">
                        Share your opinions and suggestions to improve village administration and facilities.
                    </p>
                    <a href="feedback.php" class="btn btn-outline btn-sm btn-block">Send Feedback</a>
                </div>
            </div>

            <!-- Recent Grievance Activity Table -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Grievances Submitted By You</h2>
                    <a href="my_grievances.php" class="btn btn-outline btn-sm">View All Grievances</a>
                </div>

                <?php if ($recent_grievances && mysqli_num_rows($recent_grievances) > 0): ?>
                    <div class="table-responsive">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Submitted On</th>
                                    <th>Status</th>
                                    <th>Admin Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($g = mysqli_fetch_assoc($recent_grievances)): ?>
                                    <tr>
                                        <td>#<?php echo $g['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($g['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($g['category']); ?></td>
                                        <td><?php echo date("d M Y", strtotime($g['created_at'])); ?></td>
                                        <td>
                                            <?php 
                                            $st = $g['status'];
                                            if ($st === 'Pending') {
                                                echo '<span class="badge badge-pending">Pending</span>';
                                            } elseif ($st === 'In Progress') {
                                                echo '<span class="badge badge-in-progress">In Progress</span>';
                                            } elseif ($st === 'Resolved') {
                                                echo '<span class="badge badge-resolved">Resolved</span>';
                                            } else {
                                                echo '<span class="badge">' . htmlspecialchars($st) . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if (!empty($g['admin_remark'])) {
                                                echo '<span style="color: #2e7d32;">' . htmlspecialchars($g['admin_remark']) . '</span>';
                                            } else {
                                                echo '<span style="color: #9e9e9e; font-style: italic;">Awaiting review</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: #78909c; padding: 20px 0; text-align: center;">
                        You have not submitted any grievances yet. 
                        <a href="submit_grievance.php" style="color: #2e7d32; font-weight: 600;">Click here to report an issue.</a>
                    </p>
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


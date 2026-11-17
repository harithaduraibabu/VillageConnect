<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset($_SESSION['citizen_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once "../config/db.php";

$citizen_id = $_SESSION['citizen_id'];
$citizen_name = $_SESSION['citizen_name'] ?? 'Citizen';

// Filter by status if provided
$status_filter = trim($_GET['status'] ?? '');
$params = [$citizen_id];
$types = "i";

$query = "SELECT id, title, category, description, status, admin_remark, created_at, updated_at 
          FROM grievances 
          WHERE citizen_id = ?";

if (!empty($status_filter) && in_array($status_filter, ['Pending', 'In Progress', 'Resolved'])) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grievances - VillageConnect</title>
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
                    Citizen: <strong><?php echo htmlspecialchars($citizen_name); ?></strong>
                </span>
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="dashboard.php">Citizen Dashboard</a></li>
                <li><a href="submit_grievance.php">Submit Grievance</a></li>
                <li><a href="my_grievances.php" class="active">My Grievances</a></li>
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
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
                <div>
                    <h1 class="section-title" style="margin-bottom: 5px;">My Grievance History</h1>
                    <p class="section-desc" style="margin-bottom: 0;">
                        Track your complaints, verification updates, and actions taken by village administrators.
                    </p>
                </div>
                <div>
                    <a href="submit_grievance.php" class="btn btn-primary">Submit New Grievance</a>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="card" style="padding: 15px; margin-bottom: 20px;">
                <form action="my_grievances.php" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <span style="font-weight: 600; font-size: 0.92rem; color: #37474f;">Filter by Status:</span>
                    <a href="my_grievances.php" class="btn btn-sm <?php echo empty($status_filter) ? 'btn-primary' : 'btn-outline'; ?>">All</a>
                    <a href="my_grievances.php?status=Pending" class="btn btn-sm <?php echo ($status_filter === 'Pending') ? 'btn-primary' : 'btn-outline'; ?>">Pending</a>
                    <a href="my_grievances.php?status=In Progress" class="btn btn-sm <?php echo ($status_filter === 'In Progress') ? 'btn-primary' : 'btn-outline'; ?>">In Progress</a>
                    <a href="my_grievances.php?status=Resolved" class="btn btn-sm <?php echo ($status_filter === 'Resolved') ? 'btn-primary' : 'btn-outline'; ?>">Resolved</a>
                </form>
            </div>

            <!-- Grievance Table / List -->
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th style="width: 220px;">Title & Category</th>
                                <th>Description</th>
                                <th style="width: 120px;">Submitted On</th>
                                <th style="width: 110px;">Status</th>
                                <th style="width: 200px;">Authority Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td>
                                        <div style="font-weight: 600; color: #1b5e20;">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </div>
                                        <span style="font-size: 0.8rem; background: #e0f2f1; color: #00695c; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-top: 4px;">
                                            <?php echo htmlspecialchars($row['category']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem; color: #37474f; line-height: 1.5;">
                                            <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.88rem; color: #546e7a;">
                                            <?php echo date("d M Y", strtotime($row['created_at'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $s = $row['status'];
                                        if ($s === 'Pending') {
                                            echo '<span class="badge badge-pending">Pending</span>';
                                        } elseif ($s === 'In Progress') {
                                            echo '<span class="badge badge-in-progress">In Progress</span>';
                                        } elseif ($s === 'Resolved') {
                                            echo '<span class="badge badge-resolved">Resolved</span>';
                                        } else {
                                            echo '<span class="badge">' . htmlspecialchars($s) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['admin_remark'])): ?>
                                            <div style="font-size: 0.88rem; color: #1b5e20; background: #e8f5e9; padding: 6px 10px; border-radius: 4px; border-left: 3px solid #2e7d32;">
                                                <?php echo nl2br(htmlspecialchars($row['admin_remark'])); ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #90a4ae; font-size: 0.85rem; font-style: italic;">
                                                Pending official review
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 40px;">
                    <h3 style="color: #78909c;">No Grievances Found</h3>
                    <p style="color: #90a4ae; margin-top: 8px;">
                        <?php if (!empty($status_filter)): ?>
                            You have no grievances with status '<strong><?php echo htmlspecialchars($status_filter); ?></strong>'.
                        <?php else: ?>
                            You have not submitted any complaints yet.
                        <?php endif; ?>
                    </p>
                    <a href="submit_grievance.php" class="btn btn-primary btn-sm" style="margin-top: 15px;">Submit a Grievance</a>
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


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

// Search filter
$search = trim($_GET['search'] ?? '');
$params = [];
$types = "";

$query = "SELECT c.id, c.full_name, c.email, c.phone, c.village_name, c.created_at, 
                 COUNT(g.id) as grievance_count 
          FROM citizens c 
          LEFT JOIN grievances g ON c.id = g.citizen_id ";

if (!empty($search)) {
    $query .= " WHERE c.full_name LIKE ? OR c.email LIKE ? OR c.village_name LIKE ? ";
    $searchTerm = "%{$search}%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = "sss";
}

$query .= " GROUP BY c.id ORDER BY c.created_at DESC";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Citizens - VillageConnect</title>
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
                <li><a href="manage_citizens.php" class="active">Citizens</a></li>
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
                    <h1 class="section-title" style="margin-bottom: 5px;">Registered Village Citizens</h1>
                    <p class="section-desc" style="margin-bottom: 0;">
                        List of verified residents registered on the village community portal.
                    </p>
                </div>
            </div>

            <!-- Search Card -->
            <div class="card" style="padding: 16px; margin-bottom: 20px;">
                <form action="manage_citizens.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <input type="text" name="search" class="form-control" style="max-width: 380px;" 
                           placeholder="Search by name, email or village..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="manage_citizens.php" class="btn btn-outline btn-sm">Clear Filter</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Citizens Table -->
            <div class="card">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Citizen Name</th>
                                    <th>Email Address</th>
                                    <th>Phone Number</th>
                                    <th>Village Name</th>
                                    <th>Grievances Filed</th>
                                    <th>Registered Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($citizen = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td>#<?php echo $citizen['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($citizen['full_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($citizen['email']); ?></td>
                                        <td><?php echo htmlspecialchars($citizen['phone']); ?></td>
                                        <td>
                                            <span style="background: #e8f5e9; color: #1b5e20; padding: 2px 8px; border-radius: 4px; font-weight: 500;">
                                                <?php echo htmlspecialchars($citizen['village_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="manage_grievances.php?citizen_id=<?php echo $citizen['id']; ?>" style="color: #0288d1; font-weight: 600;">
                                                <?php echo $citizen['grievance_count']; ?> Grievance(s)
                                            </a>
                                        </td>
                                        <td><?php echo date("d M Y", strtotime($citizen['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #78909c; padding: 30px 0;">
                        No citizen records found.
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


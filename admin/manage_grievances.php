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

// Filters
$status_filter = trim($_GET['status'] ?? '');
$category_filter = trim($_GET['category'] ?? '');
$citizen_filter = isset($_GET['citizen_id']) ? intval($_GET['citizen_id']) : 0;

$where_clauses = [];
$params = [];
$types = "";

if (!empty($status_filter) && in_array($status_filter, ['Pending', 'In Progress', 'Resolved'])) {
    $where_clauses[] = "g.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($category_filter)) {
    $where_clauses[] = "g.category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

if ($citizen_filter > 0) {
    $where_clauses[] = "g.citizen_id = ?";
    $params[] = $citizen_filter;
    $types .= "i";
}

$sql = "SELECT g.id, g.title, g.category, g.description, g.status, g.admin_remark, g.created_at, 
               c.full_name as citizen_name, c.email as citizen_email, c.phone as citizen_phone, c.village_name 
        FROM grievances g 
        JOIN citizens c ON g.citizen_id = c.id ";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY g.created_at DESC";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

$categories = ["Water Supply", "Road Maintenance", "Electricity", "Sanitation", "Street Lights", "Other"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grievances - VillageConnect</title>
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
                <li><a href="manage_grievances.php" class="active">Grievances</a></li>
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
                    <h1 class="section-title" style="margin-bottom: 5px;">Citizen Grievance Redressal</h1>
                    <p class="section-desc" style="margin-bottom: 0;">
                        Review community issues, update resolution status, and post official progress remarks.
                    </p>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card" style="padding: 16px; margin-bottom: 20px;">
                <form action="manage_grievances.php" method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="status" style="font-size: 0.9rem; font-weight: 600;">Status:</label>
                        <select name="status" id="status" class="form-control" style="width: auto;">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php echo ($status_filter === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Progress" <?php echo ($status_filter === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Resolved" <?php echo ($status_filter === 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                        </select>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="category" style="font-size: 0.9rem; font-weight: 600;">Category:</label>
                        <select name="category" id="category" class="form-control" style="width: auto;">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category_filter === $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <?php if (!empty($status_filter) || !empty($category_filter) || $citizen_filter > 0): ?>
                        <a href="manage_grievances.php" class="btn btn-outline btn-sm">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Grievance Table -->
            <div class="card">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Citizen & Village</th>
                                    <th>Grievance Title & Category</th>
                                    <th>Description</th>
                                    <th>Submitted On</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['citizen_name']); ?></strong><br>
                                            <span style="font-size: 0.8rem; color: #78909c;">Village: <?php echo htmlspecialchars($row['village_name']); ?></span><br>
                                            <span style="font-size: 0.8rem; color: #78909c;">Phone: <?php echo htmlspecialchars($row['citizen_phone']); ?></span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1b5e20;">
                                                <?php echo htmlspecialchars($row['title']); ?>
                                            </div>
                                            <span style="font-size: 0.8rem; background: #e0f2f1; color: #00695c; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-top: 3px;">
                                                <?php echo htmlspecialchars($row['category']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.88rem; color: #455a64; max-width: 250px;">
                                                <?php 
                                                $desc = htmlspecialchars($row['description']);
                                                echo (strlen($desc) > 90) ? substr($desc, 0, 90) . '...' : $desc;
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.85rem; color: #546e7a;">
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
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="update_grievance.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                Update Status
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #78909c; padding: 30px 0;">
                        No grievances match the specified criteria.
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


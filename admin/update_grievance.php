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
    header("Location: manage_grievances.php");
    exit();
}

$error_msg = "";
$success_msg = "";

// Handle status update POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $status = trim($_POST['status'] ?? '');
    $admin_remark = trim($_POST['admin_remark'] ?? '');

    $allowed_statuses = ['Pending', 'In Progress', 'Resolved'];

    if (!in_array($status, $allowed_statuses)) {
        $error_msg = "Invalid status selected.";
    } else {
        $update_sql = "UPDATE grievances SET status = ?, admin_remark = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ssi", $status, $admin_remark, $id);

        if (mysqli_stmt_execute($update_stmt)) {
            $success_msg = "Grievance status and official remarks updated successfully!";
        } else {
            $error_msg = "Failed to update grievance. Please try again.";
        }
        mysqli_stmt_close($update_stmt);
    }
}

// Fetch grievance with citizen details
$sql = "SELECT g.*, c.full_name as citizen_name, c.email as citizen_email, c.phone as citizen_phone, c.village_name 
        FROM grievances g 
        JOIN citizens c ON g.citizen_id = c.id 
        WHERE g.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$grievance = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$grievance) {
    die("Grievance not found. <a href='manage_grievances.php'>Return to Grievance List</a>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Grievance #<?php echo $grievance['id']; ?> - VillageConnect</title>
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
            <div style="margin-bottom: 20px;">
                <a href="manage_grievances.php" style="color: #2e7d32; font-weight: 600; text-decoration: none;">&larr; Back to Grievance List</a>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_msg); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <div class="grid-2">

                <!-- Left Column: Grievance & Citizen Details -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Grievance #<?php echo $grievance['id']; ?> Details</h2>
                        <span class="badge <?php 
                            if ($grievance['status'] === 'Pending') echo 'badge-pending';
                            elseif ($grievance['status'] === 'In Progress') echo 'badge-in-progress';
                            elseif ($grievance['status'] === 'Resolved') echo 'badge-resolved';
                        ?>">
                            <?php echo htmlspecialchars($grievance['status']); ?>
                        </span>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <h3 style="color: #1b5e20; font-size: 1.2rem; margin-bottom: 6px;">
                            <?php echo htmlspecialchars($grievance['title']); ?>
                        </h3>
                        <span style="font-size: 0.82rem; background: #e0f2f1; color: #00695c; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                            Category: <?php echo htmlspecialchars($grievance['category']); ?>
                        </span>
                    </div>

                    <div style="background-color: #f9fbfb; border: 1px solid #e0e0e0; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                        <strong style="color: #37474f; font-size: 0.9rem;">Citizen's Description:</strong>
                        <p style="color: #263238; line-height: 1.6; margin-top: 6px; white-space: pre-line;">
                            <?php echo htmlspecialchars($grievance['description']); ?>
                        </p>
                    </div>

                    <h4 style="color: #37474f; font-size: 0.95rem; border-bottom: 1px solid #e0e0e0; padding-bottom: 5px; margin-bottom: 10px;">
                        Citizen Information
                    </h4>
                    <ul style="list-style: none; font-size: 0.9rem; line-height: 1.8; color: #455a64;">
                        <li><strong>Name:</strong> <?php echo htmlspecialchars($grievance['citizen_name']); ?></li>
                        <li><strong>Village:</strong> <?php echo htmlspecialchars($grievance['village_name']); ?></li>
                        <li><strong>Email:</strong> <?php echo htmlspecialchars($grievance['citizen_email']); ?></li>
                        <li><strong>Phone:</strong> <?php echo htmlspecialchars($grievance['citizen_phone']); ?></li>
                        <li><strong>Submitted Date:</strong> <?php echo date("d M Y, h:i A", strtotime($grievance['created_at'])); ?></li>
                    </ul>
                </div>

                <!-- Right Column: Status & Remark Form -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Status & Admin Remarks</h2>
                    </div>

                    <form action="update_grievance.php?id=<?php echo $grievance['id']; ?>" method="POST">
                        <div class="form-group">
                            <label for="status">Redressal Status <span class="req">*</span></label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="Pending" <?php echo ($grievance['status'] === 'Pending') ? 'selected' : ''; ?>>Pending (Under Initial Review)</option>
                                <option value="In Progress" <?php echo ($grievance['status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress (Assigned / Action Underway)</option>
                                <option value="Resolved" <?php echo ($grievance['status'] === 'Resolved') ? 'selected' : ''; ?>>Resolved (Issue Fixed / Completed)</option>
                            </select>
                            <span class="form-help">Updating status immediately reflects on the citizen's personal dashboard.</span>
                        </div>

                        <div class="form-group">
                            <label for="admin_remark">Official Admin Remarks / Action Taken</label>
                            <textarea id="admin_remark" name="admin_remark" class="form-control" rows="6" 
                                      placeholder="e.g. Technician dispatched, inspection scheduled for tomorrow, or pipeline repaired successfully..."><?php echo htmlspecialchars($grievance['admin_remark'] ?? ''); ?></textarea>
                            <span class="form-help">This explanation is visible to the citizen who filed the grievance.</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; margin-top: 15px;">
                            Save Changes & Notify Citizen
                        </button>
                    </form>
                </div>

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


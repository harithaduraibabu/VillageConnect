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

$error_msg = "";
$success_msg = "";

// Categories allowed
$categories = [
    "Water Supply",
    "Road Maintenance",
    "Electricity",
    "Sanitation",
    "Street Lights",
    "Other"
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST["title"] ?? "");
    $category    = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (empty($title) || empty($category) || empty($description)) {
        $error_msg = "All fields are required. Please fill out the form completely.";
    } elseif (!in_array($category, $categories)) {
        $error_msg = "Please select a valid grievance category.";
    } elseif (strlen($description) < 15) {
        $error_msg = "Please provide a more detailed description (at least 15 characters).";
    } else {
        $sql = "INSERT INTO grievances (citizen_id, title, category, description, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isss", $citizen_id, $title, $category, $description);

        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($conn);
            $success_msg = "Grievance #{$new_id} has been submitted successfully! The local village administration will review it shortly.";
            // Clear inputs on success
            $title = "";
            $category = "";
            $description = "";
        } else {
            $error_msg = "An error occurred while saving your grievance. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Grievance - VillageConnect</title>
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
                <li><a href="submit_grievance.php" class="active">Submit Grievance</a></li>
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
            <div class="form-box" style="max-width: 720px;">
                <h2 style="color: #1b5e20; margin-bottom: 6px;">Report a Village Grievance</h2>
                <p style="color: #546e7a; font-size: 0.95rem; margin-bottom: 25px;">
                    Submit a formal complaint or request to your village administration. You can track progress and remarks from officials.
                </p>

                <!-- Feedback Messages -->
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success">
                        <strong>Success:</strong> <?php echo htmlspecialchars($success_msg); ?>
                        <div style="margin-top: 10px;">
                            <a href="my_grievances.php" class="btn btn-primary btn-sm">View in My Grievances</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="submit_grievance.php" method="POST" onsubmit="return validateGrievanceForm();">
                    <div class="form-group">
                        <label for="title">Grievance Title <span class="req">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="<?php echo htmlspecialchars($title ?? ''); ?>" 
                               placeholder="e.g. Broken drainage cover near community hall" required>
                        <span class="form-help">Provide a brief, clear summary of the problem.</span>
                    </div>

                    <div class="form-group">
                        <label for="category">Category <span class="req">*</span></label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">-- Select a Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo (isset($category) && $category === $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Detailed Description <span class="req">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="6" 
                                  placeholder="Describe the exact location, duration of the issue, and impact on residents..." required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                        <span class="form-help">Include landmarks, ward number, or street names to help officials locate the issue quickly.</span>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 25px;">
                        <button type="submit" class="btn btn-primary" style="flex: 2; padding: 12px;">
                            Submit Grievance
                        </button>
                        <a href="dashboard.php" class="btn btn-outline" style="flex: 1; padding: 12px;">
                            Cancel
                        </a>
                    </div>
                </form>
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

    <script src="../assets/js/script.js"></script>
</body>
</html>


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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if (empty($subject) || empty($message)) {
        $error_msg = "Please fill in all feedback fields.";
    } elseif (strlen($message) < 10) {
        $error_msg = "Please provide more details in your feedback message (at least 10 characters).";
    } else {
        $sql = "INSERT INTO feedback (citizen_id, subject, message) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $citizen_id, $subject, $message);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Thank you! Your feedback has been submitted successfully to the village administration.";
            $subject = "";
            $message = "";
        } else {
            $error_msg = "Failed to submit feedback due to a database error. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch feedback previously submitted by this citizen
$prev_sql = "SELECT id, subject, message, created_at FROM feedback WHERE citizen_id = ? ORDER BY created_at DESC";
$prev_stmt = mysqli_prepare($conn, $prev_sql);
mysqli_stmt_bind_param($prev_stmt, "i", $citizen_id);
mysqli_stmt_execute($prev_stmt);
$prev_res = mysqli_stmt_get_result($prev_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - VillageConnect</title>
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
                <li><a href="my_grievances.php">My Grievances</a></li>
                <li><a href="../announcements.php">Announcements</a></li>
                <li><a href="../schemes.php">Government Schemes</a></li>
                <li><a href="feedback.php" class="active">Feedback</a></li>
                <li class="nav-right"><a href="../logout.php" class="nav-highlight">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-box" style="max-width: 720px; margin-bottom: 35px;">
                <h2 style="color: #1b5e20; margin-bottom: 6px;">Submit Citizen Feedback & Suggestions</h2>
                <p style="color: #546e7a; font-size: 0.95rem; margin-bottom: 25px;">
                    Your constructive feedback helps the Panchayat council and local authorities prioritize community development projects.
                </p>

                <!-- Feedback Messages -->
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

                <form action="feedback.php" method="POST" onsubmit="return validateFeedbackForm();">
                    <div class="form-group">
                        <label for="subject">Feedback Subject <span class="req">*</span></label>
                        <input type="text" id="subject" name="subject" class="form-control" 
                               value="<?php echo htmlspecialchars($subject ?? ''); ?>" 
                               placeholder="e.g. Suggestion for village park maintenance" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message / Suggestion <span class="req">*</span></label>
                        <textarea id="message" name="message" class="form-control" rows="5" 
                                  placeholder="Share your opinions, observations, or ideas for village betterment..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="flex: 2; padding: 12px;">
                            Submit Feedback
                        </button>
                        <a href="dashboard.php" class="btn btn-outline" style="flex: 1; padding: 12px;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Previously Submitted Feedback by this Citizen -->
            <div class="card" style="max-width: 720px; margin: 0 auto;">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 1.15rem;">Your Feedback History</h3>
                </div>

                <?php if ($prev_res && mysqli_num_rows($prev_res) > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <?php while ($f = mysqli_fetch_assoc($prev_res)): ?>
                            <div style="background: #f9fbfb; border: 1px solid #e0e0e0; border-radius: 6px; padding: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <strong style="color: #1b5e20; font-size: 1rem;"><?php echo htmlspecialchars($f['subject']); ?></strong>
                                    <span style="font-size: 0.8rem; color: #78909c;">
                                        <?php echo date("d M Y, h:i A", strtotime($f['created_at'])); ?>
                                    </span>
                                </div>
                                <p style="color: #455a64; font-size: 0.92rem; line-height: 1.6; margin-bottom: 0;">
                                    <?php echo nl2br(htmlspecialchars($f['message'])); ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #78909c; text-align: center; padding: 15px 0;">
                        You have not submitted any feedback yet.
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

    <script src="../assets/js/script.js"></script>
</body>
</html>


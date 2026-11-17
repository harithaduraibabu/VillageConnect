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
$error_msg = "";

$scheme_name     = "";
$description     = "";
$eligibility     = "";
$procedure_info  = "";
$additional_info = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $scheme_name     = trim($_POST["scheme_name"] ?? "");
    $description     = trim($_POST["description"] ?? "");
    $eligibility     = trim($_POST["eligibility"] ?? "");
    $procedure_info  = trim($_POST["procedure_info"] ?? "");
    $additional_info = trim($_POST["additional_info"] ?? "");

    if (empty($scheme_name) || empty($description) || empty($eligibility) || empty($procedure_info)) {
        $error_msg = "Please fill in all required scheme fields (Name, Description, Eligibility, Procedure).";
    } else {
        $sql = "INSERT INTO government_schemes (scheme_name, description, eligibility, procedure_info, additional_info) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $scheme_name, $description, $eligibility, $procedure_info, $additional_info);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            $_SESSION['flash_success'] = "Government scheme added successfully!";
            header("Location: manage_schemes.php");
            exit();
        } else {
            $error_msg = "Failed to add government scheme. Please try again.";
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Scheme - VillageConnect</title>
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
                <li><a href="manage_grievances.php">Grievances</a></li>
                <li><a href="manage_announcements.php">Announcements</a></li>
                <li><a href="manage_schemes.php" class="active">Government Schemes</a></li>
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
                <a href="manage_schemes.php" style="color: #2e7d32; font-weight: 600; text-decoration: none;">&larr; Back to Schemes List</a>
            </div>

            <div class="form-box" style="max-width: 750px;">
                <h2 style="color: #1b5e20; margin-bottom: 6px;">Add New Government Scheme</h2>
                <p style="color: #546e7a; font-size: 0.92rem; margin-bottom: 25px;">
                    Provide clear, structured awareness information on welfare and rural development schemes.
                </p>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="add_scheme.php" method="POST">
                    <div class="form-group">
                        <label for="scheme_name">Scheme Name <span class="req">*</span></label>
                        <input type="text" id="scheme_name" name="scheme_name" class="form-control" 
                               value="<?php echo htmlspecialchars($scheme_name); ?>" 
                               placeholder="e.g. Pradhan Mantri Kisan Samman Nidhi" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Scheme Description / Objective <span class="req">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="4" 
                                  placeholder="Describe the overall purpose, benefits, and coverage of the scheme..." required><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="eligibility">Eligibility Criteria <span class="req">*</span></label>
                        <textarea id="eligibility" name="eligibility" class="form-control" rows="4" 
                                  placeholder="Specify who is eligible (landholding limits, income status, family criteria, age)..." required><?php echo htmlspecialchars($eligibility); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="procedure_info">Application Procedure <span class="req">*</span></label>
                        <textarea id="procedure_info" name="procedure_info" class="form-control" rows="4" 
                                  placeholder="Explain how a citizen applies (Panchayat camp, BDO office, required identity proofs)..." required><?php echo htmlspecialchars($procedure_info); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="additional_info">Relevant Information / Official Contacts (Optional)</label>
                        <textarea id="additional_info" name="additional_info" class="form-control" rows="3" 
                                  placeholder="Any helpline numbers, nodal officer details, or special conditions..."><?php echo htmlspecialchars($additional_info); ?></textarea>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 25px;">
                        <button type="submit" class="btn btn-primary" style="flex: 2; padding: 12px;">
                            Save & Publish Scheme
                        </button>
                        <a href="manage_schemes.php" class="btn btn-outline" style="flex: 1; padding: 12px;">
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

</body>
</html>


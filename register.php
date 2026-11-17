<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in as citizen, redirect to citizen dashboard
if (isset($_SESSION['citizen_id'])) {
    header("Location: citizen/dashboard.php");
    exit();
}

// Include database connection
require_once "config/db.php";

$error_msg = "";
$success_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name        = trim($_POST["full_name"] ?? "");
    $email            = trim($_POST["email"] ?? "");
    $phone            = trim($_POST["phone"] ?? "");
    $village_name     = trim($_POST["village_name"] ?? "");
    $password         = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Server-side validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($village_name) || empty($password) || empty($confirm_password)) {
        $error_msg = "All fields are required. Please complete the form.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error_msg = "Please enter a valid 10-digit mobile phone number.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Password and Confirm Password do not match.";
    } else {
        // Check if email already registered
        $check_sql = "SELECT id FROM citizens WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error_msg = "An account with this email address already exists. Please log in.";
            mysqli_stmt_close($check_stmt);
        } else {
            mysqli_stmt_close($check_stmt);

            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new citizen record
            $insert_sql = "INSERT INTO citizens (full_name, email, phone, village_name, password) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sssss", $full_name, $email, $phone, $village_name, $hashed_password);

            if (mysqli_stmt_execute($insert_stmt)) {
                mysqli_stmt_close($insert_stmt);
                $_SESSION['registration_success'] = "Registration successful! You can now log in with your credentials.";
                header("Location: login.php");
                exit();
            } else {
                $error_msg = "Registration failed due to a database error. Please try again.";
                mysqli_stmt_close($insert_stmt);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Registration - VillageConnect</title>
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
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="announcements.php">Announcements</a></li>
                <li><a href="schemes.php">Government Schemes</a></li>
                <li class="nav-right"><a href="login.php">Citizen Login</a></li>
                <li><a href="register.php" class="active">Citizen Register</a></li>
                <li><a href="admin/login.php">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-box">
                <h2 style="color: #1b5e20; text-align: center; margin-bottom: 8px;">Citizen Registration</h2>
                <p style="text-align: center; color: #546e7a; margin-bottom: 25px; font-size: 0.95rem;">
                    Create your account to submit village grievances and access community services.
                </p>

                <!-- Display Error Messages -->
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" onsubmit="return validateRegistrationForm();">
                    <div class="form-group">
                        <label for="full_name">Full Name <span class="req">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                               placeholder="e.g. Ramesh Kumar" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span class="req">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               placeholder="e.g. citizen@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number (10 Digits) <span class="req">*</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                               placeholder="e.g. 9876543210" maxlength="10" required>
                        <span class="form-help">Used for important SMS / verification regarding your complaints.</span>
                    </div>

                    <div class="form-group">
                        <label for="village_name">Village Name <span class="req">*</span></label>
                        <input type="text" id="village_name" name="village_name" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['village_name'] ?? ''); ?>" 
                               placeholder="e.g. Sundarpur" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="req">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Minimum 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password <span class="req">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                               placeholder="Re-enter password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; margin-top: 10px;">
                        Register Account
                    </button>
                </form>

                <div style="text-align: center; margin-top: 20px; font-size: 0.92rem; color: #546e7a;">
                    Already registered? <a href="login.php" style="color: #2e7d32; font-weight: 600;">Login here</a>
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

    <script src="assets/js/script.js"></script>
</body>
</html>


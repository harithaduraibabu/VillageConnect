<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If citizen already logged in, redirect to citizen dashboard
if (isset($_SESSION['citizen_id'])) {
    header("Location: citizen/dashboard.php");
    exit();
}

// Include database connection
require_once "config/db.php";

$error_msg = "";
$success_msg = "";

// Check for session flash messages from registration
if (isset($_SESSION['registration_success'])) {
    $success_msg = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

// Handle login POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error_msg = "Please enter both email and password.";
    } else {
        $sql = "SELECT id, full_name, email, phone, village_name, password FROM citizens WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password using password_verify()
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['citizen_id']      = $row['id'];
                $_SESSION['citizen_name']    = $row['full_name'];
                $_SESSION['citizen_email']   = $row['email'];
                $_SESSION['citizen_village'] = $row['village_name'];

                mysqli_stmt_close($stmt);
                header("Location: citizen/dashboard.php");
                exit();
            } else {
                $error_msg = "Invalid password. Please check your credentials.";
            }
        } else {
            $error_msg = "No account found with this email address.";
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
    <title>Citizen Login - VillageConnect</title>
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
                <li class="nav-right"><a href="login.php" class="active">Citizen Login</a></li>
                <li><a href="register.php">Citizen Register</a></li>
                <li><a href="admin/login.php">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-box">
                <h2 style="color: #1b5e20; text-align: center; margin-bottom: 8px;">Citizen Sign In</h2>
                <p style="text-align: center; color: #546e7a; margin-bottom: 25px; font-size: 0.95rem;">
                    Sign in to track your grievances and participate in village administration.
                </p>

                <!-- Success Message (e.g., after registration) -->
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_msg); ?>
                    </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address <span class="req">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               placeholder="e.g. citizen@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="req">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; margin-top: 10px;">
                        Sign In
                    </button>
                </form>

                <div style="margin-top: 25px; padding-top: 18px; border-top: 1px solid #eceff1; text-align: center; font-size: 0.92rem; color: #546e7a;">
                    <p>Don't have an account? <a href="register.php" style="color: #2e7d32; font-weight: 600;">Register here</a></p>
                    <p style="margin-top: 8px;">Are you a village official? <a href="admin/login.php" style="color: #0288d1; font-weight: 600;">Admin Login</a></p>
                </div>

                <div style="margin-top: 20px; background-color: #f1f8e9; padding: 12px; border-radius: 6px; font-size: 0.85rem; border: 1px dashed #aed581;">
                    <strong>Demo Citizen Credentials:</strong><br>
                    Email: <code>ramesh@example.com</code><br>
                    Password: <code>password123</code>
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


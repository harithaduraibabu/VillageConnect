<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If admin already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
require_once "../config/db.php";

$error_msg = "";

// Handle admin login POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($username) || empty($password)) {
        $error_msg = "Please enter both username/email and password.";
    } else {
        // Query admin by username or email
        $sql = "SELECT id, username, full_name, email, password FROM admins WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($admin = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $admin['password'])) {
                // Set admin session
                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name']     = $admin['full_name'];

                mysqli_stmt_close($stmt);
                header("Location: dashboard.php");
                exit();
            } else {
                $error_msg = "Invalid password. Access denied.";
            }
        } else {
            $error_msg = "No administrator found with these credentials.";
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
    <title>Admin Login - VillageConnect</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Header Section -->
    <header class="top-header" style="background-color: #0d381e;">
        <div class="container header-content">
            <div>
                <a href="../index.php" class="brand-title">
                    VillageConnect
                </a>
                <span class="brand-subtitle">Village Administration Control Center</span>
            </div>
            <div>
                <a href="../index.php" style="color: #c8e6c9; text-decoration: none; font-size: 0.9rem;">&larr; Back to Public Website</a>
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar" style="background-color: #1b5e20;">
        <div class="container">
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../announcements.php">Announcements</a></li>
                <li><a href="../schemes.php">Government Schemes</a></li>
                <li class="nav-right"><a href="../login.php">Citizen Login</a></li>
                <li><a href="login.php" class="active">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-box" style="border-top-color: #0d381e;">
                <div style="text-align: center; margin-bottom: 20px;">
                    
                    <h2 style="color: #1b5e20; margin-top: 5px;">Administrator Sign In</h2>
                    <p style="color: #546e7a; font-size: 0.92rem;">Authorized access for Village Panchayat & Local Authority</p>
                </div>

                <!-- Display Errors -->
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username or Email <span class="req">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               placeholder="e.g. admin or admin@csp.local" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Admin Password <span class="req">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Enter admin password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; margin-top: 10px; background-color: #1b5e20;">
                        Authenticate & Access Dashboard
                    </button>
                </form>

                <!-- Demonstration Account Details for College Viva -->
                <div style="margin-top: 25px; background: #e8f5e9; border: 1px dashed #2e7d32; padding: 15px; border-radius: 6px; font-size: 0.88rem;">
                    <strong style="color: #1b5e20;">College Demonstration Credentials:</strong>
                    <ul style="margin-left: 20px; margin-top: 5px; color: #2e7d32;">
                        <li>Username: <code>admin</code></li>
                        <li>Password: <code>admin123</code></li>
                    </ul>
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


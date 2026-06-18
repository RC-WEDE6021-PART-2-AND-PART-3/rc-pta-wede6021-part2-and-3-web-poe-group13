<?php
/**
 * Admin Login Page - Past Times Clothing Store
 */
require_once '../includes/DBConn.php';

// Redirect if already logged in as admin
if (isAdminLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';
$username = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($conn, $_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Check admin in database
        $sql = "SELECT * FROM tblAdmin WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "Admin not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Past Times</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            
            <h1 class="auth-title">ADMIN LOGIN</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error" style="margin-bottom: 15px; font-size: 12px;"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="auth-input" placeholder="Admin Username" 
                           value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="auth-input" placeholder="Password" required>
                </div>
                
                <button type="submit" class="btn-auth">Login</button>
            </form>
            
            <p class="auth-footer">
                <a href="../index.php">Back to Store</a>
            </p>
        </div>
    </div>
</body>
</html>

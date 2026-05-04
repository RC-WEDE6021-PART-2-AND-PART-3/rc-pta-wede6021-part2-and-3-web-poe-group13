<?php
/**
 * Login Page - Past Times Clothing Store
 */
require_once 'includes/DBConn.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error_message = '';
$username = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Check user in database
        $sql = "SELECT * FROM tblUser WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Check if user is verified
            if ($user['is_verified'] == 0) {
                $error_message = "Your account is pending verification. Please wait for admin approval.";
            } else {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Handle remember me
                    if (isset($_POST['remember_me'])) {
                        setcookie('remember_user', $user['username'], time() + (86400 * 30), "/");
                    }
                    
                    header("Location: index.php");
                    exit();
                } else {
                    $error_message = "Invalid password.";
                }
            }
        } else {
            $error_message = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Past Times</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-icon">
                <i class="fas fa-user"></i>
            </div>
            
            <h1 class="auth-title">LOGIN</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error" style="margin-bottom: 15px; font-size: 12px;"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="auth-input" placeholder="Username" 
                           value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="auth-input" placeholder="Password" required>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" name="remember_me" id="remember_me">
                    <label for="remember_me">Remember me</label>
                </div>
                
                <button type="submit" class="btn-auth">Login</button>
            </form>
            
            <a href="#" class="auth-link">Forgot username/password</a>
            
            <p class="auth-footer">
                Don't have an account? <a href="signup.php">Sign up here</a>
                <p></p>
                <a href="index.php">Back to Homepage</a>
            </p>
        </div>
    </div>
</body>
</html>

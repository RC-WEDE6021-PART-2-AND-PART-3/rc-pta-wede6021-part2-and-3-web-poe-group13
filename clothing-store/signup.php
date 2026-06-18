<?php
/**
 * Sign Up Page - Past Times Clothing Store
 */
require_once 'includes/DBConn.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error_message = '';
$success_message = '';
$username = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($conn, $_POST['username']);
    $email = sanitize($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $check_sql = "SELECT * FROM tblUser WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user (not verified by default)
            $sql = "INSERT INTO tblUser (username, email, password, is_verified) VALUES (?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success_message = "Registration successful! Please wait for admin verification before logging in.";
                $username = $email = '';
            } else {
                $error_message = "Error creating account. Please try again.";
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
    <title>Sign Up - Past Times</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Sign up</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error" style="margin-bottom: 15px; font-size: 12px;"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success" style="margin-bottom: 15px; font-size: 12px;"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="auth-input" placeholder="Username" 
                           value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="auth-input" placeholder="Email" 
                           value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="auth-input" placeholder="Password" required>
                </div>
                
                <div class="auth-input-group">
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="confirm_password" class="auth-input" placeholder="Confirm password" required>
                </div>
                
                <button type="submit" class="btn-auth">Create account</button>
            </form>
            
            <p class="auth-footer">
                already have an account login <a href="login.php">here</a>
            </p>
        </div>
    </div>
</body>
</html>

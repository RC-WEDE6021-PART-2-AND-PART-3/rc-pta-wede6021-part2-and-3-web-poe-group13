<?php
/**
 * Contact Us Page - Past Times Clothing Store
 */
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $message = sanitize($conn, $_POST['message']);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Insert into database
        $sql = "INSERT INTO tblContact (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            $success_message = "Thank you for your message! We will get back to you soon.";
            // Clear form
            $name = $email = $message = '';
        } else {
            $error_message = "Error submitting message. Please try again.";
        }
    }
}
?>

<div class="contact-info">
    <div class="contact-card">
        <div class="contact-icon">
            <i class="fas fa-envelope"></i>
        </div>
        <h4>Email</h4>
        <p>Pasttimes@gmail.com</p>
    </div>
    
    <div class="contact-card">
        <div class="contact-icon">
            <i class="fas fa-phone"></i>
        </div>
        <h4>Phone Number</h4>
        <p>0796814433</p>
    </div>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success" style="max-width: 500px; margin: 0 auto 20px;"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error" style="max-width: 500px; margin: 0 auto 20px;"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="contact-form-container">
    <h2>Contact Us</h2>
    
    <form method="POST" action="">
        <div class="form-group">
            <input type="text" name="name" class="form-input" placeholder="Enter Name" 
                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <input type="email" name="email" class="form-input" placeholder="Enter Email" 
                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <textarea name="message" class="form-textarea" placeholder="Comment" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
        </div>
        
        <button type="submit" class="btn-submit-contact">Submit</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>

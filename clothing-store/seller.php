<?php
/**
 * Seller Page - Past Times Clothing Store
 */
require_once 'includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($conn, $_POST['title']);
    $price = floatval($_POST['price']);
    $description = sanitize($conn, $_POST['description']);
    $category_id = (int)$_POST['category_id'];
    
    // Validate inputs
    if (empty($title) || $price <= 0) {
        $error_message = "Title and valid price are required.";
    } else {
        // Handle image upload
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = 'images/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['image']['name']);
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_url = $upload_path;
                }
            }
        }
        
        // Insert into database
        $sql = "INSERT INTO tblClothes (title, description, price, image_url, category_id, seller_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsis", $title, $description, $price, $image_url, $category_id, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Item submitted successfully!";
            // Update user as seller
            $update_sql = "UPDATE tblUser SET is_seller = 1 WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();
        } else {
            $error_message = "Error submitting item. Please try again.";
        }
    }
}

// Get categories for dropdown
$categories_sql = "SELECT * FROM tblCategory WHERE parent_category IS NULL";
$categories_result = $conn->query($categories_sql);
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<h1 class="page-title" style="color: #00ffff; font-weight: bold;">Seller</h1>

<?php if ($success_message): ?>
    <div class="alert alert-success" style="max-width: 800px; margin: 0 auto 20px;"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error" style="max-width: 800px; margin: 0 auto 20px;"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="seller-container">
    <form method="POST" enctype="multipart/form-data" class="seller-form">
        <div class="seller-inputs">
            <input type="text" name="title" class="seller-input" placeholder="Title" required>
            <input type="number" name="price" class="seller-input" placeholder="Item Price" step="0.01" min="0" required>
            
            <select name="category_id" class="seller-input" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <textarea name="description" class="seller-textarea" placeholder="Comment/Description"></textarea>
        </div>
        
        <div class="seller-images">
            <label for="image-upload" class="camera-icon">
                <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="image-upload" name="image" accept="image/*" style="display: none;">
            
            <div class="image-preview-grid">
                <div class="image-preview-box" id="preview1"></div>
                <div class="image-preview-box" id="preview2"></div>
                <div class="image-preview-box" id="preview3"></div>
                <div class="image-preview-box" id="preview4"></div>
            </div>
            <p class="preview-label">Add item picture</p>
        </div>
        
        <button type="submit" class="btn-seller-submit" style="grid-column: 1 / -1;">Submit</button>
    </form>
</div>

<script>
document.getElementById('image-upload').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview1').style.backgroundImage = 'url(' + e.target.result + ')';
            document.getElementById('preview1').style.backgroundSize = 'cover';
            document.getElementById('preview1').style.backgroundPosition = 'center';
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

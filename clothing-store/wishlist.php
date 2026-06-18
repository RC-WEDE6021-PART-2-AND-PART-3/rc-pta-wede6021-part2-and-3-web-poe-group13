<?php
/**
 * Wishlist Page - Past Times Clothing Store
 */
require_once 'includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle remove from wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $wishlist_id = (int)$_POST['wishlist_id'];
    $delete_sql = "DELETE FROM tblWishlist WHERE wishlist_id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $wishlist_id, $user_id);
    $delete_stmt->execute();
    header("Location: wishlist.php");
    exit();
}

// Handle add to cart from wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $clothes_id = (int)$_POST['clothes_id'];
    
    // Check if item already in cart
    $check_sql = "SELECT * FROM tblCart WHERE user_id = ? AND clothes_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $clothes_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $update_sql = "UPDATE tblCart SET quantity = quantity + 1 WHERE user_id = ? AND clothes_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $user_id, $clothes_id);
        $update_stmt->execute();
    } else {
        $insert_sql = "INSERT INTO tblCart (user_id, clothes_id, quantity) VALUES (?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $clothes_id);
        $insert_stmt->execute();
    }
    $success_message = "Item added to cart!";
}

// Get wishlist items
$sql = "SELECT w.*, cl.title, cl.price, cl.image_url 
        FROM tblWishlist w 
        JOIN tblClothes cl ON w.clothes_id = cl.clothes_id 
        WHERE w.user_id = ?
        ORDER BY w.added_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_items = [];

while ($row = $result->fetch_assoc()) {
    $wishlist_items[] = $row;
}
?>

<h1 class="page-title" style="color: #00ffff; font-weight: bold;">My Wishlist</h1>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<div class="wishlist-container">
    <div class="wishlist-header">
        <span>Product</span>
        <span>Price</span>
        <span></span>
    </div>
    
    <?php if (!empty($wishlist_items)): ?>
        <?php foreach ($wishlist_items as $item): ?>
            <div class="wishlist-item">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                    <button type="submit" name="remove_item" class="wishlist-item-remove">X</button>
                </form>
                
                <div class="wishlist-item-image">
                    <?php if (!empty($item['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php else: ?>
                        <i class="fas fa-tshirt" style="font-size: 40px;"></i>
                    <?php endif; ?>
                </div>
                
                <span class="wishlist-item-name"><?php echo htmlspecialchars($item['title']); ?></span>
                <span class="wishlist-item-price">R<?php echo number_format($item['price'], 2); ?></span>
                <span class="wishlist-item-date">Added: <?php echo date('d F Y', strtotime($item['added_at'])); ?></span>
                
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="clothes_id" value="<?php echo $item['clothes_id']; ?>">
                    <button type="submit" name="add_to_cart" class="btn-add-cart">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; color: white; padding: 40px;">
            <p>Your wishlist is empty</p>
            <a href="index.php" style="color: #00ffff;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

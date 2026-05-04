<?php
/**
 * Men's Section - Past Times Clothing Store
 */
require_once 'includes/header.php';

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (isLoggedIn()) {
        $clothes_id = (int)$_POST['clothes_id'];
        $user_id = $_SESSION['user_id'];
        
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
    } else {
        $error_message = "Please login to add items to cart.";
    }
}

// Handle add to wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_wishlist'])) {
    if (isLoggedIn()) {
        $clothes_id = (int)$_POST['clothes_id'];
        $user_id = $_SESSION['user_id'];
        
        $check_sql = "SELECT * FROM tblWishlist WHERE user_id = ? AND clothes_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $clothes_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            $insert_sql = "INSERT INTO tblWishlist (user_id, clothes_id) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ii", $user_id, $clothes_id);
            $insert_stmt->execute();
            $success_message = "Item added to wishlist!";
        } else {
            $error_message = "Item already in wishlist.";
        }
    } else {
        $error_message = "Please login to add items to wishlist.";
    }
}

// Get men's clothes (category_id = 2)
$sql = "SELECT * FROM tblClothes WHERE category_id = 2";
$result = $conn->query($sql);
$men_items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $men_items[] = $row;
    }
}
?>

<h1 class="page-title" style="color: #00ffff; font-weight: bold;">Men</h1>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="content-box">
    <div class="product-grid" style="grid-template-columns: repeat(4, 1fr);">
        <?php if (!empty($men_items)): ?>
            <?php foreach ($men_items as $item): ?>
                <div class="product-card">
                    <div class="product-image" style="cursor: pointer;" onclick="showItemOptions(<?php echo $item['clothes_id']; ?>)">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                            <i class="fas fa-tshirt" style="font-size: 40px;"></i>
                        <?php endif; ?>
                    </div>
                    <p class="product-price">R<?php echo number_format($item['price'], 2); ?></p>
                    
                    <div id="item-options-<?php echo $item['clothes_id']; ?>" style="display: none; margin-top: 10px;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="clothes_id" value="<?php echo $item['clothes_id']; ?>">
                            <button type="submit" name="add_to_cart" style="background: #00cc00; color: white; border: none; padding: 5px 10px; cursor: pointer; font-size: 10px; margin: 2px;">Add to Cart</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="clothes_id" value="<?php echo $item['clothes_id']; ?>">
                            <button type="submit" name="add_to_wishlist" style="background: #ff6600; color: white; border: none; padding: 5px 10px; cursor: pointer; font-size: 10px; margin: 2px;">Wishlist</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php 
            $default_items = [
                ['name' => 'Blue T-Shirt', 'price' => 170.00],
                ['name' => 'Green T-Shirt', 'price' => 350.00],
                ['name' => 'Watch', 'price' => 749.00],
                ['name' => 'Hat', 'price' => 345.00],
                ['name' => 'Jersey', 'price' => 220.00],
                ['name' => 'White Shirt', 'price' => 371.99],
                ['name' => 'Jeans', 'price' => 550.00],
                ['name' => 'Silver Watch', 'price' => 1950.00],
                ['name' => 'Grey Pants', 'price' => 340.00],
                ['name' => 'Blue Shorts', 'price' => 400.00],
                ['name' => 'Purple Shorts', 'price' => 330.00],
                ['name' => 'Slides', 'price' => 890.00]
            ];
            foreach ($default_items as $item): 
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-tshirt" style="font-size: 40px;"></i>
                    </div>
                    <p class="product-price">R<?php echo number_format($item['price'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function showItemOptions(itemId) {
    var options = document.getElementById('item-options-' + itemId);
    if (options.style.display === 'none') {
        options.style.display = 'block';
    } else {
        options.style.display = 'none';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

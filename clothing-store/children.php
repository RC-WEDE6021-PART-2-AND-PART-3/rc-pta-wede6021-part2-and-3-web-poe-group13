<?php
/**
 * Children's Section - Past Times Clothing Store
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

// Get girls' clothes (category_id = 4)
$girls_sql = "SELECT * FROM tblClothes WHERE category_id = 4";
$girls_result = $conn->query($girls_sql);
$girls_items = [];
if ($girls_result && $girls_result->num_rows > 0) {
    while ($row = $girls_result->fetch_assoc()) {
        $girls_items[] = $row;
    }
}

// Get boys' clothes (category_id = 5)
$boys_sql = "SELECT * FROM tblClothes WHERE category_id = 5";
$boys_result = $conn->query($boys_sql);
$boys_items = [];
if ($boys_result && $boys_result->num_rows > 0) {
    while ($row = $boys_result->fetch_assoc()) {
        $boys_items[] = $row;
    }
}
?>

<h1 class="page-title" style="color: #00ffff; font-weight: bold;">Children</h1>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="content-box">
    <div class="children-container">
        <!-- Girls Section -->
        <div class="children-section girls-section">
            <h3 class="section-title">Girls</h3>
            <div class="children-grid">
                <?php if (!empty($girls_items)): ?>
                    <?php foreach ($girls_items as $item): ?>
                        <div class="product-card">
                            <div class="product-image" style="cursor: pointer;" onclick="showItemOptions(<?php echo $item['clothes_id']; ?>)">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php else: ?>
                                    <i class="fas fa-person-dress" style="font-size: 40px; color: #ff00ff;"></i>
                                <?php endif; ?>
                            </div>
                            <p class="product-price" style="color: #ff0000;">R<?php echo number_format($item['price'], 2); ?></p>
                            
                            <div id="item-options-<?php echo $item['clothes_id']; ?>" style="display: none; margin-top: 10px;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="clothes_id" value="<?php echo $item['clothes_id']; ?>">
                                    <button type="submit" name="add_to_cart" style="background: #00cc00; color: white; border: none; padding: 5px 8px; cursor: pointer; font-size: 9px;">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php 
                    $default_girls = [
                        ['price' => 170.00, 'icon' => 'fa-person-dress'],
                        ['price' => 350.00, 'icon' => 'fa-shoe-prints'],
                        ['price' => 220.00, 'icon' => 'fa-ribbon'],
                        ['price' => 371.99, 'icon' => 'fa-ribbon'],
                        ['price' => 340.00, 'icon' => 'fa-shoe-prints'],
                        ['price' => 400.00, 'icon' => 'fa-bag-shopping']
                    ];
                    foreach ($default_girls as $item): 
                    ?>
                        <div class="product-card">
                            <div class="product-image">
                                <i class="fas <?php echo $item['icon']; ?>" style="font-size: 40px; color: #ff00ff;"></i>
                            </div>
                            <p class="product-price" style="color: #ff0000;">R<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Boys Section -->
        <div class="children-section boys-section">
            <h3 class="section-title">Boys</h3>
            <div class="children-grid">
                <?php if (!empty($boys_items)): ?>
                    <?php foreach ($boys_items as $item): ?>
                        <div class="product-card">
                            <div class="product-image" style="cursor: pointer;" onclick="showItemOptions(<?php echo $item['clothes_id']; ?>)">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php else: ?>
                                    <i class="fas fa-tshirt" style="font-size: 40px; color: #00ff00;"></i>
                                <?php endif; ?>
                            </div>
                            <p class="product-price" style="color: #00ffff;">R<?php echo number_format($item['price'], 2); ?></p>
                            
                            <div id="item-options-<?php echo $item['clothes_id']; ?>" style="display: none; margin-top: 10px;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="clothes_id" value="<?php echo $item['clothes_id']; ?>">
                                    <button type="submit" name="add_to_cart" style="background: #00cc00; color: white; border: none; padding: 5px 8px; cursor: pointer; font-size: 9px;">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php 
                    $default_boys = [
                        ['price' => 749.00, 'icon' => 'fa-tshirt'],
                        ['price' => 345.00, 'icon' => 'fa-tshirt'],
                        ['price' => 550.00, 'icon' => 'fa-shirt'],
                        ['price' => 1950.00, 'icon' => 'fa-tshirt'],
                        ['price' => 330.00, 'icon' => 'fa-hat-cowboy'],
                        ['price' => 890.00, 'icon' => 'fa-tshirt']
                    ];
                    foreach ($default_boys as $item): 
                    ?>
                        <div class="product-card">
                            <div class="product-image">
                                <i class="fas <?php echo $item['icon']; ?>" style="font-size: 40px; color: #00ff00;"></i>
                            </div>
                            <p class="product-price" style="color: #00ffff;">R<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
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

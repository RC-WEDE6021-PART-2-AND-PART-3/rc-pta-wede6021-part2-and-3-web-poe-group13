<?php
/**
 * Cart Page - Past Times Clothing Store
 */
require_once 'includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle remove from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $cart_id = (int)$_POST['cart_id'];
    $delete_sql = "DELETE FROM tblCart WHERE cart_id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $cart_id, $user_id);
    $delete_stmt->execute();
    header("Location: cart.php");
    exit();
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    // Get cart total
    $total_sql = "SELECT SUM(c.quantity * cl.price) as total FROM tblCart c 
                  JOIN tblClothes cl ON c.clothes_id = cl.clothes_id 
                  WHERE c.user_id = ?";
    $total_stmt = $conn->prepare($total_sql);
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    $total_amount = $total_row['total'];
    
    $promo_code = isset($_POST['promo_code']) ? sanitize($conn, $_POST['promo_code']) : '';
    
    // Create order
    $order_sql = "INSERT INTO tblOrder (user_id, total_amount, promo_code) VALUES (?, ?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("ids", $user_id, $total_amount, $promo_code);
    $order_stmt->execute();
    $order_id = $conn->insert_id;
    
    // Move cart items to order items
    $cart_items_sql = "SELECT c.*, cl.price FROM tblCart c 
                       JOIN tblClothes cl ON c.clothes_id = cl.clothes_id 
                       WHERE c.user_id = ?";
    $cart_items_stmt = $conn->prepare($cart_items_sql);
    $cart_items_stmt->bind_param("i", $user_id);
    $cart_items_stmt->execute();
    $cart_items_result = $cart_items_stmt->get_result();
    
    while ($cart_item = $cart_items_result->fetch_assoc()) {
        $insert_item_sql = "INSERT INTO tblOrderItems (order_id, clothes_id, quantity, price) VALUES (?, ?, ?, ?)";
        $insert_item_stmt = $conn->prepare($insert_item_sql);
        $insert_item_stmt->bind_param("iiid", $order_id, $cart_item['clothes_id'], $cart_item['quantity'], $cart_item['price']);
        $insert_item_stmt->execute();
    }
    
    // Clear cart
    $clear_cart_sql = "DELETE FROM tblCart WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_sql);
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();
    
    $success_message = "Order placed successfully! Order ID: " . $order_id;
}

// Get cart items
$sql = "SELECT c.*, cl.title, cl.price, cl.image_url 
        FROM tblCart c 
        JOIN tblClothes cl ON c.clothes_id = cl.clothes_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];
$total_items = 0;
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_items += $row['quantity'];
    $total_price += $row['quantity'] * $row['price'];
}
?>

<h1 class="page-title" style="color: #00ffff; font-weight: bold;">Cart</h1>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<div class="cart-container" style="max-width: 1000px; margin: 0 auto;">
    <!-- Cart Items -->
    <div class="cart-items">
        <div class="cart-header">
            <span>Product</span>
            <span>Price</span>
            <span style="text-align: right;">Items: <?php echo $total_items; ?></span>
        </div>
        
        <?php if (!empty($cart_items)): ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                        <button type="submit" name="remove_item" class="cart-item-remove">X</button>
                    </form>
                    
                    <div class="cart-item-image">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                            <i class="fas fa-tshirt" style="font-size: 30px;"></i>
                        <?php endif; ?>
                    </div>
                    
                    <span class="cart-item-name"><?php echo htmlspecialchars($item['title']); ?></span>
                    <span class="cart-item-price">R<?php echo number_format($item['price'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: white; padding: 40px;">
                <p>Your cart is empty</p>
                <a href="index.php" style="color: #00ffff;">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Order Summary -->
    <div class="order-summary">
        <h3>Order summary</h3>
        
        <div class="summary-row">
            <span>Items: <?php echo $total_items; ?></span>
            <span>R <?php echo number_format($total_price, 2); ?></span>
        </div>
        
        <form method="POST">
            <div class="promo-section">
                <label>Promo Code:</label>
                <input type="text" name="promo_code" class="promo-input" placeholder="Enter your code">
                <button type="button" class="btn-apply">Apply</button>
            </div>
            
            <div class="summary-row" style="border-top: 1px solid white; padding-top: 15px;">
                <span>Total price:</span>
                <span>R <?php echo number_format($total_price, 2); ?></span>
            </div>
            
            <?php if (!empty($cart_items)): ?>
                <button type="submit" name="checkout" class="btn-checkout">Checkout</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

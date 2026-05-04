<?php
/**
 * Homepage - Past Times Clothing Store
 */
require_once 'includes/header.php';

// Get sale items
$sql = "SELECT * FROM tblClothes WHERE is_on_sale = 1 LIMIT 4";
$result = $conn->query($sql);
$sale_items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sale_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Past Times Clothing Store</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- optional stylesheet -->
</head>

<a href="admin/login.php">Admin Login</a>


<body>

    <h1 class="welcome-title">Welcome to Past Times</h1>

    <div class="content-box">
        <h2 class="sale-title">Sale !!</h2>
        
        <div class="product-grid">
            <?php if (!empty($sale_items)): ?>
                <?php foreach ($sale_items as $item): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php else: ?>
                                <i class="fas fa-tshirt" style="font-size: 40px;"></i>
                            <?php endif; ?>
                        </div>
                        <p class="product-price">R<?php echo number_format($item['price'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default sale items if database is empty -->
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-vest" style="font-size: 40px;"></i>
                    </div>
                    <p class="product-price">R150.00</p>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-tshirt" style="font-size: 40px;"></i>
                    </div>
                    <p class="product-price">R55.00</p>
                </div>
                <div class="product-card">
                        <i class="fas fa-baby" style="font-size: 40px;"></i>
                    </div>
                    <p class="product-price">R124.00</p>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-shoe-prints" style="font-size: 40px;"></i>
                    </div>
                    <p class="product-price">R550.00</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
    

    <div class="about-section">
        <h3 class="about-title">Who are we</h3>
        <p class="about-text">
            We specialize in a wide range of affordable second-hand clothing for women, men, and 
            children, as well as shoes, accessories, and linen. What sets us apart from other second-
            hand clothing businesses is our extensive variety of items at unbeatable prices, our quick 
            and convenient process for purchasing your unwanted clothing, and the fact that our 
            stores are filled with new stock daily. This means there's always something fresh and 
            exciting to discover every time you visit.
        </p>
    </div>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>

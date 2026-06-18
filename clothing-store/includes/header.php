<?php
require_once 'DBConn.php';

// Get current page for active nav highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Times - Second Hand Clothing Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php if (isLoggedIn()): ?>
        <?php $user = getLoggedInUser($conn); ?>
        <div class="user-info">
            User <?php echo htmlspecialchars($user['username']); ?> is logged in
        </div>
    <?php endif; ?>
    
    <header>
        <div class="logo">Logo</div>
        
        <nav>
            <?php if ($current_page == 'index'): ?>
                <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
            <?php endif; ?>
            
            <a href="index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">Home</a>
            <a href="women.php" class="<?php echo $current_page == 'women' ? 'active' : ''; ?>">Women</a>
            <a href="men.php" class="<?php echo $current_page == 'men' ? 'active' : ''; ?>">Men</a>
            <a href="children.php" class="<?php echo $current_page == 'children' ? 'active' : ''; ?>">Children</a>
            <a href="wishlist.php" class="<?php echo $current_page == 'wishlist' ? 'active' : ''; ?>">Wishlist</a>
            
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <?php 
                $cart_count = getCartCount($conn);
                if ($cart_count > 0): 
                ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </nav>
    </header>
    
    <main>

<?php 
// 1. Start session and set cache headers immediately
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Prevent browser caching for all pages using this menu
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include('config/constants.php'); 

// 3. Mandatory Session Check
// If u_id is missing, stop everything and send them to login
if(!isset($_SESSION['u_id'])) {
    header("location: login.php");
    exit(); // IMPORTANT: This stops line 56 of myorders.php from running and causing a crash
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCOS</title>
    <link rel="icon" type="image/png" href="images/mcoslogo.png">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="<?php echo SITEURL; ?>" title="Logo">
                    <img src="images/mcoslogo.png" alt="Restaurant Logo" class="img-responsive">
                </a>
            </div>

            <div class="menu text-right">
                <ul>
                    <li><a href="<?php echo SITEURL; ?>">Home</a></li>
                    <li><a href="<?php echo SITEURL; ?>categories.php">Categories</a></li>
                    <li><a href="<?php echo SITEURL; ?>foods.php">Foods</a></li>
                    
                    <?php
                        if(empty($_SESSION["u_id"])) {
                            echo '<li><a href="login.php">Login</a></li>';
                        } else {
                            echo '<li><a href="myorders.php">My Orders</a></li>';
                            echo '<li><a href="profile.php">Profile</a></li>';
                            echo '<li><a href="logout.php">Logout</a></li>';
                        }
                    ?>
                </ul>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>

    <?php 
        $cart_count = 0;
        if(isset($_SESSION['u_id'])) {
            $c_user_id = $_SESSION['u_id'];
            // SQL to count items in cart table
            $sql_cart_count = "SELECT SUM(quantity) as total_items FROM CART WHERE cust_ID = $c_user_id";
            $res_cart_count = mysqli_query($conn, $sql_cart_count);
            if($res_cart_count) {
                $row_cart_count = mysqli_fetch_assoc($res_cart_count);
                $cart_count = $row_cart_count['total_items'] ?? 0;
            }
        }
    ?>

    <?php if($cart_count > 0): ?>
    <a href="cart.php" style="position: fixed; bottom: 20px; right: 20px; background: #2f3542; color: white; padding: 15px 25px; border-radius: 50px; text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 1000; display: flex; align-items: center; gap: 10px; transition: 0.3s;">
        <span style="font-weight: bold;">View My Cart</span>
        <span style="background: #ff4757; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.9rem;"><?php echo $cart_count; ?> Items</span>
    </a>
    <?php endif; ?>

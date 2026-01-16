<?php
// 1. Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include('config/constants.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCOS</title>
    <link rel="icon" type="image/png" href="images/mcoslogo.png">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Centering the content away from screen borders */
        /* General wrapper for all pages */
        .wrapper {
            width: 85%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Specific instructions for the navbar's wrapper */
        .navbar .wrapper {
            display: flex;
            justify-content: space-between;
            /* Pushes Logo left, Menu right */
            align-items: center;
            padding: 15px 0;
            /* Vertical spacing */
        }

        .menu ul {
            list-style: none;
            display: flex;
            gap: 25px;
            padding: 0;
            margin: 0;
        }

        .menu ul li a {
            color: #2f3542;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .menu ul li a:hover {
            color: #ff4757;
        }

        .logout-btn {
            color: #ff4757 !important;
        }
    </style>
</head>

<body>
    <section class="navbar">
        <div class="wrapper">
            <div class="logo">
                <a href="<?php echo SITEURL; ?>" title="Logo">
                    <img src="images/mcoslogo.png" alt="Restaurant Logo" style="width: 100px;">
                </a>
            </div>

            <div class="menu">
                <ul>
                    <li><a href="<?php echo SITEURL; ?>">Home</a></li>
                    <li><a href="<?php echo SITEURL; ?>foods.php">Menu</a></li>
                    <?php
                    if (!isset($_SESSION["u_id"])) {
                        echo '<li><a href="login.php">Login</a></li>';
                    } else {
                        echo '<li><a href="myorders.php">My Orders</a></li>';
                        echo '<li><a href="profile.php">Profile</a></li>';
                        echo '<li><a href="logout.php" class="logout-btn">Logout</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </section>

    <?php
    $cart_count = 0;
    if (isset($_SESSION['u_id']) && !empty($conn)) {
        $c_user_id = $_SESSION['u_id'];

        $sql_cart_count = "SELECT SUM(QUANTITY) AS TOTAL_ITEMS FROM CART WHERE CUST_ID = :user_id";

        $res_cart_count = oci_parse($conn, $sql_cart_count);
        oci_bind_by_name($res_cart_count, ':user_id', $c_user_id);

        if (oci_execute($res_cart_count)) {
            $row_cart_count = oci_fetch_array($res_cart_count, OCI_ASSOC);

            if ($row_cart_count && isset($row_cart_count['TOTAL_ITEMS'])) {
                $cart_count = (int)$row_cart_count['TOTAL_ITEMS'];
            } else {
                $cart_count = 0;
            }
        }
        oci_free_statement($res_cart_count);
    }
    ?>

    <?php if ($cart_count > 0): ?>
        <a href="cart.php" style="position: fixed; bottom: 20px; right: 20px; background: #2f3542; color: white; padding: 15px 25px; border-radius: 50px; text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 1000; display: flex; align-items: center; gap: 10px; transition: 0.3s;">
            <span style="font-weight: bold;">View My Cart</span>
            <span style="background: #ff4757; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.9rem;"><?php echo $cart_count; ?> Items</span>
        </a>
    <?php endif; ?>
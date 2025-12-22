<?php 
ob_start();
include('partials-front/menu.php'); 

if(empty($_SESSION["u_id"])) {
    header('location:login.php');
    exit;
}

$u_id = $_SESSION['u_id'];

// Handle Remove Item
if(isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM CART WHERE cart_ID = $cart_id AND cust_ID = $u_id");
    header('location:cart.php');
}

// Handle Update Quantity
if(isset($_GET['update_qty']) && isset($_GET['id'])) {
    $new_qty = $_GET['update_qty'];
    $cart_id = $_GET['id'];
    if($new_qty > 0) {
        mysqli_query($conn, "UPDATE CART SET quantity = $new_qty WHERE cart_ID = $cart_id AND cust_ID = $u_id");
    } else {
        mysqli_query($conn, "DELETE FROM CART WHERE cart_ID = $cart_id AND cust_ID = $u_id");
    }
    header('location:cart.php');
}

$sql = "SELECT c.*, m.menu_name, m.menu_price, m.menu_pict 
        FROM CART c 
        JOIN MENU m ON c.menu_ID = m.menu_ID 
        WHERE c.cust_ID = $u_id";
$res = mysqli_query($conn, $sql);
$count = mysqli_num_rows($res);
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0; min-height: 70vh;">
    <div class="wrapper" style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
        <h2 class="text-center" style="color: #2f3542; margin-bottom: 30px;">My Shopping Cart</h2>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <?php if($count > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.1); text-align: left;">
                            <th style="padding: 15px;">Food</th>
                            <th style="padding: 15px;">Price</th>
                            <th style="padding: 15px;">Quantity</th>
                            <th style="padding: 15px;">Subtotal</th>
                            <th style="padding: 15px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while($row = mysqli_fetch_assoc($res)): 
                            $sub = $row['menu_price'] * $row['quantity'];
                            $total += $sub;
                        ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 15px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img src="images/food/<?php echo $row['menu_pict']; ?>" style="width: 50px; border-radius: 5px;">
                                        <?php echo $row['menu_name']; ?>
                                    </div>
                                </td>
                                <td style="padding: 15px;">RM <?php echo number_format($row['menu_price'], 2); ?></td>
                                <td style="padding: 15px;">
                                    <a href="cart.php?id=<?php echo $row['cart_ID']; ?>&update_qty=<?php echo $row['quantity']-1; ?>" style="color: white; text-decoration: none;">-</a>
                                    <span style="margin: 0 10px;"><?php echo $row['quantity']; ?></span>
                                    <a href="cart.php?id=<?php echo $row['cart_ID']; ?>&update_qty=<?php echo $row['quantity']+1; ?>" style="color: white; text-decoration: none;">+</a>
                                </td>
                                <td style="padding: 15px;">RM <?php echo number_format($sub, 2); ?></td>
                                <td style="padding: 15px;">
                                    <a href="cart.php?remove=<?php echo $row['cart_ID']; ?>" style="color: #ff4757; text-decoration: none; font-size: 0.8rem;">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div style="margin-top: 30px; text-align: right; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                    <h3 style="margin-bottom: 10px;">Total: RM <?php echo number_format($total, 2); ?></h3>
                    <p style="font-size: 0.8rem; color: #a4b0be; margin-bottom: 20px;">*Excludes RM 2.00 delivery fee</p>
                    <a href="foods.php" class="btn-secondary" style="margin-right: 10px; padding: 10px 20px; border: 1px solid #3498db; color: #3498db; text-decoration: none; border-radius: 5px;">Continue Shopping</a>
                    <a href="payment.php" class="btn-primary" style="background: #2ecc71; color: white; padding: 10px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Proceed to Payment</a>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 50px;">
                    <p style="color: #a4b0be; margin-bottom: 20px;">Your cart is empty.</p>
                    <a href="foods.php" style="color: #3498db; font-weight: bold; text-decoration: none;">Go to Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('partials-front/footer.php'); ?>
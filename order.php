<?php
ob_start();
include('partials-front/menu.php');

if (!isset($_SESSION['u_id'])) {
    header('location:login.php');
    exit;
}

$u_id = $_SESSION['u_id'];

// Check whether food id is set
if (isset($_GET['food_id'])) {
    $food_id = $_GET['food_id'];

    $sql = "SELECT * FROM MENU WHERE menu_ID = :food_id";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":food_id", $food_id);
    oci_execute($stid);

    $row = oci_fetch_assoc($stid);

    if ($row) {
        $title = $row['MENU_NAME'];
        $price = $row['MENU_PRICE'];
        $details = $row['MENU_DETAILS'];
        $image_name = $row['MENU_PICT'];
    } else {
        header('location:' . SITEURL);
        exit;
    }
} else {
    header('location:' . SITEURL);
    exit;
}

// Fetch current user details
$cust_name = $cust_contact = $cust_dorm = "";
$sql_cust = "SELECT * FROM CUSTOMER WHERE cust_ID = :u_id";
$stid_cust = oci_parse($conn, $sql_cust);
oci_bind_by_name($stid_cust, ":u_id", $u_id);
oci_execute($stid_cust);
$row_cust = oci_fetch_assoc($stid_cust);

if ($row_cust) {
    $cust_name = $row_cust['CUST_FIRST_NAME'] . " " . $row_cust['CUST_LAST_NAME'];
    $cust_contact = $row_cust['CUST_CONTACT_NO'];
    $cust_dorm = $row_cust['CUST_DORM'];
}
?>

<section class="food-order" style="background-color: #f1f2f6; padding: 4% 0;">
    <div class="container">
        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
            <h2 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Checkout</h2>

            <form action="" method="POST" class="order">
                <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <div style="flex: 1; min-width: 300px; border-right: 1px solid #eee; padding-right: 20px;">
                        <h3 style="border-bottom: 2px solid #ff4757; display: inline-block;">Selected Food</h3>
                        <div style="margin-top: 20px; display: flex; gap: 15px;">
                            <div class="food-menu-img">
                                <?php if ($image_name == "") echo "<div class='error'>No Image</div>";
                                else { ?>
                                    <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" style="width: 100px; border-radius: 10px;">
                                <?php } ?>
                            </div>
                            <div class="food-menu-desc">
                                <h4 style="margin:0;"><?php echo $title; ?></h4>
                                <input type="hidden" name="price" id="unit_price" value="<?php echo $price; ?>">
                                <p style="font-size: 0.9rem; color: #747d8c; margin: 5px 0;"><?php echo $details; ?></p>
                                <p style="font-weight: bold; color: #ff4757;">RM <?php echo $price; ?></p>
                                <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                                    <button type="button" onclick="changeQty(-1)" style="padding: 5px 10px; cursor:pointer;">-</button>
                                    <input type="number" name="qty" id="qty" value="1" readonly style="width: 50px; text-align: center; border: 1px solid #ccc;">
                                    <button type="button" onclick="changeQty(1)" style="padding: 5px 10px; cursor:pointer;">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 300px;">
                        <h3 style="border-bottom: 2px solid #ff4757; display: inline-block;">Confirm Detail</h3>
                        <div style="margin: 20px 0; background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 0.9rem;">
                            <p><strong>Name:</strong> <?php echo $cust_name; ?></p>
                            <p><strong>Contact:</strong> <?php echo $cust_contact; ?></p>
                            <p><strong>Dorm:</strong> <?php echo $cust_dorm; ?></p>
                        </div>
                        <div style="border-top: 1px solid #eee; padding-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;"><span>Subtotal</span><span>RM <span id="display_subtotal"><?php echo $price; ?></span></span></div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;"><span>Delivery Charge</span><span>RM <span id="display_delivery">2.00</span></span></div>
                            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; color: #ff4757; margin-top: 10px;"><span>Grand Total</span><span>RM <span id="display_grand"><?php echo $price + 2; ?></span></span></div>
                        </div>
                        <input type="submit" name="submit" value="Confirm & Make Payment" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 12px; font-size: 1.1rem;">
                    </div>
                </div>
            </form>

            <?php
            if (isset($_POST['submit'])) {
                if (empty($_SESSION["u_id"])) {
                    header('location:login.php');
                    exit;
                } else {
                    // Store choices in Session instead of Database
                    $_SESSION['temp_order'] = [
                        'food_id' => $food_id,
                        'food_name' => $title,
                        'price' => $price,
                        'qty' => $_POST['qty'],
                        'delivery_charge' => 2.00,
                        'grand_total' => ($price * $_POST['qty']) + 2.00
                    ];
                    header('location:payment.php');
                    exit;
                }
            }
            ?>
        </div>
    </div>
</section>

<script>
    function changeQty(val) {
        let qtyInput = document.getElementById('qty');
        let currentQty = parseInt(qtyInput.value);
        let newQty = currentQty + val;
        if (newQty >= 1) {
            qtyInput.value = newQty;
            updateTotals(newQty);
        }
    }

    function updateTotals(qty) {
        let price = parseFloat(document.getElementById('unit_price').value);
        let delivery = 2.00;
        let subtotal = price * qty;
        let grand = subtotal + delivery;
        document.getElementById('display_subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('display_grand').innerText = grand.toFixed(2);
    }
</script>

<?php include('partials-front/footer.php'); ?>
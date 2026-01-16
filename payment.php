<?php
ob_start();
include('partials-front/menu.php');

// 1. Check Login
if (empty($_SESSION["u_id"])) {
    header('location:login.php');
    exit;
}

$u_id = $_SESSION['u_id'];

// 2. Fetch Customer Details
$sql_cust = "SELECT * FROM CUSTOMER WHERE CUST_ID = :cust_id";
$stid_cust = oci_parse($conn, $sql_cust);
oci_bind_by_name($stid_cust, ":cust_id", $u_id);
oci_execute($stid_cust);
$cust = oci_fetch_assoc($stid_cust); // Default keys are UPPERCASE

// 3. Calculate Totals
$sql_cart_total = "SELECT SUM(M.MENU_PRICE * C.QUANTITY) AS SUBTOTAL 
                   FROM CART C 
                   JOIN MENU M ON C.MENU_ID = M.MENU_ID 
                   WHERE C.CUST_ID = :cust_id";

$stid_total = oci_parse($conn, $sql_cart_total);
oci_bind_by_name($stid_total, ":cust_id", $u_id);
oci_execute($stid_total);
$row_total = oci_fetch_assoc($stid_total);

// Handle Null Subtotal (Empty Cart)
$subtotal = isset($row_total['SUBTOTAL']) ? $row_total['SUBTOTAL'] : 0;
$delivery = 2.00;
$grand_total = $subtotal + $delivery;

if ($subtotal == 0) {
    header('location:foods.php');
    exit;
}

// 4. Handle Payment Submission
if (isset($_POST['confirm_payment'])) {

    // Validate Inputs
    $payment_method = $_POST['bank_name'];
    $valid_upload = false;
    $receipt_name = "";

    // A. Handle File Upload 
    if (isset($_FILES['receipt']['name']) && $_FILES['receipt']['name'] != "") {
        $ext = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
        $receipt_name = "Receipt-" . time() . "-" . rand(000, 999) . "." . $ext;
        $tmp_path = $_FILES['receipt']['tmp_name'];
        $dest_path = "images/receipts/" . $receipt_name;

        // Create folder if not exists
        if (!is_dir('images/receipts/')) mkdir('images/receipts/', 0777, true);

        if (move_uploaded_file($tmp_path, $dest_path)) {
            $valid_upload = true;
        }
    }

    if ($valid_upload) {
        try {
            $sql1 = "INSERT INTO ORDERS (ORDER_ID, GRAND_TOTAL, DELIVERY_CHARGE, CUST_ID, STAFF_ID)
                     VALUES (ORDER_SEQ.NEXTVAL, :grand_total, :delivery, :cust_id, 2)
                     RETURNING ORDER_ID INTO :order_id";

            $stid1 = oci_parse($conn, $sql1);
            oci_bind_by_name($stid1, ":grand_total", $grand_total);
            oci_bind_by_name($stid1, ":delivery", $delivery);
            oci_bind_by_name($stid1, ":cust_id", $u_id);
            oci_bind_by_name($stid1, ":order_id", $new_order_id, 32, SQLT_INT);

            if (!oci_execute($stid1, OCI_NO_AUTO_COMMIT)) throw new Exception("Failed to create Order");

            // STEP 2: Get Items from Cart
            $sql_get_cart = "SELECT C.MENU_ID, C.QUANTITY, M.MENU_PRICE 
                             FROM CART C 
                             JOIN MENU M ON C.MENU_ID = M.MENU_ID 
                             WHERE C.CUST_ID = :cust_id";
            $stid_cart = oci_parse($conn, $sql_get_cart);
            oci_bind_by_name($stid_cart, ":cust_id", $u_id);
            oci_execute($stid_cart); // Select tak perlu commit

            // STEP 3: Insert into ORDER_MENU
            $sql2 = "INSERT INTO ORDER_MENU (ORDER_ID, MENU_ID, ORDER_QUANTITY, SUB_TOTAL)
                     VALUES (:order_id, :menu_id, :qty, :sub_total)";
            $stid2 = oci_parse($conn, $sql2);

            while ($item = oci_fetch_assoc($stid_cart)) {
                $m_id = $item['MENU_ID'];
                $qty  = $item['QUANTITY'];
                $st   = $item['MENU_PRICE'] * $qty;

                oci_bind_by_name($stid2, ":order_id", $new_order_id);
                oci_bind_by_name($stid2, ":menu_id", $m_id);
                oci_bind_by_name($stid2, ":qty", $qty);
                oci_bind_by_name($stid2, ":sub_total", $st);

                if (!oci_execute($stid2, OCI_NO_AUTO_COMMIT)) throw new Exception("Failed to add items");
            }

            // STEP 4: Insert Payment
            $sql3 = "INSERT INTO PAYMENT (PAYMENT_ID, AMOUNT_PAID, PAYMENT_METHOD, PAYMENT_STATUS, RECEIPT_FILE, ORDER_ID)
                     VALUES (PAYMENT_SEQ.NEXTVAL, :grand_total, :payment_method, 'Pending Verification', :receipt_file, :order_id)";

            $stid3 = oci_parse($conn, $sql3);
            oci_bind_by_name($stid3, ":grand_total", $grand_total);
            oci_bind_by_name($stid3, ":payment_method", $payment_method);
            oci_bind_by_name($stid3, ":receipt_file", $receipt_name);
            oci_bind_by_name($stid3, ":order_id", $new_order_id);

            if (!oci_execute($stid3, OCI_NO_AUTO_COMMIT)) throw new Exception("Failed to save payment");

            // STEP 5: Clear Cart
            $sql_clear = "DELETE FROM CART WHERE CUST_ID = :cust_id";
            $stid_clear = oci_parse($conn, $sql_clear);
            oci_bind_by_name($stid_clear, ":cust_id", $u_id);

            if (!oci_execute($stid_clear, OCI_NO_AUTO_COMMIT)) throw new Exception("Failed to clear cart");

            // FINAL STEP
            oci_commit($conn);

            $_SESSION['pay_notice'] = "<div class='success text-center'>Order Placed Successfully!</div>";
            header('location:myorders.php'); // Pastikan page ni wujud atau tukar ke index.php
            exit;
        } catch (Exception $e) {
            oci_rollback($conn);
            $error_msg = $e->getMessage();
            $_SESSION['pay_notice'] = "<div class='error text-center'>Transaction Failed: $error_msg</div>";
        }
    } else {
        $_SESSION['pay_notice'] = "<div class='error text-center'>Failed to upload receipt.</div>";
    }
}
?>

<section class="payment-section" style="background-color: #f1f2f6; padding: 4% 0;">
    <div class="container">

        <?php
        if (isset($_SESSION['pay_notice'])) {
            echo $_SESSION['pay_notice'];
            unset($_SESSION['pay_notice']);
        }
        ?>

        <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">

            <h2 class="text-center" style="color: #2f3542; margin-bottom: 20px;">Confirm & Pay</h2>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; border-left: 5px solid #2ecc71;">
                <p><strong>Deliver to:</strong> <?php echo isset($cust['CUST_DORM']) ? $cust['CUST_DORM'] : 'Address not set'; ?></p>
                <p><strong>Contact:</strong> <?php echo isset($cust['CUST_CONTACT_NO']) ? $cust['CUST_CONTACT_NO'] : 'No Contact'; ?></p>
                <hr>
                <p>Subtotal: RM <?php echo number_format($subtotal, 2); ?></p>
                <p>Delivery: RM <?php echo number_format($delivery, 2); ?></p>
                <p style="font-size: 1.2rem; color: #ff4757;"><strong>Total to Pay: RM <?php echo number_format($grand_total, 2); ?></strong></p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <!--Scan QR to pay -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold; display: block; margin-bottom: 10px;">Scan QR to Pay</label>
                    <img src="images/mcosqr.jpeg" alt="QR" class="img-responsive" style=" width: 250px; height: auto; border-radius: 8px; border: 1px solid #ccc; display: inline-block;">
                </div>
                <!--End of QR-->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold;">Select Bank</label>
                    <select name="bank_name" class="form-control" style="width:100%; padding:10px;" required>
                        <option value="Maybank">Maybank</option>
                        <option value="CIMB Bank">CIMB Bank</option>
                        <option value="Bank Islam">Bank Islam</option>
                        <option value="TNG eWallet">TNG eWallet</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight:bold;">Upload Receipt</label>
                    <input type="file" name="receipt" required class="form-control" accept="image/*,.pdf">
                </div>

                <button type="submit" name="confirm_payment" class="btn-primary" style="width:100%; padding:12px; background:#2ecc71; border:none; color:white; border-radius:5px; cursor:pointer; font-weight:bold;">Confirm Payment</button>

                <div class="text-center" style="margin-top:15px;">
                    <a href="cart.php" style="color:#747d8c; text-decoration:none;">Cancel & Back to Cart</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>
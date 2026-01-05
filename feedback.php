<?php
include('partials-front/menu.php');

if (!isset($_SESSION['u_id'])) {
    header('location:login.php');
    exit;
}
if (!isset($_GET['id'])) {
    header('location:myorders.php');
    exit;
}

$order_id = $_GET['id'];
$cust_id = $_SESSION['u_id'];

$sql_check = "SELECT * FROM FEEDBACK WHERE ORDER_ID = :order_id";
$stid_check = oci_parse($conn, $sql_check);
oci_bind_by_name($stid_check, ":order_id", $order_id);
oci_execute($stid_check);

$already_submitted = oci_fetch_assoc($stid_check) ? true : false;
oci_free_statement($stid_check);

// --- HANDLE SUBMIT ---
if (isset($_POST['submit_feedback']) && !$already_submitted) {
    $feedback_text = $_POST['feedback_text'];
    $feedback_cat = $_POST['feedback_cat'];

    $sql_fb = "INSERT INTO FEEDBACK (FEEDBACK, CUST_ID, ORDER_ID, FEEDBACK_CAT_ID) 
               VALUES (:feedback, :cust_id, :order_id, :feedback_cat)";

    $stid_fb = oci_parse($conn, $sql_fb);
    oci_bind_by_name($stid_fb, ":feedback", $feedback_text);
    oci_bind_by_name($stid_fb, ":cust_id", $cust_id);
    oci_bind_by_name($stid_fb, ":order_id", $order_id);
    oci_bind_by_name($stid_fb, ":feedback_cat", $feedback_cat);

    if (oci_execute($stid_fb)) {
        header("Refresh:0");
    } else {
        $e = oci_error($stid_fb);
        echo "<script>alert('Error: " . $e['message'] . "');</script>";
    }
    oci_free_statement($stid_fb);
}

$sql_items = "SELECT m.MENU_NAME 
              FROM ORDER_MENU om 
              JOIN MENU m ON om.MENU_ID = m.MENU_ID 
              WHERE om.ORDER_ID = :order_id";
$stid_items = oci_parse($conn, $sql_items);
oci_bind_by_name($stid_items, ":order_id", $order_id);
oci_execute($stid_items);

// --- FETCH CATEGORIES ---
$sql_cat = "SELECT * FROM FEEDBACK_CATEGORY";
$stid_cat = oci_parse($conn, $sql_cat);
oci_execute($stid_cat);
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 50px 0;">
    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 20px;">

        <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h2 class="text-center" style="margin-bottom: 20px; color: #2f3542;">Order Feedback</h2>
            <p class="text-center" style="color: #747d8c; margin-bottom: 30px;">Order ID: <strong>#<?php echo $order_id; ?></strong></p>

            <?php if ($already_submitted): ?>

                <div style="text-align: center; padding: 30px; background: #e3fff3; border-radius: 10px;">
                    <h1 style="font-size: 3rem; margin-bottom: 10px;">✅</h1>
                    <h3 style="color: #27ae60;">Thank You!</h3>
                    <p style="color: #57606f;">We have received your feedback for this order.</p>
                    <br>
                    <a href="myorders.php" class="btn-primary" style="background: #2f3542; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Back to Orders</a>
                </div>

            <?php else: ?>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                    <strong style="display:block; margin-bottom: 10px;">Items in this order:</strong>
                    <ul style="padding-left: 20px; color: #57606f;">
                        <?php while ($row = oci_fetch_assoc($stid_items)): ?>
                            <li><?php echo $row['MENU_NAME']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <form action="" method="POST">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: bold;">Category</label>
                        <select name="feedback_cat" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <?php while ($cat = oci_fetch_assoc($stid_cat)): ?>
                                <option value="<?php echo $cat['FEEDBACK_CAT_ID']; ?>"><?php echo $cat['FEEDBACK_CAT_NAME']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: bold;">Comments</label>
                        <textarea name="feedback_text" rows="5" required placeholder="Describe your experience..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                    </div>

                    <div style="text-align: center;">
                        <a href="order-details.php?id=<?php echo $order_id; ?>" style="color: #747d8c; text-decoration: none; margin-right: 20px;">Cancel</a>
                        <button type="submit" name="submit_feedback" class="btn-primary" style="background: #ff4757; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Submit Feedback</button>
                    </div>

                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('partials-front/footer.php'); ?>
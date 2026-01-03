<?php
include('partials-front/menu.php');

// 1. Security Check - User mesti login
if (!isset($_SESSION['u_id'])) {
    header('location:login.php');
    exit;
}

$cust_id = $_SESSION['u_id'];

// 2. Check ada tak ID di pass dari URL (match dengan link di myorders.php)
if (isset($_GET['id'])) {

    $order_id = $_GET['id']; // Kita guna variable 'id' sebab link guna ?id=

    // 3. SQL Query (Updated Table Name: ORDERS)
    // Left Join Delivery & Staff supaya tak error kalau belum assign
    $sql = "SELECT o.*, d.delivery_status, d.delivery_time, d.delivery_date,
                   s.staff_first_name, s.staff_last_name
            FROM ORDERS o
            LEFT JOIN DELIVERY d ON o.order_ID = d.order_ID
            LEFT JOIN STAFF s ON d.staff_ID = s.staff_ID
            WHERE o.order_ID = :order_id AND o.cust_ID = :cust_id";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":order_id", $order_id);
    oci_bind_by_name($stid, ":cust_id", $cust_id);
    oci_execute($stid);

    $row = oci_fetch_assoc($stid);


    // Kalau order tak jumpa (user cuba main tembak ID orang lain)
    if (!$row) {
        header('location:' . SITEURL . 'myorders.php');
        exit();
    }

    // Prepare Data untuk Display
    $order_date = date('d M Y, H:i', strtotime($row['ORDER_DATE']));
    $grand_total = $row['GRAND_TOTAL'];
    $delivery_charge = $row['DELIVERY_CHARGE'];

    // Status Logic (Default Processing kalau NULL)
    $status = isset($row['DELIVERY_STATUS']) ? $row['DELIVERY_STATUS'] : "Processing";

    // Staff Logic
    if (isset($row['STAFF_FIRST_NAME'])) {
        $staff_name = $row['STAFF_FIRST_NAME'] . " " . $row['STAFF_LAST_NAME'];
    } else {
        $staff_name = "Finding Driver...";
    }

    // 4. Check Feedback (Untuk button review nanti)
    $sql_fb = "SELECT COUNT(*) AS CNT FROM FEEDBACK WHERE order_ID = :order_id";
    $stid_fb = oci_parse($conn, $sql_fb);
    oci_bind_by_name($stid_fb, ":order_id", $order_id);
    oci_execute($stid_fb);

    $fb_row = oci_fetch_assoc($stid_fb);
    $feedback_exists = $fb_row['CNT'] > 0;
} else {
    // Kalau takde ID, tendang balik
    header('location:' . SITEURL . 'myorders.php');
    exit();
}

// 5. Fetch Items dalam Order tu
$sql_items = "SELECT om.*, m.menu_name
              FROM ORDER_MENU om
              JOIN MENU m ON om.menu_ID = m.menu_ID
              WHERE om.order_ID = :order_id";
$stid_items = oci_parse($conn, $sql_items);
oci_bind_by_name($stid_items, ":order_id", $order_id);
oci_execute($stid_items);

$order_items = [];
while (($item = oci_fetch_assoc($stid_items)) != false) {
    $order_items[] = $item;
}
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0; min-height: 80vh;">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 20px;">

        <div class="order-details-box" style="background-color: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

            <h2 class="text-center" style="margin-bottom: 30px; color: #2f3542; font-weight: bold;">Order Invoice</h2>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f2f6; padding-bottom: 25px; margin-bottom: 30px;">
                <div style="line-height: 1.8;">
                    <p style="margin:0;"><strong style="color: #747d8c;">Order ID:</strong> <span style="color: #ff4757; font-weight: bold;">#<?php echo $order_id; ?></span></p>
                    <p style="margin:0;"><strong style="color: #747d8c;">Date:</strong> <?php echo $order_date; ?></p>
                    <p style="margin:0;"><strong style="color: #747d8c;">Delivery Staff:</strong> <?php echo $staff_name; ?></p>
                </div>
                <div style="text-align: right;">
                    <p style="margin-bottom: 8px; font-weight: bold; color: #747d8c;">Status</p>
                    <?php
                    // Warna Warni Status
                    $status_bg = '#f1c40f'; // Default Kuning
                    if ($status == 'Delivered') $status_bg = '#2ecc71'; // Hijau
                    else if ($status == 'Cancelled') $status_bg = '#ff4757'; // Merah
                    else if ($status == 'On The Way') $status_bg = '#3498db'; // Biru
                    ?>
                    <span style="padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: bold; background-color: <?php echo $status_bg; ?>; color: white; display: inline-block;">
                        <?php echo $status; ?>
                    </span>
                </div>
            </div>

            <h3 style="color: #2f3542; margin-bottom: 20px; font-size: 1.1rem;">Items Ordered</h3>

            <div style="border-radius: 10px; overflow: hidden; border: 1px solid #f1f2f6; margin-bottom: 30px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #2f3542; color: white; text-align: left;">
                            <th style="padding: 15px;">No</th>
                            <th style="padding: 15px;">Item Name</th>
                            <th style="padding: 15px;">Price</th>
                            <th style="padding: 15px; text-align: center;">Qty</th>
                            <th style="padding: 15px; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 1;
                        if (count($order_items) > 0):
                            foreach ($order_items as $item):
                        ?>
                                <tr style="border-bottom: 1px solid #f1f2f6;">
                                    <td style="padding: 15px; color: #747d8c;"><?php echo $sn++; ?></td>
                                    <td style="padding: 15px; font-weight: 600; color: #2f3542;"><?php echo $item['MENU_NAME']; ?></td>
                                    <td style="padding: 15px;">RM <?php echo number_format($item['SUB_TOTAL'] / $item['ORDER_QUANTITY'], 2); ?></td>
                                    <td style="padding: 15px; text-align: center;"><?php echo $item['ORDER_QUANTITY']; ?></td>
                                    <td style="padding: 15px; text-align: right; font-weight: bold; color: #2f3542;">RM <?php echo number_format($item['SUB_TOTAL'], 2); ?></td>
                                </tr>
                            <?php
                            endforeach;
                        else:
                            ?>
                            <tr>
                                <td colspan="5" style="padding:15px; text-align:center;">No items found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-left: auto; max-width: 350px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #747d8c;">
                    <span>Delivery Charge:</span>
                    <span>RM <?php echo number_format($delivery_charge, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 800; color: #2f3542; border-top: 1px solid #ddd; padding-top: 10px;">
                    <span>Grand Total:</span>
                    <span style="color: #ff4757;">RM <?php echo number_format($grand_total, 2); ?></span>
                </div>
            </div>

            <div class="text-center" style="margin-top: 40px; display: flex; justify-content: center; gap: 15px;">
                <a href="<?php echo SITEURL; ?>myorders.php" style="padding: 12px 30px; text-decoration: none; border-radius: 8px; background-color: #2f3542; color: white; font-weight: bold; transition: 0.3s;">
                    Back to My Orders
                </a>

                <?php if ($status == 'Delivered'): ?>
                    <?php if (!$feedback_exists): ?>
                        <a href="<?php echo SITEURL; ?>feedback.php?id=<?php echo $order_id; ?>" style="padding: 12px 30px; text-decoration: none; border-radius: 8px; background-color: #ff4757; color: white; font-weight: bold; transition: 0.3s;">
                            Give Feedback
                        </a>
                    <?php else: ?>
                        <button disabled style="padding: 12px 30px; border-radius: 8px; background-color: #2ecc71; color: white; font-weight: bold; border: none; cursor: not-allowed; opacity: 0.8;">
                            Feedback Submitted ✅
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include('partials-front/footer.php'); ?>
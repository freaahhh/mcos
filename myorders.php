<?php
include('partials-front/menu.php');

if (!isset($_SESSION['u_id'])) {
    header('location: login.php');
    exit();
}

$customer_id = $_SESSION['u_id'];
$orders = []; // Initialize as empty array
$sn = 1;      // Initialize serial number

// Fetch Orders + Delivery Status (Use LEFT JOIN)
$sql = "SELECT o.ORDER_ID, o.ORDER_DATE, o.DELIVERY_CHARGE, o.GRAND_TOTAL, d.DELIVERY_STATUS
        FROM ORDERS o
        LEFT JOIN DELIVERY d ON o.ORDER_ID = d.ORDER_ID
        WHERE o.CUST_ID = :customer_id 
        ORDER BY o.ORDER_ID DESC";

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':customer_id', $customer_id);

if (oci_execute($stid)) {
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        $orders[] = $row;
    }
} else {
    $e = oci_error($stid);
    echo "<div class='error text-center'>Query Error: " . $e['message'] . "</div>";
}
oci_free_statement($stid);
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">

        <h2 class="text-center" style="margin-bottom: 30px; color: #2f3542;">My Order History</h2>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">

            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 15px;">No.</th>
                        <th style="padding: 15px;">Order ID</th>
                        <th style="padding: 15px;">Date</th>
                        <th style="padding: 15px;">Total (RM)</th>
                        <th style="padding: 15px;">Status</th>
                        <th style="padding: 15px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($orders) > 0) {
                        foreach ($orders as $row) {
                            $order_id = $row['ORDER_ID'];
                            $order_date = $row['ORDER_DATE'];
                            $total = $row['GRAND_TOTAL'];
                            $db_status = isset($row['DELIVERY_STATUS']) ? trim($row['DELIVERY_STATUS']) : null;

                            if (empty($db_status)) {
                                $display_status = "Processing";
                            } else {
                                $display_status = $db_status;
                            }
                    ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 15px;"><?php echo $sn++; ?>.</td>
                                <td style="padding: 15px; font-weight: bold; color: #ff4757;">#<?php echo $order_id; ?></td>
                                <td style="padding: 15px;">
                                    <?php echo date('d M Y', strtotime($order_date)); ?>
                                </td>
                                <td style="padding: 15px;">
                                    <?php echo number_format($total, 2); ?>
                                </td>
                                <td style="padding: 15px;">
                                    <?php
                                    $check_status = strtoupper($display_status);

                                    if ($check_status == "DELIVERED") {
                                        echo "<span style='background: #2ecc71; padding: 5px 12px; border-radius: 5px; font-size: 0.85rem; font-weight:bold;'>Delivered</span>";
                                    } elseif ($check_status == "ON DELIVERY" || $check_status == "OUT FOR DELIVERY") {
                                        echo "<span style='background: #e67e22; padding: 5px 12px; border-radius: 5px; font-size: 0.85rem; font-weight:bold;'>On Delivery</span>";
                                    } elseif ($check_status == "CANCELLED") {
                                        echo "<span style='background: #ff4757; padding: 5px 12px; border-radius: 5px; font-size: 0.85rem; font-weight:bold;'>Cancelled</span>";
                                    } else {
                                        echo "<span style='background: #57606f; padding: 5px 12px; border-radius: 5px; font-size: 0.85rem; font-weight:bold;'>Processing</span>";
                                    }
                                    ?>
                                </td>
                                <td style="padding: 15px;">
                                    <a href="<?php echo SITEURL; ?>order-details.php?id=<?php echo $order_id; ?>"
                                        style="color: #3498db; text-decoration: none; font-weight: bold; border: 1px solid #3498db; padding: 5px 12px; border-radius: 5px; transition: 0.3s;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' style='padding: 30px; text-align: center; color: #a4b0be;'>You haven't placed any orders yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="clearfix"></div>
    </div>
</div>

<?php include('partials-front/footer.php'); ?>
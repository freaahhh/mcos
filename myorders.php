<?php
include('partials-front/menu.php');

if (!isset($_SESSION['u_id'])) {
    header('location: login.php');
    exit();
}

$customer_id = $_SESSION['u_id'];
$orders = [];
$sn = 1;

// --- UPDATED SQL QUERY ---
$sql = "SELECT o.ORDER_ID, 
               c.CUST_USERNAME, 
               o.GRAND_TOTAL, 
               o.DELIVERY_CHARGE,
               TO_CHAR(o.ORDER_DATE, 'YYYY-MM-DD HH24:MI:SS') AS ORDER_DATE_FORMATTED, -- Needed for display
               
               CASE 
                   WHEN p.PAYMENT_STATUS = 'Verified' AND d.DELIVERY_STATUS = 'Delivered' THEN 'COMPLETED' 
                   WHEN p.PAYMENT_STATUS = 'Verified' AND d.DELIVERY_STATUS = 'Ordered' THEN 'AWAITING DELIVERY' 
                   WHEN p.PAYMENT_STATUS = 'Verified' AND d.DELIVERY_STATUS = 'On Delivery' THEN 'IN TRANSIT' 
                   WHEN p.PAYMENT_STATUS = 'Failed' THEN 'CANCELLED' 
                   ELSE 'PROCESSING' 
               END AS CURRENT_ORDER_STATUS

        FROM ORDERS o
        JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID
        LEFT JOIN PAYMENT p ON o.ORDER_ID = p.ORDER_ID 
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
                        <th style="padding: 15px;">Live Status</th>
                        <th style="padding: 15px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($orders) > 0) {
                        foreach ($orders as $row) {
                            $order_id = $row['ORDER_ID'];
                            $order_date = date('d M Y, H:i', strtotime($row['ORDER_DATE_FORMATTED']));
                            $total = $row['GRAND_TOTAL'];

                            // Get the status from your Query Logic
                            $status = $row['CURRENT_ORDER_STATUS'];

                            // --- COLOR LOGIC (Updated to match your terms) ---
                            $badge_bg = '#57606f'; // Default Grey (Processing)

                            if ($status == 'COMPLETED') {
                                $badge_bg = '#2ecc71'; // Green
                            } elseif ($status == 'IN TRANSIT') {
                                $badge_bg = '#3498db'; // Blue
                            } elseif ($status == 'AWAITING DELIVERY') {
                                $badge_bg = '#f39c12'; // Orange (Was 'Preparing')
                            } elseif ($status == 'CANCELLED') {
                                $badge_bg = '#e74c3c'; // Red
                            }
                            /*
                            } elseif ($status == 'PENDING PAYMENT') {
                                $badge_bg = '#e74c3c'; // Red
                            }
                            */
                    ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 15px;"><?php echo $sn++; ?>.</td>
                                <td style="padding: 15px; font-weight: bold; color: #ff4757;">#<?php echo $order_id; ?></td>
                                <td style="padding: 15px;">
                                    <?php echo $order_date; ?>
                                </td>
                                <td style="padding: 15px;">
                                    <?php echo number_format($total, 2); ?>
                                </td>

                                <td style="padding: 15px;">
                                    <span style="background: <?php echo $badge_bg; ?>; padding: 5px 12px; border-radius: 5px; font-size: 0.85rem; font-weight:bold; color: white; display: inline-block;">
                                        <?php echo $status; ?>
                                    </span>

                                    <!-- Remove Pending Payment because once the customer submit payment, they are not allowed to edit it. They must make a new order.
                                    <?php if ($status == 'PENDING PAYMENT'): ?>
                                        <div style="margin-top: 5px;">
                                            <a href="payment.php?order_id=<?php echo $order_id; ?>" style="font-size: 0.75rem; color: #e74c3c; text-decoration: none; border-bottom: 1px dotted #e74c3c;">Pay Now &rarr;</a>
                                        </div>
                                    <?php endif; ?>
                                    -->
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
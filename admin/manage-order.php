<?php include('partials/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper">
        <h1 class="text-center" style="margin-bottom: 30px; color: #2f3542;">Manage Orders</h1>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 15px;">ID</th>
                        <th style="padding: 15px;">Customer</th>
                        <th style="padding: 15px;">Total</th>
                        <th style="padding: 15px;">Status</th>
                        <th style="padding: 15px;">Staff Handled</th>
                        <th style="padding: 15px;">Receipt</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Oracle SQL: Table name 'ORDERS' used to avoid reserved word conflict
                        $sql = "SELECT o.ORDER_ID, c.CUST_USERNAME, o.GRAND_TOTAL, d.DELIVERY_STATUS, 
                                       p.PAYMENT_STATUS, p.RECEIPT_FILE, s.STAFF_USERNAME
                                FROM ORDERS o 
                                JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID 
                                LEFT JOIN DELIVERY d ON o.ORDER_ID = d.ORDER_ID 
                                LEFT JOIN PAYMENT p ON o.ORDER_ID = p.ORDER_ID 
                                LEFT JOIN STAFF s ON o.STAFF_ID = s.STAFF_ID
                                ORDER BY o.ORDER_ID DESC";

                        $stmt = oci_parse($conn, $sql);
                        oci_execute($stmt);

                        $has_data = false;

                        // Fetching rows; Oracle returns keys in UPPERCASE
                        while($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                            $has_data = true;
                            $order_id = $row['ORDER_ID'];
                            $customer = $row['CUST_USERNAME'];
                            $total = $row['GRAND_TOTAL'];
                            $status = $row['DELIVERY_STATUS'] ?? "Ordered";
                            $handled_by = $row['STAFF_USERNAME'] ?? "<i>Unassigned</i>";
                            
                            // FIX: Using null coalescing operator to prevent "Undefined array key" warning
                            $receipt = $row['RECEIPT_FILE'] ?? ""; 
                            ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 15px; font-weight: bold; color: #ff4757;">#<?php echo $order_id; ?></td>
                                <td style="padding: 15px;"><?php echo $customer; ?></td>
                                <td style="padding: 15px;">RM <?php echo number_format($total, 2); ?></td>
                                <td style="padding: 15px;">
                                    <?php 
                                        $color = ($status == 'Delivered') ? '#2ecc71' : (($status == 'Cancelled') ? '#ff4757' : '#e67e22');
                                        echo "<span style='background: $color; padding: 4px 10px; border-radius: 5px; font-size: 0.8rem; font-weight: bold;'>$status</span>";
                                    ?>
                                </td>
                                <td style="padding: 15px; font-size: 0.85rem; color: #a4b0be;"><?php echo $handled_by; ?></td>
                                <td style="padding: 15px;">
                                    <?php if($receipt != "" && $receipt != null): ?>
                                        <a href="../images/receipts/<?php echo $receipt; ?>" target="_blank" style="color: #3498db; text-decoration: none; font-weight: bold;">View</a>
                                    <?php else: ?>
                                        <small style="color:#747d8c;">No File</small>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px;">
                                    <a href="update-order.php?id=<?php echo $order_id; ?>" 
                                       style="color: white; background: #3498db; padding: 5px 12px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; font-weight: bold;">
                                        Update
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        
                        oci_free_statement($stmt);

                        if(!$has_data) {
                            echo "<tr><td colspan='7' style='padding: 30px; text-align: center; color: #a4b0be;'>No orders found.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('partials/footer.php'); ?>
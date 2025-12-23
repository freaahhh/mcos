<?php include('partials-front/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0; min-height: 80vh;">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
        
        <div class="order-details-box" style="background-color: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            
            <h2 class="text-center" style="margin-bottom: 30px; color: #2f3542; font-weight: bold;">Order Invoice</h2>
            
            <?php 
                if(isset($_GET['id'])) {
                    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
                    $cust_id = $_SESSION['u_id'];

                    // SQL Query joining ORDER, DELIVERY, and STAFF tables
                    // Added security check: AND o.cust_ID = $cust_id
                    $sql = "SELECT o.*, d.delivery_status, d.delivery_time, d.delivery_date, 
                                   s.staff_first_name, s.staff_last_name
                            FROM `ORDER` o 
                            LEFT JOIN DELIVERY d ON o.order_ID = d.order_ID 
                            LEFT JOIN STAFF s ON d.staff_ID = s.staff_ID 
                            WHERE o.order_ID = $order_id AND o.cust_ID = $cust_id";

                    $res = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($res);

                    if($row) {
                        $order_date = date('d M Y, H:i', strtotime($row['order_date']));
                        $grand_total = $row['grand_total'];
                        $delivery_charge = $row['delivery_charge'];
                        $status = $row['delivery_status'] ?? "Processing";
                        $staff_name = $row['staff_first_name'] ? $row['staff_first_name']." ".$row['staff_last_name'] : "Not Assigned Yet";
                    } else {
                        header('location:'.SITEURL.'myorders.php');
                        exit();
                    }
                } else {
                    header('location:'.SITEURL.'myorders.php');
                    exit();
                }

                // Check if feedback already exists for this order
                $sql_fb_check = "SELECT * FROM FEEDBACK WHERE order_ID = $order_id";
                $res_fb_check = mysqli_query($conn, $sql_fb_check);
                $feedback_exists = mysqli_num_rows($res_fb_check) > 0;
            ?>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f2f6; padding-bottom: 25px; margin-bottom: 30px;">
                <div style="line-height: 1.8;">
                    <p style="margin:0;"><strong style="color: #747d8c;">Order ID:</strong> <span style="color: #ff4757; font-weight: bold;">#<?php echo $order_id; ?></span></p>
                    <p style="margin:0;"><strong style="color: #747d8c;">Date:</strong> <?php echo $order_date; ?></p>
                    <p style="margin:0;"><strong style="color: #747d8c;">Delivery Staff:</strong> <?php echo $staff_name; ?></p>
                </div>
                <div style="text-align: right;">
                    <p style="margin-bottom: 8px; font-weight: bold; color: #747d8c;">Order Status</p>
                    <?php 
                        $status_bg = ($status == 'Delivered') ? '#2ecc71' : (($status == 'Cancelled') ? '#ff4757' : '#f1c40f');
                    ?>
                    <span style="padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: bold; background-color: <?php echo $status_bg; ?>; color: white; display: inline-block; box-shadow: 0 4px 10px <?php echo $status_bg; ?>55;">
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
                            $sql2 = "SELECT om.*, m.menu_name 
                                     FROM ORDER_MENU om 
                                     JOIN MENU m ON om.menu_ID = m.menu_ID 
                                     WHERE om.order_ID = $order_id";
                            
                            $res2 = mysqli_query($conn, $sql2);
                            $sn = 1;
                            while($item = mysqli_fetch_assoc($res2)) {
                                ?>
                                <tr style="border-bottom: 1px solid #f1f2f6;">
                                    <td style="padding: 15px; color: #747d8c;"><?php echo $sn++; ?></td>
                                    <td style="padding: 15px; font-weight: 600; color: #2f3542;"><?php echo $item['menu_name']; ?></td>
                                    <td style="padding: 15px;">RM <?php echo number_format($item['sub_total'] / $item['order_quantity'], 2); ?></td>
                                    <td style="padding: 15px; text-align: center;"><?php echo $item['order_quantity']; ?></td>
                                    <td style="padding: 15px; text-align: right; font-weight: bold; color: #2f3542;">RM <?php echo number_format($item['sub_total'], 2); ?></td>
                                </tr>
                                <?php
                            }
                        ?>
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
                <a href="myorders.php" style="padding: 12px 30px; text-decoration: none; border-radius: 8px; background-color: #2f3542; color: white; font-weight: bold; transition: 0.3s;">
                    Back to My Orders
                </a>
                
                <?php if(!$feedback_exists): ?>
                    <a href="feedback.php?id=<?php echo $order_id; ?>" style="padding: 12px 30px; text-decoration: none; border-radius: 8px; background-color: #ff4757; color: white; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);">
                        Give Feedback
                    </a>
                <?php else: ?>
                    <button disabled style="padding: 12px 30px; border-radius: 8px; background-color: #2ecc71; color: white; font-weight: bold; border: none; cursor: not-allowed; opacity: 0.8;">
                        Feedback Submitted ✅
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('partials-front/footer.php'); ?>

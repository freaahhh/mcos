<?php include('partials/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 1200px; margin: 0 auto;">
        
        <h1 style="color: #2f3542; margin-bottom: 30px;">Staff Dashboard</h1>
        
        <?php 
            if(isset($_SESSION['login'])) {
                echo $_SESSION['login'];
                unset($_SESSION['login']);
            }
        ?>

        <div class="row" style="display: flex; gap: 20px; margin-bottom: 30px;">
            <?php 
                $stats = [
                    'Categories' => "SELECT * FROM CATEGORY",
                    'Menu Items' => "SELECT * FROM MENU",
                    'Total Orders' => "SELECT * FROM `ORDER`"
                ];
                foreach($stats as $label => $query):
                    $res = mysqli_query($conn, $query);
                    $count = mysqli_num_rows($res);
            ?>
                <div style="flex: 1; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center;">
                    <h1 style="color: #ff4757; margin: 0;"><?php echo $count; ?></h1>
                    <p style="color: #747d8c; font-weight: bold; margin-top: 10px;"><?php echo $label; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row" style="display: flex; gap: 20px; margin-bottom: 30px;">
            
            <div style="flex: 2; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h3 style="border-bottom: 2px solid #f1f2f6; padding-bottom: 10px; margin-bottom: 20px;">Top Performers</h3>
                
                <div style="display: flex; gap: 20px;">
                    <?php 
                        // Fetching Top 2 Best Sellers
                        $sql_top = "SELECT m.menu_name, m.menu_pict, SUM(om.order_quantity) as total_sold 
                                    FROM ORDER_MENU om 
                                    JOIN MENU m ON om.menu_ID = m.menu_ID 
                                    GROUP BY om.menu_ID 
                                    ORDER BY total_sold DESC LIMIT 2";
                        $res_top = mysqli_query($conn, $sql_top);
                        $rank = 1;
                        while($row_top = mysqli_fetch_assoc($res_top)):
                    ?>
                        <div style="flex: 1; text-align: center; background: #f8f9fa; padding: 15px; border-radius: 10px;">
                            <span style="background: #ff4757; color: white; padding: 2px 10px; border-radius: 10px; font-size: 0.8rem;">#<?php echo $rank++; ?> Seller</span>
                            <br><br>
                            <?php if($row_top['menu_pict'] != ""): ?>
                                <img src="../images/food/<?php echo $row_top['menu_pict']; ?>" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="height: 120px; background: #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center;">No Image</div>
                            <?php endif; ?>
                            <h4 style="margin-top: 10px;"><?php echo $row_top['menu_name']; ?></h4>
                            <p style="color: #2ecc71; font-weight: bold;"><?php echo $row_top['total_sold']; ?> Sold</p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div style="flex: 1; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-height: 400px; overflow-y: auto;">
                <h3 style="border-bottom: 2px solid #f1f2f6; padding-bottom: 10px; margin-bottom: 15px;">Recent Feedback</h3>
                <?php 
                    $sql_fb = "SELECT f.feedback, c.cust_username 
                               FROM FEEDBACK f 
                               JOIN CUSTOMER c ON f.cust_ID = c.cust_ID 
                               ORDER BY f.feedback_ID DESC LIMIT 5";
                    $res_fb = mysqli_query($conn, $sql_fb);
                    if(mysqli_num_rows($res_fb) > 0):
                        while($fb = mysqli_fetch_assoc($res_fb)):
                ?>
                    <div style="border-bottom: 1px solid #f1f2f6; padding: 10px 0;">
                        <p style="margin: 0; font-size: 0.9rem; color: #2f3542;">"<?php echo $fb['feedback']; ?>"</p>
                        <small style="color: #ff4757;">- <?php echo $fb['cust_username']; ?></small>
                    </div>
                <?php endwhile; else: echo "<p>No feedback yet.</p>"; endif; ?>
            </div>
        </div>

        <div style="background: #2f3542; color: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="border-bottom: 1px solid #57606f; padding-bottom: 10px; margin-bottom: 25px;">Revenue Summary</h3>
            <div style="display: flex; justify-content: space-around; text-align: center;">
                <?php 
                    // Calculations for Revenue
                    $rev_queries = [
                        'Monthly' => "SELECT SUM(grand_total) AS T FROM `ORDER` WHERE MONTH(order_date) = MONTH(NOW()) AND YEAR(order_date) = YEAR(NOW())",
                        'Yearly' => "SELECT SUM(grand_total) AS T FROM `ORDER` WHERE YEAR(order_date) = YEAR(NOW())",
                        'Lifetime' => "SELECT SUM(grand_total) AS T FROM `ORDER`"
                    ];
                    foreach($rev_queries as $label => $q):
                        $r = mysqli_query($conn, $q);
                        $val = mysqli_fetch_assoc($r)['T'] ?? 0;
                ?>
                    <div>
                        <p style="color: #a4b0be; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;"><?php echo $label; ?></p>
                        <h2 style="color: #2ecc71;">RM <?php echo number_format($val, 2); ?></h2>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>


<?php include('partials/footer.php'); ?>

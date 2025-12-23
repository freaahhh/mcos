<?php include('partials/menu.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 1200px; margin: 0 auto;">
        
        <h1 style="color: #2f3542; margin-bottom: 30px;">Dashboard Overview</h1>
        
        <?php if(isset($_SESSION['login'])) { echo $_SESSION['login']; unset($_SESSION['login']); } ?>

        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            <?php 
                $stats = [
                    'Menu Items' => "SELECT * FROM MENU",
                    'Total Orders' => "SELECT * FROM `ORDER`",
                    'Active Customers' => "SELECT * FROM CUSTOMER"
                ];
                foreach($stats as $label => $query):
                    $res = mysqli_query($conn, $query);
                    $count = mysqli_num_rows($res);
            ?>
                <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #2f3542;">
                    <p style="color: #747d8c; font-size: 0.85rem; text-transform: uppercase; margin: 0;"><?php echo $label; ?></p>
                    <h2 style="color: #2f3542; margin: 5px 0;"><?php echo $count; ?></h2>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            
            <div style="flex: 2; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; color: #2f3542; font-size: 1rem;">Revenue Analysis (Last 7 Days)</h3>
                <canvas id="revenueChart" style="max-height: 250px;"></canvas>
            </div>

            <div style="flex: 1; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
                <h3 style="margin-top: 0; color: #2f3542; font-size: 1rem;">Order Delivery Status</h3>
                <canvas id="statusChart" style="max-height: 200px; margin-top: 20px;"></canvas>
            </div>

        </div>

        <div style="display: flex; gap: 20px;">
            
            <div style="flex: 1.5; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; color: #2f3542; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px;">Top Performers</h3>
                <div style="display: flex; gap: 15px; margin-top: 15px;">
                    <?php 
                        $sql_top = "SELECT m.menu_name, SUM(om.order_quantity) as total_sold 
                                    FROM ORDER_MENU om 
                                    JOIN MENU m ON om.menu_ID = m.menu_ID 
                                    GROUP BY om.menu_ID ORDER BY total_sold DESC LIMIT 2";
                        $res_top = mysqli_query($conn, $sql_top);
                        while($top = mysqli_fetch_assoc($res_top)):
                    ?>
                        <div style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 10px;">
                            <strong style="color: #ff4757; display: block;"><?php echo $top['menu_name']; ?></strong>
                            <span style="font-size: 0.9rem; color: #747d8c;"><?php echo $top['total_sold']; ?> units sold</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div style="flex: 1; background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #ff4757;">Revenue Summary</h3>
                <?php 
                    $total_q = mysqli_query($conn, "SELECT SUM(grand_total) as T FROM `ORDER` WHERE YEAR(order_date) = YEAR(NOW())");
                    $total_v = mysqli_fetch_assoc($total_q)['T'] ?? 0;
                ?>
                <p style="color: #a4b0be; margin-bottom: 5px;">Total Revenue (<?php echo date('Y'); ?>)</p>
                <h1 style="color: #2ecc71; margin: 0;">RM <?php echo number_format($total_v, 2); ?></h1>
                <hr style="border: 0; border-top: 1px solid #57606f; margin: 20px 0;">
                <a href="manage-order.php" style="color: white; font-size: 0.85rem; text-decoration: none; font-weight: bold;">View Detailed Report →</a>
            </div>

        </div>
        <div style="display: flex; gap: 20px; margin-top: 30px;">
            
            <div style="flex: 1.5; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; color: #2f3542; font-size: 1rem;">Feedback by Category</h3>
                <canvas id="feedbackChart" style="max-height: 250px;"></canvas>
            </div>

            <div style="flex: 1; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: #2f3542; font-size: 1rem;">Recent Comments</h3>
                    <a href="manage-feedback.php" style="font-size: 0.8rem; color: #3498db; text-decoration: none; font-weight: bold;">View All</a>
                </div>
                <div style="max-height: 220px; overflow-y: auto; padding-right: 5px;">
                    <?php 
                        $sql_fb = "SELECT f.feedback, c.cust_username FROM feedback f 
                                   JOIN customer c ON f.cust_id = c.cust_id 
                                   ORDER BY f.feedback_id DESC LIMIT 5";
                        $res_fb = mysqli_query($conn, $sql_fb);
                        while($fb = mysqli_fetch_assoc($res_fb)):
                    ?>
                        <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f8f9fa;">
                            <p style="margin: 0; font-size: 0.85rem; color: #57606f; line-height: 1.4;">"<?php echo $fb['feedback']; ?>"</p>
                            <small style="color: #ff4757; font-weight: bold;">- <?php echo $fb['cust_username']; ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

    </div>
</div>


<?php 
// --- Data Preparation for JS Charts ---

// 1. Get Revenue for last 7 days
$days = []; $revs = [];
for($i=6; $i>=0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('D', strtotime($date));
    $q = mysqli_query($conn, "SELECT SUM(grand_total) as total FROM `ORDER` WHERE DATE(order_date) = '$date'");
    $val = mysqli_fetch_assoc($q)['total'] ?? 0;
    $days[] = $label;
    $revs[] = $val;
}

// 2. Get Status Counts
$status_q = mysqli_query($conn, "SELECT delivery_status, COUNT(*) as count FROM DELIVERY GROUP BY delivery_status");
$labels_st = []; $data_st = [];
while($st = mysqli_fetch_assoc($status_q)) {
    $labels_st[] = $st['delivery_status'];
    $data_st[] = $st['count'];
}
?>

<script>
// --- Chart Logic ---

// 1. Line Chart for Revenue
const ctxRev = document.getElementById('revenueChart').getContext('2d');
new Chart(ctxRev, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($days); ?>,
        datasets: [{
            label: 'Daily Revenue (RM)',
            data: <?php echo json_encode($revs); ?>,
            borderColor: '#ff4757',
            backgroundColor: 'rgba(255, 71, 87, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { plugins: { legend: { display: false } } }
});

// 2. Pie Chart for Order Status
const ctxSt = document.getElementById('statusChart').getContext('2d');
new Chart(ctxSt, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($labels_st); ?>,
        datasets: [{
            data: <?php echo json_encode($data_st); ?>,
            backgroundColor: ['#2ecc71', '#f1c40f', '#ff4757', '#3498db']
        }]
    },
    options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
});

// 3. Bar Chart for Feedback Categories
const ctxFb = document.getElementById('feedbackChart').getContext('2d');
<?php 
    // Fixed query to use correct column casing: feedback_cat_ID
    $fb_data = mysqli_query($conn, "SELECT fc.feedback_cat_name, COUNT(f.feedback_ID) as total 
                                    FROM feedback_category fc 
                                    LEFT JOIN feedback f ON fc.feedback_cat_ID = f.feedback_cat_ID 
                                    GROUP BY fc.feedback_cat_ID");
    $labels_fb = []; $counts_fb = [];
    while($row = mysqli_fetch_assoc($fb_data)) {
        $labels_fb[] = $row['feedback_cat_name'];
        $counts_fb[] = $row['total'];
    }
?>
new Chart(ctxFb, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels_fb); ?>,
        datasets: [{
            label: 'Total Feedback',
            data: <?php echo json_encode($counts_fb); ?>,
            backgroundColor: '#3498db',
            borderRadius: 5
        }]
    },
    options: { indexAxis: 'y', plugins: { legend: { display: false } } }
});

</script>

<?php include('partials/footer.php'); ?>

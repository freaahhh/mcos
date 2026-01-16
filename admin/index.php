<?php include('partials/menu.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 1200px; margin: 0 auto;">

        <h1 style="color: #2f3542; margin-bottom: 30px;">Dashboard Overview</h1>

        <?php if (isset($_SESSION['login'])) {
            echo $_SESSION['login'];
            unset($_SESSION['login']);
        } ?>

        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            <?php
            // Changed table name from ORDER to ORDERS
            $stats = [
                'Menu Items' => "SELECT * FROM MENU",
                'Total Orders' => "SELECT * FROM ORDERS",
                'Active Customers' => "SELECT * FROM CUSTOMER"
            ];
            foreach ($stats as $label => $query):
                $stmt_count = oci_parse($conn, $query);
                oci_execute($stmt_count);
                // Manually counting rows for Oracle
                $count = oci_fetch_all($stmt_count, $temp_res);
            ?>
                <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #2f3542;">
                    <p style="color: #747d8c; font-size: 0.85rem; text-transform: uppercase; margin: 0;"><?php echo $label; ?></p>
                    <h2 style="color: #2f3542; margin: 5px 0;"><?php echo $count; ?></h2>
                </div>
            <?php oci_free_statement($stmt_count);
            endforeach; ?>
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
                    // Oracle uses FETCH FIRST X ROWS ONLY instead of LIMIT
                    $sql_top = "SELECT m.MENU_NAME, SUM(om.ORDER_QUANTITY) as TOTAL_SOLD 
                                    FROM ORDER_MENU om 
                                    JOIN MENU m ON om.MENU_ID = m.MENU_ID 
                                    GROUP BY m.MENU_NAME, om.MENU_ID 
                                    ORDER BY TOTAL_SOLD DESC FETCH FIRST 2 ROWS ONLY";
                    $stmt_top = oci_parse($conn, $sql_top);
                    oci_execute($stmt_top);
                    while ($top = oci_fetch_array($stmt_top, OCI_ASSOC)):
                    ?>
                        <div style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 10px;">
                            <strong style="color: #ff4757; display: block;"><?php echo $top['MENU_NAME']; ?></strong>
                            <span style="font-size: 0.9rem; color: #747d8c;"><?php echo $top['TOTAL_SOLD']; ?> units sold</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div style="flex: 1; background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #ff4757;">Revenue Summary</h3>
                <?php
                // Oracle YEAR() replacement: EXTRACT(YEAR FROM order_date)
                $sql_rev = "SELECT SUM(GRAND_TOTAL) as T FROM ORDERS WHERE EXTRACT(YEAR FROM ORDER_DATE) = EXTRACT(YEAR FROM SYSDATE)";
                $stmt_rev = oci_parse($conn, $sql_rev);
                oci_execute($stmt_rev);
                $total_v = oci_fetch_array($stmt_rev, OCI_ASSOC)['T'] ?? 0;
                ?>
                <p style="color: #a4b0be; margin-bottom: 5px;">Total Revenue (<?php echo date('Y'); ?>)</p>
                <h1 style="color: #2ecc71; margin: 0;">RM <?php echo number_format($total_v, 2); ?></h1>
                <hr style="border: 0; border-top: 1px solid #57606f; margin: 20px 0;">
                <a href="manage-order.php" style="color: white; font-size: 0.85rem; text-decoration: none; font-weight: bold;">View Detailed Report →</a>
            </div>
        </div>

        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 30px;">
            <h3 style="margin-top: 0; color: #2f3542; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px; margin-bottom: 25px;">
                <span style="margin-right: 10px;">📊</span> System Report Center
            </h3>

            <div style="display: flex; gap: 20px; flex-wrap: wrap;">

                <div style="flex: 1; min-width: 250px; border: 1px solid #f1f2f6; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s;" onmouseover="this.style.borderColor='#ff4757'" onmouseout="this.style.borderColor='#f1f2f6'">
                    <div>
                        <strong style="display: block; color: #2f3542;">Weekly Sales Report</strong>
                        <span style="font-size: 0.8rem; color: #a4b0be;">Current week performance summary</span>
                    </div>
                    <a href="print-report.php?type=weekly" target="_blank" style="background: #f1f2f6; padding: 10px; border-radius: 50%; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <img src="https://cdn-icons-png.flaticon.com/512/724/724933.png" style="width: 20px;" alt="Download">
                    </a>
                </div>

                <div style="flex: 1; min-width: 250px; border: 1px solid #f1f2f6; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s;" onmouseover="this.style.borderColor='#ff4757'" onmouseout="this.style.borderColor='#f1f2f6'">
                    <div>
                        <strong style="display: block; color: #2f3542;">Monthly Revenue Audit</strong>
                        <span style="font-size: 0.8rem; color: #a4b0be;">Full breakdown of monthly transactions</span>
                    </div>
                    <a href="print-report.php?type=monthly" target="_blank" style="background: #f1f2f6; padding: 10px; border-radius: 50%; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <img src="https://cdn-icons-png.flaticon.com/512/724/724933.png" style="width: 20px;" alt="Download">
                    </a>
                </div>

                <div style="flex: 1; min-width: 250px; border: 1px solid #f1f2f6; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s;" onmouseover="this.style.borderColor='#ff4757'" onmouseout="this.style.borderColor='#f1f2f6'">
                    <div>
                        <strong style="display: block; color: #2f3542;">Product Insights</strong>
                        <span style="font-size: 0.8rem; color: #a4b0be;">Best-seller analytics (High Demand)</span>
                    </div>
                    <a href="print-report.php?type=products" target="_blank" style="background: #f1f2f6; padding: 10px; border-radius: 50%; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <img src="https://cdn-icons-png.flaticon.com/512/724/724933.png" style="width: 20px;" alt="Download">
                    </a>
                </div>

                <div style="flex: 1; min-width: 250px; border: 1px solid #f1f2f6; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s;" onmouseover="this.style.borderColor='#ff4757'" onmouseout="this.style.borderColor='#f1f2f6'">
                    <div>
                        <strong style="display: block; color: #2f3542;">Customer Loyalty</strong>
                        <span style="font-size: 0.8rem; color: #a4b0be;">Frequent purchaser analytics</span>
                    </div>
                    <a href="print-report.php?type=customers" target="_blank" style="background: #f1f2f6; padding: 10px; border-radius: 50%; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <img src="https://cdn-icons-png.flaticon.com/512/724/724933.png" style="width: 20px;" alt="Download">
                    </a>
                </div>

                <div style="flex: 1; min-width: 250px; border: 1px solid #f1f2f6; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s;" onmouseover="this.style.borderColor='#ff4757'" onmouseout="this.style.borderColor='#f1f2f6'">
                    <div>
                        <strong style="display: block; color: #2f3542;">Staff Salary Report</strong>
                        <span style="font-size: 0.8rem; color: #a4b0be;">Payroll Summary</span>
                    </div>
                    <a href="print-report.php?type=salary" target="_blank" style="background: #f1f2f6; padding: 10px; border-radius: 50%; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <img src="https://cdn-icons-png.flaticon.com/512/724/724933.png" style="width: 20px;" alt="Download">
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
// --- Data Preparation for JS Charts ---

// 1. Get Revenue for last 7 days
$days = [];
$revs = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('D', strtotime($date));

    // Oracle date comparison using TO_DATE
    $q_str = "SELECT SUM(GRAND_TOTAL) as TOTAL FROM ORDERS WHERE TRUNC(ORDER_DATE) = TO_DATE(:target_date, 'YYYY-MM-DD')";
    $stmt_q = oci_parse($conn, $q_str);
    oci_bind_by_name($stmt_q, ":target_date", $date);
    oci_execute($stmt_q);

    $val = oci_fetch_array($stmt_q, OCI_ASSOC)['TOTAL'] ?? 0;
    $days[] = $label;
    $revs[] = $val;
}

// 2. Get Status Counts
$status_q_str = "SELECT DELIVERY_STATUS, COUNT(*) as COUNT_ST FROM DELIVERY GROUP BY DELIVERY_STATUS";
$stmt_st = oci_parse($conn, $status_q_str);
oci_execute($stmt_st);
$labels_st = [];
$data_st = [];
while ($st = oci_fetch_array($stmt_st, OCI_ASSOC)) {
    $labels_st[] = $st['DELIVERY_STATUS'];
    $data_st[] = $st['COUNT_ST'];
}
?>

<script>
    // --- Chart.js Configuration ---

    // 1. Revenue Analysis Line Chart
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($days); ?>, // PHP array to JS
            datasets: [{
                label: 'Daily Revenue (RM)',
                data: <?php echo json_encode($revs); ?>,
                borderColor: '#ff4757',
                backgroundColor: 'rgba(255, 71, 87, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#ff4757'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'RM ' + value
                    }
                }
            }
        }
    });

    // 2. Order Delivery Status Doughnut Chart
    const ctxSt = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxSt, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels_st); ?>,
            datasets: [{
                data: <?php echo json_encode($data_st); ?>,
                backgroundColor: ['#2ecc71', '#f1c40f', '#ff4757', '#3498db', '#747d8c'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            }
        }
    });
</script>

<?php include('partials/footer.php'); ?>
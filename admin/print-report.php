<?php
include('../config/constants.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
    exit;
}

$type = $_GET['type'] ?? 'weekly';
$title = "Sales Report";
$sql = "";

// Report Type Logic with GROUP BY and HAVING
// Report Type Logic 
if ($type == 'weekly') {
    $title = "Weekly Sales Summary (Week " . date('W') . ")";
    $sql = "SELECT ORDER_ID, DELIVERY_CHARGE, GRAND_TOTAL, 
                       TO_CHAR(ORDER_DATE, 'YYYY-MM-DD HH24:MI:SS') AS ORDER_DATE_FORMATTED 
                FROM ORDERS 
                WHERE TO_CHAR(ORDER_DATE, 'IYYYIW') = TO_CHAR(SYSDATE, 'IYYYIW') 
                ORDER BY ORDER_DATE DESC";
} elseif ($type == 'monthly') {
    $title = "Monthly Revenue Audit (" . date('F Y') . ")";
    $sql = "SELECT ORDER_ID, DELIVERY_CHARGE, GRAND_TOTAL, 
                       TO_CHAR(ORDER_DATE, 'YYYY-MM-DD HH24:MI:SS') AS ORDER_DATE_FORMATTED 
                FROM ORDERS 
                WHERE TO_CHAR(ORDER_DATE, 'MM-YYYY') = TO_CHAR(SYSDATE, 'MM-YYYY') 
                ORDER BY ORDER_DATE DESC";
} elseif ($type == 'products') {
    // SQL Produk kekal sama sebab tiada kolum Date yang perlu diproses
    $title = "High-Performance Products (Star Items)";
    $sql = "SELECT m.MENU_NAME, SUM(om.ORDER_QUANTITY) as TOTAL_VAL, COUNT(om.ORDER_ID) as SUB_COUNT 
                FROM ORDER_MENU om 
                JOIN MENU m ON om.MENU_ID = m.MENU_ID 
                GROUP BY m.MENU_NAME 
                HAVING SUM(om.ORDER_QUANTITY) > 5 
                ORDER BY TOTAL_VAL DESC";
} elseif ($type == 'customers') {
    // SQL Customer kekal sama
    $title = "Frequent Customer Analytics (Loyalty Report)";
    $sql = "SELECT c.CUST_USERNAME, COUNT(o.ORDER_ID) as TOTAL_VAL, SUM(o.GRAND_TOTAL) as SUB_COUNT 
                FROM ORDERS o 
                JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID 
                GROUP BY c.CUST_USERNAME 
                HAVING COUNT(o.ORDER_ID) >= 2 
                ORDER BY TOTAL_VAL DESC";
} elseif ($type == 'salary') {
    $title = "Staff Payroll & Performance Audit";
    // We use CASE to decide which salary to show
    $sql = "SELECT 
            s.STAFF_FIRST_NAME || ' ' || s.STAFF_LAST_NAME AS FULL_NAME, 
            s.STAFF_TYPE, 
            stats.TOTAL_VAL, -- This is Total Hours
            stats.SUB_COUNT, -- This is Total Days Logged
            CASE 
                WHEN s.STAFF_TYPE = 'Part-Time' THEN (stats.TOTAL_VAL * pt.HOURLY_SALARY)
                WHEN s.STAFF_TYPE = 'Full-Time' THEN 
                    -- Formula: (Monthly Salary / 30) * Days Logged
                    -- We use LEAST to make sure they don't get MORE than their salary if they log > 30 days
                    LEAST(ft.MONTHLY_SALARY, (ft.MONTHLY_SALARY / 30) * stats.SUB_COUNT)
                ELSE 0 
            END AS CALCULATED_SALARY
        FROM STAFF s
        JOIN (
            SELECT STAFF_ID, 
                   SUM(HOURS_WORKED) as TOTAL_VAL, 
                   COUNT(DISTINCT WORK_DATE) as SUB_COUNT -- Use DISTINCT to count unique days
            FROM WORK_LOG
            GROUP BY STAFF_ID
        ) stats ON s.STAFF_ID = stats.STAFF_ID
        LEFT JOIN FULL_TIME ft ON s.STAFF_ID = ft.STAFF_ID
        LEFT JOIN PART_TIME pt ON s.STAFF_ID = pt.STAFF_ID
        ORDER BY stats.TOTAL_VAL DESC";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
            color: #2f3542;
            line-height: 1.6;
        }

        .report-header {
            border-bottom: 2px solid #ff4757;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #fff;
        }

        .report-table th {
            background: #f1f2f6;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 0.9rem;
        }

        .report-table td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 0.9rem;
        }

        .summary-box {
            background: #2f3542;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: right;
        }

        .badge {
            background: #2ecc71;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="report-header">
        <div>
            <h1 style="margin: 0; color: #2f3542;"><?php echo $title; ?></h1>
            <p style="color: #747d8c; margin: 5px 0;">Generated by: <?php echo $_SESSION['staff_username'] ?? 'System Admin'; ?> | Date: <?php echo date('d M Y, H:i'); ?></p>
        </div>
        <button class="print-btn" onclick="window.print()" style="padding: 10px 20px; background: #ff4757; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Print PDF</button>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <?php if ($type == 'products'): ?>
                    <th>Product Name</th>
                    <th>Total Units Sold (Aggregated)</th>
                    <th>Order Occurrences</th>
                <?php elseif ($type == 'customers'): ?>
                    <th>Customer Username</th>
                    <th>Total Orders (Aggregated)</th>
                    <th>Total Revenue Contribution</th>
                <?php elseif ($type == 'salary'): ?>
                    <th>Staff Name</th>
                    <th>Work Hours / Type</th>
                    <th>Calculated Salary</th>
                <?php else: ?>
                    <th>Order ID</th>
                    <th>Transaction Date</th>
                    <th>Delivery Fee</th>
                    <th>Order Total</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt);
            $grand_total = 0;

            while ($row = oci_fetch_array($stmt, OCI_ASSOC)):
            ?>
                <tr>
                    <?php if ($type == 'salary'): ?>
                        <td>
                            <?php echo $row['FULL_NAME']; ?>
                            <span class="badge" style="background:#3498db;"><?php echo $row['STAFF_TYPE']; ?></span>
                        </td>
                        <td>
                            <strong><?php echo $row['TOTAL_VAL']; ?> hrs</strong>
                            <div style="font-size: 0.75rem; color: #747d8c;"><?php echo $row['SUB_COUNT']; ?> days logged</div>
                        </td>
                        <td style="padding: 15px; font-weight: bold; color: #2ed573;">
                            RM <?php echo number_format($row['CALCULATED_SALARY'], 2); ?>
                        </td>

                        <?php
                        // ADD THIS LINE BELOW TO FIX THE TOTAL
                        $grand_total += $row['CALCULATED_SALARY'];
                        ?>
                    <?php elseif ($type == 'products' || $type == 'customers'): ?>
                        <td>
                            <?php echo ($type == 'products') ? $row['MENU_NAME'] : $row['CUST_USERNAME']; ?>
                            <span class="badge">Verified</span>
                        </td>
                        <td style="font-weight: bold;"><?php echo $row['TOTAL_VAL']; ?></td>
                        <td>
                            <?php
                            if ($type == 'products') {
                                echo $row['SUB_COUNT'] . " Times Ordered";
                            } else {
                                echo "RM " . number_format($row['SUB_COUNT'], 2);
                                $grand_total += $row['SUB_COUNT'];
                            }
                            ?>
                        </td>

                    <?php else: ?>
                        <td>#<?php echo $row['ORDER_ID']; ?></td>
                        <td>
                            <?php
                            // TUKAR FORMAT TARIKH DI SINI
                            echo date('d M Y', strtotime($row['ORDER_DATE_FORMATTED']));
                            ?>
                        </td>
                        <td>RM <?php echo number_format($row['DELIVERY_CHARGE'] ?? 0, 2); ?></td>
                        <td style="font-weight: bold;">RM <?php echo number_format($row['GRAND_TOTAL'], 2); ?></td>
                        <?php $grand_total += $row['GRAND_TOTAL']; ?>
                    <?php endif; ?>
                </tr>
            <?php endwhile;
            oci_free_statement($stmt); ?>
        </tbody>
    </table>

    <div class="summary-box">
        <h3 style="margin: 0;">
            <?php echo ($type == 'products') ? "Total Data Rows Aggregated" : "Report Net Total:"; ?>
            <span style="color: #2ecc71; margin-left: 15px;">
                <?php echo ($type == 'products') ? "Success" : "RM " . number_format($grand_total, 2); ?>
            </span>
        </h3>
    </div>

    <p style="text-align: center; margin-top: 50px; font-size: 0.8rem; color: #a4b0be;">
        MCOS Management System - Confidential Internal Report
    </p>
</body>

</html>
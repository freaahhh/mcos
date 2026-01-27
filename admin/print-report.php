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

    $title = "High-Performance Products (Star Items)";
    $sql = "SELECT m.MENU_NAME, SUM(om.ORDER_QUANTITY) as TOTAL_VAL, COUNT(om.ORDER_ID) as SUB_COUNT 
                FROM ORDER_MENU om 
                JOIN MENU m ON om.MENU_ID = m.MENU_ID 
                GROUP BY m.MENU_NAME 
                HAVING SUM(om.ORDER_QUANTITY) > 5 
                ORDER BY TOTAL_VAL DESC";
} elseif ($type == 'customers') {

    $title = "Frequent Customer Analytics (Loyalty Report)";
    $sql = "SELECT c.CUST_USERNAME, COUNT(o.ORDER_ID) as TOTAL_VAL, SUM(o.GRAND_TOTAL) as SUB_COUNT 
                FROM ORDERS o 
                JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID 
                GROUP BY c.CUST_USERNAME 
                HAVING COUNT(o.ORDER_ID) >= 2 
                ORDER BY TOTAL_VAL DESC";
} elseif ($type == 'salary') {
    $title = "Staff Payroll & Performance Audit";
    $sql = "SELECT 
            s.STAFF_FIRST_NAME || ' ' || s.STAFF_LAST_NAME AS FULL_NAME, 
            s.STAFF_TYPE, 
            stats.TOTAL_VAL, 
            stats.SUB_COUNT, 
            CASE 
                WHEN s.STAFF_TYPE = 'Part-Time' THEN (stats.TOTAL_VAL * pt.HOURLY_SALARY)
                WHEN s.STAFF_TYPE = 'Full-Time' THEN 
                    LEAST(ft.MONTHLY_SALARY, (ft.MONTHLY_SALARY / 30) * stats.SUB_COUNT)
                ELSE 0 
            END AS CALCULATED_SALARY
        FROM STAFF s
        JOIN (
            SELECT STAFF_ID, 
                   SUM(HOURS_WORKED) as TOTAL_VAL, 
                   COUNT(DISTINCT WORK_DATE) as SUB_COUNT 
            FROM WORK_LOG
            GROUP BY STAFF_ID
        ) stats ON s.STAFF_ID = stats.STAFF_ID
        LEFT JOIN FULL_TIME ft ON s.STAFF_ID = ft.STAFF_ID
        LEFT JOIN PART_TIME pt ON s.STAFF_ID = pt.STAFF_ID
        ORDER BY stats.TOTAL_VAL DESC";
} elseif ($type == 'dorm_stats') {
    $title = "Dormitory Sales Analysis";
    $sql = "SELECT M.MENU_NAME, C.CUST_DORM, 
            SUM(OM.ORDER_QUANTITY) AS TOTAL_QUANTITY_SOLD,  
            SUM(OM.SUB_TOTAL) AS TOTAL_REVENUE 
            FROM ORDER_MENU OM  
            JOIN MENU M ON OM.MENU_ID = M.MENU_ID  
            JOIN ORDERS O ON O.ORDER_ID = OM.ORDER_ID 
            JOIN CUSTOMER C ON C.CUST_ID = O.CUST_ID 
            GROUP BY M.MENU_NAME, C.CUST_DORM 
            ORDER BY M.MENU_NAME ASC, TOTAL_QUANTITY_SOLD DESC";
} elseif ($type == 'worklog') {
    $title = "Staff Work Log Summary (Monthly)";
    $sql = "SELECT 
                s.STAFF_FIRST_NAME || ' ' || s.STAFF_LAST_NAME AS STAFF_NAME,
                s.STAFF_TYPE,
                NVL(TO_CHAR(w.WORK_DATE, 'YYYY-MM'), 'No Record') AS WORK_MONTH,
                NVL(SUM(w.HOURS_WORKED), 0) AS TOTAL_HOURS,
                COUNT(DISTINCT w.WORK_DATE) AS TOTAL_DAYS,
                COUNT(d.DELIVERY_ID) AS TOTAL_DELIVERIES
            FROM STAFF s
            LEFT JOIN WORK_LOG w ON s.STAFF_ID = w.STAFF_ID
            LEFT JOIN DELIVERY d ON s.STAFF_ID = d.STAFF_ID
            GROUP BY 
                s.STAFF_FIRST_NAME || ' ' || s.STAFF_LAST_NAME,
                s.STAFF_TYPE,
                TO_CHAR(w.WORK_DATE, 'YYYY-MM')
            ORDER BY STAFF_NAME, WORK_MONTH";
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

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            color: white;
        }

        .status-completed {
            background-color: #2ecc71;
        }

        .status-transit {
            background-color: #3498db;
        }

        .status-awaiting {
            background-color: #f39c12;
        }

        .status-pending {
            background-color: #e74c3c;
        }

        .status-processing {
            background-color: #95a5a6;
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

                <?php elseif ($type == 'dorm_stats'): ?>
                    <th>Menu Item</th>
                    <th>Dormitory</th>
                    <th>Quantity Sold</th>
                    <th>Revenue Generated</th>

                <?php elseif ($type == 'worklog'): ?>
                    <th>Staff Name</th>
                    <th>Staff Type</th>
                    <th>Work Month</th>
                    <th>Total Hours Worked</th>
                    <th>Total Days Logged</th>
                    <th>Total Deliveries</th>

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
                    <?php if ($type == 'worklog'): ?>

                        <td><?php echo $row['STAFF_NAME']; ?></td>
                        <td><?php echo $row['STAFF_TYPE']; ?></td>
                        <td><?php echo $row['WORK_MONTH']; ?></td>
                        <td style="font-weight:bold;">
                            <?php echo $row['TOTAL_HOURS'] ?? 0; ?> hrs
                        </td>
                        <td><?php echo $row['TOTAL_DAYS'] ?? 0; ?> days</td>
                        <td><?php echo $row['TOTAL_DELIVERIES'] ?? 0; ?></td>

                    <?php elseif ($type == 'salary'): ?>

                        <td>
                            <?php echo $row['FULL_NAME']; ?>
                            <span class="badge" style="background:#3498db;"><?php echo $row['STAFF_TYPE']; ?></span>
                        </td>
                        <td>
                            <strong><?php echo $row['TOTAL_VAL']; ?> hrs</strong>
                            <div style="font-size: 0.75rem; color: #747d8c;">
                                <?php echo $row['SUB_COUNT']; ?> days logged
                            </div>
                        </td>
                        <td style="font-weight: bold; color: #2ed573;">
                            RM <?php echo number_format($row['CALCULATED_SALARY'], 2); ?>
                        </td>
                        <?php $grand_total += $row['CALCULATED_SALARY']; ?>

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

                    <?php elseif ($type == 'dorm_stats'): ?>
                        <td style="font-weight: bold; color: #2f3542;"><?php echo $row['MENU_NAME']; ?></td>
                        <td>
                            <span class="badge" style="background: #e1b12c; font-size: 0.9em;"><?php echo $row['CUST_DORM']; ?></span>
                        </td>
                        <td><?php echo $row['TOTAL_QUANTITY_SOLD']; ?> Units</td>
                        <td style="font-weight: bold;">RM <?php echo number_format($row['TOTAL_REVENUE'], 2); ?></td>
                        <?php $grand_total += $row['TOTAL_REVENUE']; ?>
                    <?php else: ?>
                        <td>#<?php echo $row['ORDER_ID']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['ORDER_DATE_FORMATTED'])); ?></td>
                        <td>RM <?php echo number_format($row['DELIVERY_CHARGE'] ?? 0, 2); ?></td>
                        <td style="font-weight: bold;">
                            RM <?php echo number_format($row['GRAND_TOTAL'], 2); ?>
                        </td>
                        <?php $grand_total += $row['GRAND_TOTAL']; ?>

                    <?php endif; ?>

                </tr>
            <?php endwhile;
            oci_free_statement($stmt); ?>
        </tbody>
    </table>

    <div class="summary-box">
        <h3 style="margin: 0;">
            <?php
            if ($type == 'products') {
                echo "Total Data Rows Aggregated";
            } elseif ($type == 'worklog') {
                echo "Total Staff Records";
            } else {
                echo "Report Net Total:";
            }
            ?>
            <span style="color: #2ecc71; margin-left: 15px;">
                <?php
                if ($type == 'products') {
                    echo "Success";
                } elseif ($type == 'worklog') {
                    echo "Summary Generated";
                } else {
                    echo "RM " . number_format($grand_total, 2);
                }
                ?>
            </span>
        </h3>
    </div>

    <p style="text-align: center; margin-top: 50px; font-size: 0.8rem; color: #a4b0be;">
        MCOS Management System - Confidential Internal Report
    </p>
</body>

</html>
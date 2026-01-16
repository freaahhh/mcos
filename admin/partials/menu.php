<?php
include('../config/constants.php');

// 1. Authorization: Strict check for Staff role
// Ensure 'staff' matches the casing used in your login.php session assignment
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
    header('location:' . SITEURL . 'login.php');
    exit;
}

// 2. Identify if this is the Head Admin (STAFF_ID 1)
// Based on your previous STAFF table screenshot, Michael Scott is ID 1
$is_admin = (isset($_SESSION['u_id']) && $_SESSION['u_id'] == 1);

// 3. Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MCOS</title>
    <link rel="icon" type="image/png" href="../images/mcoslogo.png">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Centering the content away from screen borders */
        .wrapper {
            width: 95%;
            /* Increased width slightly to give more room */
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 10px;
        }

        .navbar {
            background: white;
            border-bottom: 2px solid #f1f2f6;
        }

        .navbar .wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .menu ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }

        .menu ul li a {
            color: #2f3542;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .menu ul li a:hover {
            color: #ff4757;
        }

        .logout-btn {
            color: #ff4757 !important;
        }

        /* BUTTON FIX: Real button UI instead of highlights */
        .btn-action {
            padding: 8px 16px;
            border-radius: 6px;
            color: white !important;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
        }

        .btn-action:hover {
            opacity: 0.85;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-1px);
        }

        .btn-update {
            background-color: #3498db;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        /* Table Styling */
        .table-admin {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table-admin th {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 15px;
            text-align: left;
        }

        .table-admin td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body>
    <section class="navbar">
        <div class="wrapper">
            <div class="logo">
                <a href="index.php"><img src="../images/mcoslogo.png" style="width: 100px;"></a>
            </div>
            <div class="menu">
                <ul>
                    <li><a href="index.php">Dashboard</a></li>

                    <?php if ($is_admin): ?>
                        <li><a href="worklog.php">Work Log</a></li>
                    <?php endif; ?>

                    <li><a href="manage-staff.php">Staff</a></li>
                    <li><a href="manage-customer.php">Customer</a></li>
                    <li><a href="manage-category.php">Category</a></li>
                    <li><a href="manage-food.php">Menu</a></li>
                    <li><a href="manage-order.php">Order</a></li>
                    <li><a href="manage-feedback.php">Feedback</a></li>
                    <li><a href="manage-profile.php">Profile</a></li>
                    <li><a href="../logout.php" class="logout-btn">Logout</a></li>
                </ul>
            </div>
        </div>
    </section>
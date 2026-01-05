<?php
include('config/constants.php');

// 1. Check Login User
if (!isset($_SESSION['u_id'])) {
    $_SESSION['no-login-message'] = "<div class='error text-center'>Please Login to order food.</div>";
    header('location:' . SITEURL . 'login.php');
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $food_id = (int) $_POST['menu_id'];
    $qty     = (int) $_POST['quantity'];
} elseif (isset($_GET['food_id'])) {
    $food_id = (int) $_GET['food_id'];
    $qty     = isset($_GET['qty']) ? (int) $_GET['qty'] : 1;
} else {
    header('location:' . SITEURL);
    exit();
}

$cust_id = (int) $_SESSION['u_id'];

$sql_check = "SELECT QUANTITY FROM CART WHERE CUST_ID = :cust_id AND MENU_ID = :food_id";
$stid_check = oci_parse($conn, $sql_check);

oci_bind_by_name($stid_check, ":cust_id", $cust_id);
oci_bind_by_name($stid_check, ":food_id", $food_id);
oci_execute($stid_check);

$row = oci_fetch_assoc($stid_check);

if ($row) {
    $sql_update = "UPDATE CART SET QUANTITY = QUANTITY + :qty WHERE CUST_ID = :cust_id AND MENU_ID = :food_id";

    $stid_update = oci_parse($conn, $sql_update);
    oci_bind_by_name($stid_update, ":qty", $qty);
    oci_bind_by_name($stid_update, ":cust_id", $cust_id);
    oci_bind_by_name($stid_update, ":food_id", $food_id);

    $res = oci_execute($stid_update, OCI_COMMIT_ON_SUCCESS);
    oci_free_statement($stid_update);
} else {
    $sql_insert = "INSERT INTO CART (CUST_ID, MENU_ID, QUANTITY) VALUES (:cust_id, :food_id, :qty)";

    $stid_insert = oci_parse($conn, $sql_insert);
    oci_bind_by_name($stid_insert, ":cust_id", $cust_id);
    oci_bind_by_name($stid_insert, ":food_id", $food_id);
    oci_bind_by_name($stid_insert, ":qty", $qty);

    $res = oci_execute($stid_insert, OCI_COMMIT_ON_SUCCESS);
    oci_free_statement($stid_insert);
}

oci_free_statement($stid_check);

if ($res) {
    $_SESSION['order'] = "<div class='success text-center'>Food Added to Cart Successfully.</div>";
} else {
    $e = oci_error();
    $_SESSION['order'] = "<div class='error text-center'>Failed to Add to Cart. Error: " . htmlentities($e['message']) . "</div>";
}

if (isset($_SERVER['HTTP_REFERER'])) {
    header('location:' . $_SERVER['HTTP_REFERER']);
} else {
    header('location:' . SITEURL . 'foods.php');
}
exit();

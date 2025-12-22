<?php 
include('config/constants.php');

if(!isset($_SESSION['u_id'])) {
    header('location:'.SITEURL.'login.php');
    exit();
}

if(isset($_GET['food_id'])) {
    $food_id = $_GET['food_id'];
    $cust_id = $_SESSION['u_id'];
    $qty = isset($_GET['qty']) ? $_GET['qty'] : 1;

    // Check if item already in cart
    $check = "SELECT * FROM CART WHERE cust_ID=$cust_id AND menu_ID=$food_id";
    $res_check = mysqli_query($conn, $check);

    if(mysqli_num_rows($res_check) > 0) {
        // Update quantity
        $sql = "UPDATE CART SET quantity = quantity + $qty WHERE cust_ID=$cust_id AND menu_ID=$food_id";
    } else {
        // Insert new
        $sql = "INSERT INTO CART (cust_ID, menu_ID, quantity) VALUES ($cust_id, $food_id, $qty)";
    }

    if(mysqli_query($conn, $sql)) {
        header('location:'.SITEURL.'foods.php'); // Or stay on the same page
    }
}
?>
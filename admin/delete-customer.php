<?php 
    include('../config/constants.php');

    if(isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "DELETE FROM CUSTOMER WHERE cust_ID=$id";
        $res = mysqli_query($conn, $sql);

        if($res == true) {
            $_SESSION['delete'] = "<div class='success'>Customer Removed Successfully.</div>";
            header('location:'.SITEURL.'admin/manage-customer.php');
        } else {
            $_SESSION['delete'] = "<div class='error'>Failed to Remove Customer.</div>";
            header('location:'.SITEURL.'admin/manage-customer.php');
        }
    } else {
        header('location:'.SITEURL.'admin/manage-customer.php');
    }
?>

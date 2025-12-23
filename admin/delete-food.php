<?php 
    include('../config/constants.php');

    if(isset($_GET['id']) && isset($_GET['image_name'])) {
        $id = $_GET['id'];
        $image_name = $_GET['image_name'];

        // 1. Remove Image
        if($image_name != "") {
            $path = "../images/food/".$image_name;
            unlink($path);
        }

        // 2. Delete from Database
        $sql = "DELETE FROM menu WHERE menu_ID=$id";
        $res = mysqli_query($conn, $sql);

        if($res == true) {
            $_SESSION['delete'] = "<div class='success'>Food Deleted Successfully.</div>";
            header('location:'.SITEURL.'admin/manage-food.php');
        } else {
            $_SESSION['delete'] = "<div class='error'>Failed to Delete Food.</div>";
            header('location:'.SITEURL.'admin/manage-food.php');
        }
    } else {
        header('location:'.SITEURL.'admin/manage-food.php');
    }
?>

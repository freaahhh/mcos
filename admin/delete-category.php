<?php 
    include('../config/constants.php');

    if(isset($_GET['id']) && isset($_GET['image_name'])) {
        $id = $_GET['id'];
        $image_name = $_GET['image_name'];

        // 1. Remove the physical image file
        if($image_name != "") {
            $path = "../images/category/".$image_name;
            $remove = unlink($path); // Delete the file

            if($remove == false) {
                $_SESSION['delete'] = "<div class='error'>Failed to remove category image.</div>";
                header('location:'.SITEURL.'admin/manage-category.php');
                die();
            }
        }

        // 2. Delete from Database
        $sql = "DELETE FROM category WHERE category_ID=$id";
        $res = mysqli_query($conn, $sql);

        if($res == true) {
            $_SESSION['delete'] = "<div class='success'>Category Deleted Successfully.</div>";
            header('location:'.SITEURL.'admin/manage-category.php');
        } else {
            $_SESSION['delete'] = "<div class='error'>Failed to Delete Category.</div>";
            header('location:'.SITEURL.'admin/manage-category.php');
        }
    } else {
        header('location:'.SITEURL.'admin/manage-category.php');
    }
?>

<?php 
    include('../config/constants.php');

    if(isset($_GET['id']) && isset($_GET['image_name'])) {
        // Use filter_input to ensure the ID is an integer for safety
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $image_name = $_GET['image_name'];

        if($id) {
            // 1. Remove the physical image file
            if($image_name != "") {
                $path = "../images/category/".$image_name;
                
                // Check if file exists before attempting to delete
                if(file_exists($path)) {
                    $remove = unlink($path); 

                    if($remove == false) {
                        $_SESSION['delete'] = "<div class='error'>Failed to remove category image file from server.</div>";
                        header('location:'.SITEURL.'admin/manage-category.php');
                        exit();
                    }
                }
            }

            // 2. Delete from Oracle Database
            // Note: Use bind variables (:id) for Oracle standard security
            $sql = "DELETE FROM CATEGORY WHERE CATEGORY_ID = :id";
            
            $stmt = oci_parse($conn, $sql);
            
            // Bind the ID parameter
            oci_bind_by_name($stmt, ":id", $id);
            
            // Execute the deletion
            $res = oci_execute($stmt);

            if($res) {
                $_SESSION['delete'] = "<div class='success'>Category Deleted Successfully.</div>";
            } else {
                $e = oci_error($stmt); // Capture specific Oracle error
                $_SESSION['delete'] = "<div class='error'>Failed to Delete Category: " . $e['message'] . "</div>";
            }
            
            oci_free_statement($stmt);
            header('location:'.SITEURL.'admin/manage-category.php');
            exit();

        } else {
            // Invalid ID provided
            header('location:'.SITEURL.'admin/manage-category.php');
            exit();
        }
    } else {
        // No ID or Image Name provided
        header('location:'.SITEURL.'admin/manage-category.php');
        exit();
    }
?>
<?php 
    include('../config/constants.php');

    if(isset($_GET['id']) && isset($_GET['image_name'])) {
        // Sanitize the ID using filter_input for security
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $image_name = $_GET['image_name'];

        if($id) {
            // 1. Remove the physical image file from the server
            if($image_name != "") {
                $path = "../images/food/".$image_name;
                
                // Verify the file exists before attempting to delete it
                if(file_exists($path)) {
                    unlink($path);
                }
            }

            // 2. Delete from Oracle Database
            // Use bind variables (:id) which is the Oracle standard for security
            $sql = "DELETE FROM MENU WHERE MENU_ID = :id";
            
            $stmt = oci_parse($conn, $sql);
            
            // Bind the ID parameter to the placeholder
            oci_bind_by_name($stmt, ":id", $id);
            
            // Execute the deletion statement
            $res = oci_execute($stmt);

            if($res) {
                $_SESSION['delete'] = "<div class='success'>Food Deleted Successfully.</div>";
            } else {
                // Capture the specific Oracle error if the delete fails
                $e = oci_error($stmt);
                $_SESSION['delete'] = "<div class='error'>Failed to Delete Food: " . $e['message'] . "</div>";
            }
            
            // Clean up resources
            oci_free_statement($stmt);
            header('location:'.SITEURL.'admin/manage-food.php');
            exit();

        } else {
            // Invalid ID format provided
            header('location:'.SITEURL.'admin/manage-food.php');
            exit();
        }
    } else {
        // ID or Image Name missing from the request
        header('location:'.SITEURL.'admin/manage-food.php');
        exit();
    }
?>
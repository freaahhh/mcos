<?php 
    include('../config/constants.php');

    if(isset($_GET['id'])) {
        // Use filter_input to ensure the ID is an integer to prevent errors
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if($id) {
            // Oracle SQL: Use bind variables (:id) for safety
            $sql = "DELETE FROM CUSTOMER WHERE CUST_ID = :id";
            
            $stmt = oci_parse($conn, $sql);
            
            // Bind the ID parameter to the placeholder
            oci_bind_by_name($stmt, ":id", $id);
            
            // Execute the statement
            $res = oci_execute($stmt); 

            if($res) {
                $_SESSION['delete'] = "<div class='success'>Customer Removed Successfully.</div>";
            } else {
                // Capture specific Oracle error message for debugging
                $e = oci_error($stmt); 
                $_SESSION['delete'] = "<div class='error'>Failed to Remove Customer: " . $e['message'] . "</div>";
            }
            
            oci_free_statement($stmt);
            header('location:'.SITEURL.'admin/manage-customer.php');
            exit();
        } else {
            // Invalid ID format
            header('location:'.SITEURL.'admin/manage-customer.php');
            exit();
        }
    } else {
        // No ID provided in URL
        header('location:'.SITEURL.'admin/manage-customer.php');
        exit();
    }
?>
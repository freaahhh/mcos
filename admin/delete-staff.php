<?php 
    include('../config/constants.php');

    if(isset($_GET['id'])) {
        // Validate that the ID is a numeric integer
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if($id) {
            // 1. Transactional Deletion: Remove from child salary tables first
            // We use OCI_NO_AUTO_COMMIT to ensure all deletions succeed before finalizing
            
            // Delete from FULL_TIME if exists
            $sql_ft = "DELETE FROM FULL_TIME WHERE STAFF_ID = :id";
            $stmt_ft = oci_parse($conn, $sql_ft);
            oci_bind_by_name($stmt_ft, ":id", $id);
            oci_execute($stmt_ft, OCI_NO_AUTO_COMMIT);
            oci_free_statement($stmt_ft);

            // Delete from PART_TIME if exists
            $sql_pt = "DELETE FROM PART_TIME WHERE STAFF_ID = :id";
            $stmt_pt = oci_parse($conn, $sql_pt);
            oci_bind_by_name($stmt_pt, ":id", $id);
            oci_execute($stmt_pt, OCI_NO_AUTO_COMMIT);
            oci_free_statement($stmt_pt);

            // 2. Finally, Delete from the main STAFF table
            $sql_staff = "DELETE FROM STAFF WHERE STAFF_ID = :id";
            $stmt_staff = oci_parse($conn, $sql_staff);
            oci_bind_by_name($stmt_staff, ":id", $id);
            
            // Execute and commit the transaction
            if(oci_execute($stmt_staff)) {
                oci_commit($conn); 
                $_SESSION['delete'] = "<div class='success'>Staff Deleted Successfully.</div>";
            } else {
                oci_rollback($conn); // Revert changes if staff deletion fails
                $e = oci_error($stmt_staff);
                $_SESSION['delete'] = "<div class='error'>Failed to Delete Staff: " . $e['message'] . "</div>";
            }
            
            oci_free_statement($stmt_staff);
            header('location:'.SITEURL.'admin/manage-staff.php');
            exit();

        } else {
            header('location:'.SITEURL.'admin/manage-staff.php');
            exit();
        }
    } else {
        header('location:'.SITEURL.'admin/manage-staff.php');
        exit();
    }
?>
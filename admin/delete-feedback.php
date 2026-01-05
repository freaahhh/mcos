<?php 
    // Include constants for DB connection and Session
    include('../config/constants.php');

    // 1. Mandatory Session and Role Check
    // Prevents unauthorized users from triggering this script via direct URL
    if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
        header('location:'.SITEURL.'login.php');
        exit;
    }

    // 2. Strict Admin-Only Authorization
    // Only the head admin (Staff ID 1) has permission to delete feedback
    if($_SESSION['u_id'] != 1) {
        $_SESSION['delete'] = "<div class='error text-center'>Access Denied: Only Admin can delete feedback.</div>";
        header('location:'.SITEURL.'admin/manage-feedback.php');
        exit;
    }

    // 3. Get the Feedback ID to be deleted
    if(isset($_GET['id'])) {
        // Use filter_input for an extra layer of integer validation
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if($id) {
            // 4. SQL Query to Delete Feedback using Oracle standard uppercase casing
            // Use bind variables (:id) to prevent SQL injection
            $sql = "DELETE FROM FEEDBACK WHERE FEEDBACK_ID = :id";

            $stmt = oci_parse($conn, $sql);

            // Bind the ID parameter
            oci_bind_by_name($stmt, ":id", $id);

            // 5. Execute the Query
            $res = oci_execute($stmt);

            // Check if execution was successful
            if($res) {
                // Success Message
                $_SESSION['delete'] = "<div class='success text-center' style='color: #2ecc71; padding: 10px; margin-bottom: 20px;'>Feedback deleted successfully.</div>";
            } else {
                // Failure Message with specific Oracle error
                $e = oci_error($stmt);
                $_SESSION['delete'] = "<div class='error text-center' style='color: #ff4757; padding: 10px; margin-bottom: 20px;'>Failed to delete feedback: " . $e['message'] . "</div>";
            }
            
            oci_free_statement($stmt);
            header('location:'.SITEURL.'admin/manage-feedback.php');
            exit();
        } else {
            // Redirect if ID is invalid format
            header('location:'.SITEURL.'admin/manage-feedback.php');
            exit();
        }
    } else {
        // Redirect if ID is missing
        header('location:'.SITEURL.'admin/manage-feedback.php');
        exit();
    }
?>
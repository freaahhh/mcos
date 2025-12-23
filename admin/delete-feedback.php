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
        // Sanitize input to prevent SQL injection
        $id = mysqli_real_escape_string($conn, $_GET['id']);

        // 4. SQL Query to Delete Feedback based on correct column casing: feedback_ID
        $sql = "DELETE FROM feedback WHERE feedback_ID=$id";

        // Execute the Query
        $res = mysqli_query($conn, $sql);

        // 5. Check if execution was successful
        if($res == true) {
            // Success Message
            $_SESSION['delete'] = "<div class='success text-center' style='color: #2ecc71; padding: 10px; margin-bottom: 20px;'>Feedback deleted successfully.</div>";
            header('location:'.SITEURL.'admin/manage-feedback.php');
        } else {
            // Failure Message
            $_SESSION['delete'] = "<div class='error text-center' style='color: #ff4757; padding: 10px; margin-bottom: 20px;'>Failed to delete feedback. Please try again.</div>";
            header('location:'.SITEURL.'admin/manage-feedback.php');
        }
    } else {
        // Redirect if ID is missing
        header('location:'.SITEURL.'admin/manage-feedback.php');
    }
?>
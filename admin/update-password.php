<?php 
    // 1. Process Logic at the TOP to avoid "Header already sent" errors
    ob_start(); 
    include('partials/menu.php'); 

    if(isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
    } else {
        header('location:'.SITEURL.'admin/manage-staff.php');
        exit();
    }

    // Handle Form Submission
    if(isset($_POST['submit'])) {
        $id = $_POST['id'];
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // 2. Check if current ID and current password match in STAFF table
        $sql = "SELECT * FROM STAFF WHERE staff_ID=$id AND staff_password='$current_password'";
        $res = mysqli_query($conn, $sql);

        if($res == true) {
            if(mysqli_num_rows($res) == 1) {
                // Check if new passwords match
                if($new_password == $confirm_password) {
                    $sql2 = "UPDATE STAFF SET staff_password='$new_password' WHERE staff_ID=$id";
                    if(mysqli_query($conn, $sql2)) {
                        $_SESSION['update'] = "<div class='success'>Password Updated Successfully.</div>";
                        header('location:'.SITEURL.'admin/manage-staff.php');
                        exit();
                    } else {
                        $error_msg = "Failed to update password.";
                    }
                } else {
                    $error_msg = "New passwords do not match.";
                }
            } else {
                $error_msg = "Current password is incorrect.";
            }
        }
    }
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h1 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Change Staff Password</h1>

        <?php if(isset($error_msg)) echo "<div class='alert alert-danger'>$error_msg</div>"; ?>

        <form action="" method="POST">
            <table class="table-no-border" style="width: 100%;">
                <tr style="height: 60px;">
                    <td style="width: 30%;"><strong>Current Password:</strong></td>
                    <td><input type="password" name="current_password" class="form-control" placeholder="Current Password" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 5px;"></td>
                </tr>
                <tr style="height: 60px;">
                    <td><strong>New Password:</strong></td>
                    <td><input type="password" name="new_password" class="form-control" placeholder="New Password" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 5px;"></td>
                </tr>
                <tr style="height: 60px;">
                    <td><strong>Confirm Password:</strong></td>
                    <td><input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 5px;"></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 30px; text-align: center;">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="submit" name="submit" value="Update Password" class="btn-primary" style="padding: 12px 30px; border: none; background-color: #ff4757; color: white; border-radius: 5px; cursor: pointer; font-weight: bold;">
                        <a href="manage-staff.php" style="margin-left: 15px; text-decoration: none; color: #747d8c;">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include('partials/footer.php'); ?>
<?php 
ob_start();
include('partials/menu.php'); 

// Security: Only head admin can add staff
if(!$is_admin) { header('location: manage-staff.php'); exit; }
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h1 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Add New Staff</h1>

        <form action="" method="POST">
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="display:block; margin-bottom:8px; font-weight: bold;">First Name</label>
                    <input type="text" name="first_name" placeholder="First Name" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
                </div>
                <div style="flex: 1;">
                    <label style="display:block; margin-bottom:8px; font-weight: bold;">Last Name</label>
                    <input type="text" name="last_name" placeholder="Last Name" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Contact Number</label>
                <input type="text" name="contact" placeholder="e.g. 0123456789" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Username</label>
                <input type="text" name="username" placeholder="Username" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Password</label>
                <input type="password" name="password" placeholder="Password" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Initial Employment Type</label>
                <select name="emp_type" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
                    <option value="Full">Full-Time (Monthly Salary)</option>
                    <option value="Part">Part-Time (Hourly Salary)</option>
                </select>
            </div>

            <input type="submit" name="submit" value="Register Staff Member" style="width: 100%; background: #2f3542; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            
            <div class="text-center" style="margin-top: 15px;">
                <a href="manage-staff.php" style="color: #747d8c; text-decoration: none;">Cancel</a>
            </div>
        </form>

        <?php 
            if(isset($_POST['submit'])) {
                $first = $_POST['first_name'];
                $last = $_POST['last_name'];
                $contact = $_POST['contact'];
                $username = $_POST['username'];
                $password = $_POST['password'];
                $type = $_POST['emp_type'];

                // 1. Insert into STAFF table with RETURNING clause to get the new ID
                $sql = "INSERT INTO STAFF (STAFF_FIRST_NAME, STAFF_LAST_NAME, STAFF_CONTACT_NO, STAFF_USERNAME, STAFF_PASSWORD, SUPERVISOR_ID) 
                        VALUES (:first_n, :last_n, :contact_n, :user_n, :pass_n, 1) 
                        RETURNING STAFF_ID INTO :new_id";
                
                $stmt = oci_parse($conn, $sql);

                // Bind variables for security and to capture the returned ID
                oci_bind_by_name($stmt, ":first_n", $first);
                oci_bind_by_name($stmt, ":last_n", $last);
                oci_bind_by_name($stmt, ":contact_n", $contact);
                oci_bind_by_name($stmt, ":user_n", $username);
                oci_bind_by_name($stmt, ":pass_n", $password);
                oci_bind_by_name($stmt, ":new_id", $new_staff_id, 10); // Capturing the ID

                if(oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
                    
                    // 2. Assign to Salary Table using the captured $new_staff_id
                    if($type == 'Full') {
                        $sql_sal = "INSERT INTO FULL_TIME (STAFF_ID, MONTHLY_SALARY) VALUES (:sid, 3000.00)";
                    } else {
                        $sql_sal = "INSERT INTO PART_TIME (STAFF_ID, HOURLY_SALARY) VALUES (:sid, 10.00)";
                    }

                    $stmt_sal = oci_parse($conn, $sql_sal);
                    oci_bind_by_name($stmt_sal, ":sid", $new_staff_id);
                    
                    if(oci_execute($stmt_sal, OCI_NO_AUTO_COMMIT)) {
                        oci_commit($conn); // Success: Save both inserts
                        $_SESSION['add'] = "<div class='success text-center'>Staff added successfully.</div>";
                        header('location:'.SITEURL.'admin/manage-staff.php');
                        exit();
                    } else {
                        oci_rollback($conn); // Fail: Revert staff insert
                        $_SESSION['add'] = "<div class='error text-center'>Failed to assign salary type.</div>";
                    }
                } else {
                    $_SESSION['add'] = "<div class='error text-center'>Failed to add staff member.</div>";
                }
                oci_free_statement($stmt);
            }
        ?>
    </div>
</div>

<?php include('partials/footer.php'); ?>
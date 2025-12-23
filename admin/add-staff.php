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
                $first = mysqli_real_escape_string($conn, $_POST['first_name']);
                $last = mysqli_real_escape_string($conn, $_POST['last_name']);
                $contact = mysqli_real_escape_string($conn, $_POST['contact']);
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = mysqli_real_escape_string($conn, $_POST['password']);
                $type = $_POST['emp_type'];

                // 1. Insert into STAFF table
                $sql = "INSERT INTO STAFF (staff_first_name, staff_last_name, staff_contact_no, staff_username, staff_password, supervisor_ID) 
                        VALUES ('$first', '$last', '$contact', '$username', '$password', 1)";
                
                if(mysqli_query($conn, $sql)) {
                    $new_staff_id = mysqli_insert_id($conn);

                    // 2. Assign to Salary Table
                    if($type == 'Full') {
                        mysqli_query($conn, "INSERT INTO FULL_TIME (staff_ID, monthly_salary) VALUES ($new_staff_id, 3000.00)");
                    } else {
                        mysqli_query($conn, "INSERT INTO PART_TIME (staff_ID, hourly_salary) VALUES ($new_staff_id, 10.00)");
                    }

                    $_SESSION['add'] = "<div class='success text-center'>Staff added successfully.</div>";
                    header('location:'.SITEURL.'admin/manage-staff.php');
                    exit();
                }
            }
        ?>
    </div>
</div>

<?php include('partials/footer.php'); ?>

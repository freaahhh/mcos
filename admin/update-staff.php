<?php 
    ob_start(); 
    include('partials/menu.php'); 

    // STRICT SECURITY: Redirect if NOT head admin (ID 1)
    if(!$is_admin) { header('location: manage-staff.php'); exit; }

    $id = $_GET['id'];
    $sql = "SELECT s.*, 
            CASE 
                WHEN ft.staff_ID IS NOT NULL THEN 'Full'
                WHEN pt.staff_ID IS NOT NULL THEN 'Part'
                ELSE 'None'
            END AS current_type
            FROM STAFF s
            LEFT JOIN FULL_TIME ft ON s.staff_ID = ft.staff_ID
            LEFT JOIN PART_TIME pt ON s.staff_ID = pt.staff_ID
            WHERE s.staff_ID = $id";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);

    if(isset($_POST['submit'])) {
        $new_type = $_POST['emp_type'];

        // Transaction: Remove from both first to reset role
        mysqli_query($conn, "DELETE FROM FULL_TIME WHERE staff_ID = $id");
        mysqli_query($conn, "DELETE FROM PART_TIME WHERE staff_ID = $id");

        if($new_type == 'Full') {
            mysqli_query($conn, "INSERT INTO FULL_TIME (staff_ID, monthly_salary) VALUES ($id, 3000.00)");
        } else {
            mysqli_query($conn, "INSERT INTO PART_TIME (staff_ID, hourly_salary) VALUES ($id, 10.00)");
        }
        
        $_SESSION['update'] = "<div class='success text-center'>Staff role updated successfully.</div>";
        header('location: manage-staff.php');
        exit();
    }
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 500px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 class="text-center" style="color: #2f3542; margin-bottom: 25px;">Update Staff Role</h2>
        <form action="" method="POST">
            <p style="margin-bottom: 20px;"><strong>Updating Account:</strong> <span style="color: #ff4757;"><?php echo $row['staff_username']; ?></span></p>
            
            <label style="display:block; margin-bottom:10px; font-weight: bold;">Employment Type</label>
            <select name="emp_type" class="form-control" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="Full" <?php if($row['current_type'] == 'Full') echo "selected"; ?>>Full-Time (Monthly Salary)</option>
                <option value="Part" <?php if($row['current_type'] == 'Part') echo "selected"; ?>>Part-Time (Hourly Salary)</option>
            </select>
            
            <br><br>
            <input type="submit" name="submit" value="Apply Changes" class="btn-primary" style="width: 100%; background: #2f3542; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            <div class="text-center" style="margin-top: 15px;">
                <a href="manage-staff.php" style="color: #747d8c; text-decoration: none;">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include('partials/footer.php'); ?>

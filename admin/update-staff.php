<?php 
    ob_start(); 
    include('partials/menu.php'); 

    // STRICT SECURITY: Redirect if NOT head admin (ID 1)
    if(!$is_admin) { header('location: manage-staff.php'); exit; }

    // Use filter_input to safely get ID from URL
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if(!$id) { header('location: manage-staff.php'); exit; }

    // 1. Fetch current staff details using OCI8
    // Uses a CASE statement to determine current employment status
    $sql = "SELECT s.*, 
            CASE 
                WHEN ft.STAFF_ID IS NOT NULL THEN 'Full'
                WHEN pt.STAFF_ID IS NOT NULL THEN 'Part'
                ELSE 'None'
            END AS CURRENT_TYPE
            FROM STAFF s
            LEFT JOIN FULL_TIME ft ON s.STAFF_ID = ft.STAFF_ID
            LEFT JOIN PART_TIME pt ON s.STAFF_ID = pt.STAFF_ID
            WHERE s.STAFF_ID = :id";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);
    oci_execute($stmt);
    $row = oci_fetch_array($stmt, OCI_ASSOC); // Oracle metadata is UPPERCASE

    if(isset($_POST['submit'])) {
        $new_type = $_POST['emp_type'];

        // 2. Perform Transaction: Group multiple deletions and insertions
        // OCI_NO_AUTO_COMMIT prevents saving until we explicitly call oci_commit
        
        // Remove from Full Time table
        $q_del_ft = oci_parse($conn, "DELETE FROM FULL_TIME WHERE STAFF_ID = :id");
        oci_bind_by_name($q_del_ft, ":id", $id);
        oci_execute($q_del_ft, OCI_NO_AUTO_COMMIT);

        // Remove from Part Time table
        $q_del_pt = oci_parse($conn, "DELETE FROM PART_TIME WHERE STAFF_ID = :id");
        oci_bind_by_name($q_del_pt, ":id", $id);
        oci_execute($q_del_pt, OCI_NO_AUTO_COMMIT);

        // 3. Insert new role based on selection
        if($new_type == 'Full') {
            $q_ins = oci_parse($conn, "INSERT INTO FULL_TIME (STAFF_ID, MONTHLY_SALARY) VALUES (:id, 3000.00)");
        } else {
            $q_ins = oci_parse($conn, "INSERT INTO PART_TIME (STAFF_ID, HOURLY_SALARY) VALUES (:id, 10.00)");
        }
        
        oci_bind_by_name($q_ins, ":id", $id);
        $result = oci_execute($q_ins, OCI_NO_AUTO_COMMIT);

        if($result) {
            oci_commit($conn); // Success: Save all changes permanently
            $_SESSION['update'] = "<div class='success text-center' style='color:#2ecc71;'>Staff role updated successfully.</div>";
        } else {
            oci_rollback($conn); // Failure: Revert to previous state
            $_SESSION['update'] = "<div class='error text-center' style='color:#ff4757;'>Error updating staff role.</div>";
        }
        
        header('location: manage-staff.php');
        exit();
    }
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 500px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 class="text-center" style="color: #2f3542; margin-bottom: 25px;">Update Staff Role</h2>
        
        <?php 
            // Display session messages if they exist
            if(isset($_SESSION['update'])) {
                echo $_SESSION['update'];
                unset($_SESSION['update']);
            }
        ?>

        <form action="" method="POST">
            <p style="margin-bottom: 20px;"><strong>Updating Account:</strong> <span style="color: #ff4757;"><?php echo $row['STAFF_USERNAME']; ?></span></p>
            
            <label style="display:block; margin-bottom:10px; font-weight: bold;">Employment Type</label>
            <select name="emp_type" class="form-control" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="Full" <?php if($row['CURRENT_TYPE'] == 'Full') echo "selected"; ?>>Full-Time (Monthly Salary)</option>
                <option value="Part" <?php if($row['CURRENT_TYPE'] == 'Part') echo "selected"; ?>>Part-Time (Hourly Salary)</option>
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
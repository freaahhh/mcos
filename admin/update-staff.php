<?php
ob_start();
include('partials/menu.php');

// STRICT SECURITY: Redirect if NOT head admin (ID 1)
if (!$is_admin) {
    header('location: manage-staff.php');
    exit;
}

// Use filter_input to safely get ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('location: manage-staff.php');
    exit;
}

// 1. Fetch current staff details using OCI8
// Uses a CASE statement to determine current employment status
$sql = "SELECT s.*, 
        CASE 
            WHEN STAFF_TYPE = 'Full-Time' THEN 'Full'
            WHEN STAFF_TYPE = 'Part-Time' THEN 'Part'
            ELSE 'None'
        END AS CURRENT_TYPE
        FROM STAFF s
        WHERE s.STAFF_ID = :id";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":id", $id);
oci_execute($stmt);
$row = oci_fetch_array($stmt, OCI_ASSOC); // Oracle metadata is UPPERCASE

if (isset($_POST['submit'])) {
    $new_type = $_POST['emp_type']; // 'Full' or 'Part'

    // Convert 'Full'/'Part' to match your database 'Full-Time'/'Part-Time'
    $db_type = ($new_type == 'Full') ? 'Full-Time' : 'Part-Time';

    // 1. UPDATE the main STAFF table first
    $q_staff = oci_parse($conn, "UPDATE STAFF SET STAFF_TYPE = :stype WHERE STAFF_ID = :id");
    oci_bind_by_name($q_staff, ":stype", $db_type);
    oci_bind_by_name($q_staff, ":id", $id);
    oci_execute($q_staff, OCI_NO_AUTO_COMMIT);

    // 2. Remove from Salary tables (Same as before)
    $q_del_ft = oci_parse($conn, "DELETE FROM FULL_TIME WHERE STAFF_ID = :id");
    oci_bind_by_name($q_del_ft, ":id", $id);
    oci_execute($q_del_ft, OCI_NO_AUTO_COMMIT);

    $q_del_pt = oci_parse($conn, "DELETE FROM PART_TIME WHERE STAFF_ID = :id");
    oci_bind_by_name($q_del_pt, ":id", $id);
    oci_execute($q_del_pt, OCI_NO_AUTO_COMMIT);

    // 3. Insert new salary record
    if ($new_type == 'Full') {
        $q_ins = oci_parse($conn, "INSERT INTO FULL_TIME (STAFF_ID, MONTHLY_SALARY) VALUES (:id, 2500.00)");
    } else {
        $q_ins = oci_parse($conn, "INSERT INTO PART_TIME (STAFF_ID, HOURLY_SALARY) VALUES (:id, 8.00)");
    }

    oci_bind_by_name($q_ins, ":id", $id);
    $result = oci_execute($q_ins, OCI_NO_AUTO_COMMIT);

    // 4. Final Transaction Check
    if ($result) {
        oci_commit($conn);
        $_SESSION['update'] = "<div class='success text-center'>Staff updated successfully.</div>";
    } else {
        oci_rollback($conn);
        $_SESSION['update'] = "<div class='error text-center'>Error updating staff.</div>";
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
        if (isset($_SESSION['update'])) {
            echo $_SESSION['update'];
            unset($_SESSION['update']);
        }
        ?>

        <form action="" method="POST">
            <div style="background: #fff9db; border-left: 4px solid #f1c40f; padding: 10px; margin-bottom: 20px; font-size: 0.85rem; color: #856404;">
                <strong>⚠️ Admin Note:</strong> Changing employment type will affect the <strong>current</strong> month's salary report. It is recommended to perform this update at the start of a new month.
            </div>
            <p style="margin-bottom: 20px;"><strong>Updating Account:</strong> <span style="color: #ff4757;"><?php echo $row['STAFF_USERNAME']; ?></span></p>

            <label style="display:block; margin-bottom:10px; font-weight: bold;">Employment Type</label>
            <select name="emp_type" class="form-control" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="Full" <?php if ($row['CURRENT_TYPE'] == 'Full') echo "selected"; ?>>Full-Time (Monthly Salary)</option>
                <option value="Part" <?php if ($row['CURRENT_TYPE'] == 'Part') echo "selected"; ?>>Part-Time (Hourly Salary)</option>
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
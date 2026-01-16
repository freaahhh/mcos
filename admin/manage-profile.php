<?php
ob_start();
include('partials/menu.php');

$staff_id = $_SESSION['u_id'];

// --- 1. PROCESS PROFILE UPDATE (Logic must be before SELECT) ---
if (isset($_POST['update_profile'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $contact = $_POST['contact'];

    // Securely update using Bind Variables
    $sql_update = "UPDATE STAFF SET 
                   STAFF_FIRST_NAME = :fname, 
                   STAFF_LAST_NAME = :lname, 
                   STAFF_CONTACT_NO = :contact 
                   WHERE STAFF_ID = :id";

    $stmt_update = oci_parse($conn, $sql_update);
    oci_bind_by_name($stmt_update, ":fname", $fname);
    oci_bind_by_name($stmt_update, ":lname", $lname);
    oci_bind_by_name($stmt_update, ":contact", $contact);
    oci_bind_by_name($stmt_update, ":id", $staff_id);

    if (oci_execute($stmt_update)) {
        $_SESSION['msg'] = "<div style='color: #2ecc71; background: #e8f8f0; padding: 10px; border-radius: 5px; margin-bottom: 20px;'>Profile updated successfully!</div>";
        header("location: manage-profile.php");
        exit();
    } else {
        $e = oci_error($stmt_update);
        $_SESSION['msg'] = "<div style='color: #ff4757; background: #fff0f0; padding: 10px; border-radius: 5px; margin-bottom: 20px;'>Update failed: " . htmlentities($e['message']) . "</div>";
    }
    oci_free_statement($stmt_update);
}

// --- 2. FETCH STAFF & SUPERVISOR DETAILS ---
$sql = "SELECT s1.*, s2.STAFF_FIRST_NAME AS SUPER_FNAME, s2.STAFF_LAST_NAME AS SUPER_LNAME, s2.STAFF_CONTACT_NO AS SUPER_CONTACT
        FROM STAFF s1
        LEFT JOIN STAFF s2 ON s1.SUPERVISOR_ID = s2.STAFF_ID
        WHERE s1.STAFF_ID = :id";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":id", $staff_id);
oci_execute($stmt);
// Use UPPERCASE keys for Oracle result set
$row = oci_fetch_array($stmt, OCI_ASSOC);

// --- 3. FETCH EMPLOYMENT TYPE ---
// oci_fetch_all is used to count rows in Oracle
$q_ft = oci_parse($conn, "SELECT * FROM FULL_TIME WHERE STAFF_ID = :id");
oci_bind_by_name($q_ft, ":id", $staff_id);
oci_execute($q_ft);
$is_full_time = oci_fetch_all($q_ft, $tmp_ft);

$q_pt = oci_parse($conn, "SELECT * FROM PART_TIME WHERE STAFF_ID = :id");
oci_bind_by_name($q_pt, ":id", $staff_id);
oci_execute($q_pt);
$is_part_time = oci_fetch_all($q_pt, $tmp_pt);

$emp_type = ($is_full_time > 0) ? "Full-Time Staff" : (($is_part_time > 0) ? "Part-Time Staff" : "Not Assigned");

// --- 4. FETCH WORK LOGS ---
// Oracle uses FETCH FIRST syntax for limiting rows
// --- 4. FETCH WORK LOGS ---
$sql_logs = "SELECT 
                TO_CHAR(WORK_DATE, 'DD-Mon-YYYY') AS WORK_DATE_FORMATTED, 
                DAY_PRESENT, 
                HOURS_WORKED 
             FROM WORK_LOG 
             WHERE STAFF_ID = :id 
             ORDER BY WORK_DATE DESC 
             FETCH FIRST 5 ROWS ONLY";

$stmt_logs = oci_parse($conn, $sql_logs);
oci_bind_by_name($stmt_logs, ":id", $staff_id);
oci_execute($stmt_logs);
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 1100px; margin: 0 auto;">
        <h1 style="margin-bottom: 25px; color: #2f3542;">My Profile (Staff)</h1>

        <?php if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        } ?>

        <div style="background: #2f3542; color: white; padding: 35px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #57606f; padding-bottom: 20px; margin-bottom: 25px;">
                <div>
                    <h3 style="color: #ff4757; margin-top: 0;">Employment Info</h3>
                    <p style="margin: 10px 0;"><strong>Status:</strong> <span style="background:#2ecc71; color:white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem;"><?php echo $emp_type; ?></span></p>
                    <p style="margin: 5px 0;"><strong>Username:</strong> <?php echo $row['STAFF_USERNAME']; ?></p>
                </div>
                <div style="text-align: right;">
                    <h3 style="color: #ff4757; margin-top: 0;">Supervisor Details</h3>
                    <?php
                    // Fixed check for Oracle UPPERCASE keys
                    if (isset($row['SUPERVISOR_ID']) && $row['SUPERVISOR_ID'] != null):
                    ?>
                        <p style="margin: 5px 0;"><strong>Name:</strong> <?php echo $row['SUPER_FNAME'] . ' ' . $row['SUPER_LNAME']; ?></p>
                        <p style="margin: 5px 0;"><strong>Contact:</strong> <?php echo $row['SUPER_CONTACT']; ?></p>
                    <?php else: ?>
                        <p>Administrator (Direct Access)</p>
                    <?php endif; ?>
                </div>
            </div>

            <h4 style="color: #ff4757; margin-bottom: 15px;">Recent Work Logs</h4>
            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 12px;">Date</th>
                        <th style="padding: 12px;">Day</th>
                        <th style="padding: 12px;">Hours Worked</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $log_count = 0;
                    while ($log = oci_fetch_array($stmt_logs, OCI_ASSOC)):
                        $log_count++;
                    ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px;"><?php echo $log['WORK_DATE_FORMATTED']; ?></td>
                            <td style="padding: 12px;"><?php echo $log['DAY_PRESENT']; ?></td>
                            <td style="padding: 12px;"><?php echo number_format($log['HOURS_WORKED'], 2); ?> hrs</td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($log_count == 0): ?>
                        <tr>
                            <td colspan="3" style="padding: 20px; text-align: center; color: #a4b0be;">No work logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 450px; background: white; padding: 35px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <h3 style="border-bottom: 2px solid #f1f2f6; padding-bottom: 15px; margin-bottom: 25px;">Update Personal Details</h3>
                <form action="" method="POST">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight: 600;">First Name</label>
                        <input type="text" name="fname" value="<?php echo $row['STAFF_FIRST_NAME']; ?>" class="form-control" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight: 600;">Last Name</label>
                        <input type="text" name="lname" value="<?php echo $row['STAFF_LAST_NAME']; ?>" class="form-control" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display:block; margin-bottom:8px; font-weight: 600;">Contact Number</label>
                        <input type="text" name="contact" value="<?php echo $row['STAFF_CONTACT_NO']; ?>" class="form-control" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
                    </div>
                    <input type="submit" name="update_profile" value="Save Changes" class="btn btn-primary" style="width:100%; background:#3742fa; color:white; border:none; padding:14px; border-radius:8px; font-weight:bold; cursor: pointer;">
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Final cleanup of statements
oci_free_statement($stmt);
oci_free_statement($stmt_logs);
include('partials/footer.php');
?>
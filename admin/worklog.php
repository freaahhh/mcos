<?php

ob_start();
include('partials/menu.php');

// 1. Security Check: Only allow logged-in staff
if (!isset($_SESSION['u_id'])) {
    header('location: login.php');
    exit();
}

$supervisor_id = $_SESSION['u_id'];

// --- 2. PROCESS SUBMISSION (Add New Log) ---
if (isset($_POST['add_log'])) {
    $target_staff_id = $_POST['staff_id'];
    $work_date = $_POST['work_date'];
    $hours = $_POST['hours'];

    // Determine the day name from the date
    $day_name = date('l', strtotime($work_date));

    $sql_insert = "INSERT INTO WORK_LOG (STAFF_ID, WORK_DATE, DAY_PRESENT, HOURS_WORKED) 
                   VALUES (:sid, TO_DATE(:wdate, 'YYYY-MM-DD'), :dayname, :hours)";

    $stmt_insert = oci_parse($conn, $sql_insert);
    oci_bind_by_name($stmt_insert, ":sid", $target_staff_id);
    oci_bind_by_name($stmt_insert, ":wdate", $work_date);
    oci_bind_by_name($stmt_insert, ":dayname", $day_name);
    oci_bind_by_name($stmt_insert, ":hours", $hours);

    if (oci_execute($stmt_insert)) {
        $_SESSION['msg'] = "<div style='color: #2ecc71; padding: 15px; background: #e8f8f0; border-radius: 8px; margin-bottom: 20px;'>Work log added successfully!</div>";
        header("location: worklog.php");
        exit();
    } else {
        $e = oci_error($stmt_insert);
        $_SESSION['msg'] = "<div style='color: #ff4757; padding: 15px; background: #fff0f0; border-radius: 8px; margin-bottom: 20px;'>Error: " . $e['message'] . "</div>";
    }
}

// --- 3. FETCH STAFF UNDER THIS SUPERVISOR (For the dropdown) ---
$sql_staff = "SELECT STAFF_ID, STAFF_FIRST_NAME, STAFF_LAST_NAME FROM STAFF WHERE SUPERVISOR_ID = :sup_id";
$stmt_staff = oci_parse($conn, $sql_staff);
oci_bind_by_name($stmt_staff, ":sup_id", $supervisor_id);
oci_execute($stmt_staff);

// --- 4. FETCH RECENT LOGS RECORDED BY THIS SUPERVISOR ---
// Note: This joins with STAFF table so you can see names
$sql_history = "SELECT l.*, s.STAFF_FIRST_NAME, s.STAFF_LAST_NAME, 
                TO_CHAR(l.WORK_DATE, 'DD-Mon-YYYY') AS FORMATTED_DATE
                FROM WORK_LOG l
                JOIN STAFF s ON l.STAFF_ID = s.STAFF_ID
                WHERE s.SUPERVISOR_ID = :sup_id
                ORDER BY l.WORK_DATE DESC
                FETCH FIRST 10 ROWS ONLY";
$stmt_history = oci_parse($conn, $sql_history);
oci_bind_by_name($stmt_history, ":sup_id", $supervisor_id);
oci_execute($stmt_history);
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0; min-height: 80vh;">
    <div class="wrapper" style="max-width: 1000px; margin: 0 auto;">
        <h1 style="color: #2f3542; margin-bottom: 5px;">Work Log Management</h1><br>

        <?php if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        } ?>

        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px;">
            <form action="" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; align-items: end;">

                <div style="display: flex; flex-direction: column;">
                    <label style="font-size: 0.85rem; font-weight: 700; margin-bottom: 5px;">Staff Member</label>
                    <select name="staff_id" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd; box-sizing: border-box;" required>
                        <option value="">Select Staff...</option>
                        <?php while ($s = oci_fetch_array($stmt_staff, OCI_ASSOC)): ?>
                            <option value="<?php echo $s['STAFF_ID']; ?>"><?php echo $s['STAFF_FIRST_NAME'] . ' ' . $s['STAFF_LAST_NAME']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column;">
                    <label style="font-size: 0.85rem; font-weight: 700; margin-bottom: 5px;">Work Date</label>
                    <input type="date" name="work_date" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd; box-sizing: border-box;" required>
                </div>

                <div style="display: flex; flex-direction: column;">
                    <label style="font-size: 0.85rem; font-weight: 700; margin-bottom: 5px;">Hours</label>
                    <input type="number" step="0.5" name="hours" placeholder="0.0" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd; box-sizing: border-box;" required>
                </div>

                <div style="display: flex; flex-direction: column;">
                    <button type="submit" name="add_log" style="width: 100%; background: #3742fa; color: white; border: none; padding: 11px 25px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s;">
                        Save Log
                    </button>
                </div>

            </form>
        </div>

        <div style="background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="padding: 20px; background: #2f3542; color: white;">
                <h3 style="margin: 0;">Recent Logs Recorded</h3>
            </div>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #eee;">
                        <th style="padding: 15px;">Staff Name</th>
                        <th style="padding: 15px;">Date</th>
                        <th style="padding: 15px;">Day</th>
                        <th style="padding: 15px;">Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $found = false;
                    while ($row = oci_fetch_array($stmt_history, OCI_ASSOC)):
                        $found = true;
                    ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px; font-weight: 600;"><?php echo $row['STAFF_FIRST_NAME'] . ' ' . $row['STAFF_LAST_NAME']; ?></td>
                            <td style="padding: 15px;"><?php echo $row['FORMATTED_DATE']; ?></td>
                            <td style="padding: 15px; color: #747d8c;"><?php echo $row['DAY_PRESENT']; ?></td>
                            <td style="padding: 15px;"><span style="background: #dfe4ea; padding: 4px 10px; border-radius: 15px; font-size: 0.9rem;"><?php echo number_format($row['HOURS_WORKED'], 1); ?> hrs</span></td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if (!$found): ?>
                        <tr>
                            <td colspan="4" style="padding: 30px; text-align: center; color: #a4b0be;">No logs recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
oci_free_statement($stmt_staff);
oci_free_statement($stmt_history);
include('partials/footer.php');
?>
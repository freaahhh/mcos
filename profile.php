<?php
ob_start();
include('partials-front/menu.php');

// Check if customer is logged in
if (!isset($_SESSION['u_id']) || $_SESSION['user_role'] !== 'customer') {
    header('location: login.php');
    exit();
}

$cust_id = $_SESSION['u_id'];

// 1. Fetch Customer details
$sql = "SELECT * FROM CUSTOMER WHERE CUST_ID = :cid";
$res = oci_parse($conn, $sql);
oci_bind_by_name($res, ':cid', $cust_id);
oci_execute($res);
$row = oci_fetch_array($res, OCI_ASSOC + OCI_RETURN_NULLS);

$sql_orders = "SELECT * FROM (
                SELECT 
                    ORDER_ID, 
                    GRAND_TOTAL, 
                    TO_CHAR(ORDER_DATE, 'YYYY-MM-DD HH24:MI:SS') AS ORDER_DATE_FORMATTED 
                FROM ORDERS 
                WHERE CUST_ID = :cid 
                ORDER BY ORDER_DATE DESC
              ) WHERE ROWNUM <= 5";

$res_orders = oci_parse($conn, $sql_orders);
oci_bind_by_name($res_orders, ':cid', $cust_id);
oci_execute($res_orders);

// Update Profile Logic
if (isset($_POST['update_cust'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $dorm = $_POST['dorm'];
    $bank = $_POST['bank'];
    $acc = $_POST['acc'];
    $contact = $_POST['contact'];

    $sql_up = "UPDATE CUSTOMER SET 
        CUST_FIRST_NAME = :fn, 
        CUST_LAST_NAME = :ln, 
        CUST_DORM = :dr, 
        BANK_NAME = :bn, 
        BANK_ACCOUNT = :ba,
        CUST_CONTACT_NO = :cn
        WHERE CUST_ID = :cid";

    $stmt_up = oci_parse($conn, $sql_up);

    oci_bind_by_name($stmt_up, ':fn', $fname);
    oci_bind_by_name($stmt_up, ':ln', $lname);
    oci_bind_by_name($stmt_up, ':dr', $dorm);
    oci_bind_by_name($stmt_up, ':bn', $bank);
    oci_bind_by_name($stmt_up, ':ba', $acc);
    oci_bind_by_name($stmt_up, ':cn', $contact);
    oci_bind_by_name($stmt_up, ':cid', $cust_id);

    if (oci_execute($stmt_up)) {
        $_SESSION['msg'] = "<div style='color: #2ecc71; padding: 10px; text-align:center; font-weight:bold;'>Profile Updated Successfully.</div>";
        header("location: profile.php");
        exit();
    }
}
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
        <h1 style="margin-bottom: 25px; color: #2f3542;">My Profile</h1>

        <?php
        if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        ?>

        <div style="background: #2f3542; color: white; padding: 35px; border-radius: 15px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #57606f; padding-bottom: 20px; margin-bottom: 25px;">
                <div>
                    <h3 style="color: #ff4757; margin-top: 0;">Account Details</h3>
                    <p><strong>Username:</strong> <?php echo $row['CUST_USERNAME']; ?></p>
                    <p><strong>Customer ID:</strong> #<?php echo $cust_id; ?></p>
                </div>
                <div style="text-align: right;">
                    <h3 style="color: #ff4757; margin-top: 0;">Status</h3>
                    <p><strong>Phone:</strong> <?php echo $row['CUST_CONTACT_NO'] ?? 'N/A'; ?></p>
                    <p><strong>Dorm:</strong> <?php echo $row['CUST_DORM'] ?? 'N/A'; ?></p>
                </div>
            </div>

            <h4 style="color: #ff4757;">Recent Orders</h4>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                    <th style="padding: 10px;">Order ID</th>
                    <th style="padding: 10px;">Date</th>
                    <th style="padding: 10px;">Total (RM)</th>
                </tr>
                <?php
                $has_orders = false;
                while ($order = oci_fetch_array($res_orders, OCI_ASSOC)):
                    $has_orders = true;
                    $display_date = date('d M Y, H:i', strtotime($order['ORDER_DATE_FORMATTED']));
                ?>
                    <tr style="border-bottom: 1px solid #444;">
                        <td style="padding: 10px;">#<?php echo $order['ORDER_ID']; ?></td>
                        <td style="padding: 10px;"><?php echo $display_date; ?></td>
                        <td style="padding: 10px;"><?php echo number_format($order['GRAND_TOTAL'], 2); ?></td>
                    </tr>
                <?php endwhile;
                if (!$has_orders) echo "<tr><td colspan='3' style='padding:10px;'>No orders found.</td></tr>";
                ?>
            </table>
        </div>

        <div style="display: flex; gap: 20px;">
            <div style="flex: 1; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <h3>Update Personal Info</h3><br>
                <form method="POST">
                    <label style="display:block; margin-bottom:8px; font-weight: 600;">First Name</label>
                    <input type="text" name="fname" value="<?php echo $row['CUST_FIRST_NAME']; ?>" style="width:100%; margin-bottom:15px; padding:10px;">

                    <label style="display:block; margin-bottom:8px; font-weight: 600;">Last Name</label>
                    <input type="text" name="lname" value="<?php echo $row['CUST_LAST_NAME']; ?>" style="width:100%; margin-bottom:15px; padding:10px;">

                    <label style="display:block; margin-bottom:8px; font-weight: 600;">Contact No</label>
                    <input type="text" name="contact" value="<?php echo $row['CUST_CONTACT_NO']; ?>" style="width:100%; margin-bottom:15px; padding:10px;">

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight: 600;">Dorm</label>
                        <select name="dorm" class="form-control" style="width:100%; padding:10px;" required>
                            <option value="" disabled>Select Dorm / Address</option>
                            <option value="3A" <?= ($row['CUST_DORM'] == '3A') ? 'selected' : ''; ?>>3A</option>
                            <option value="3B" <?= ($row['CUST_DORM'] == '3B') ? 'selected' : ''; ?>>3B</option>
                            <option value="4A" <?= ($row['CUST_DORM'] == '4A') ? 'selected' : ''; ?>>4A</option>
                            <option value="4B" <?= ($row['CUST_DORM'] == '4B') ? 'selected' : ''; ?>>4B</option>
                            <option value="Pejabat" <?= ($row['CUST_DORM'] == 'Pejabat') ? 'selected' : ''; ?>>Pejabat Melati</option>
                            <option value="Non-Resident" <?= ($row['CUST_DORM'] == 'Non-Resident') ? 'selected' : ''; ?>>Non-Resident</option>
                        </select>
                    </div>


                    <input type="hidden" name="bank" value="<?php echo $row['BANK_NAME']; ?>">
                    <input type="hidden" name="acc" value="<?php echo $row['BANK_ACCOUNT']; ?>">

                    <button type="submit" name="update_cust" style="background:#3742fa; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Save Personal Info</button>
                </form>
            </div>

            <div style="flex: 1; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <h3>Update Billing</h3><br>
                <form method="POST">
                    <input type="hidden" name="fname" value="<?php echo $row['CUST_FIRST_NAME']; ?>">
                    <input type="hidden" name="lname" value="<?php echo $row['CUST_LAST_NAME']; ?>">
                    <input type="hidden" name="contact" value="<?php echo $row['CUST_CONTACT_NO']; ?>">
                    <input type="hidden" name="dorm" value="<?php echo $row['CUST_DORM']; ?>">

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight: 600;">Select Bank</label>
                        <select name="bank_name" class="form-control" style="width:100%; padding:10px;" required>
                            <option value="Maybank">Maybank</option>
                            <option value="CIMB Bank">CIMB Bank</option>
                            <option value="Bank Islam">Bank Islam</option>
                            <option value="TNG eWallet">TNG eWallet</option>
                        </select>
                    </div>
                    <label style="display:block; margin-bottom:8px; font-weight: 600;">Account Number</label>
                    <input type="text" name="acc" value="<?php echo $row['BANK_ACCOUNT']; ?>" style="width:100%; margin-bottom:15px; padding:10px;">

                    <button type="submit" name="update_cust" style="background:#ff4757; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Save Billing Info</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('partials-front/footer.php'); ?>
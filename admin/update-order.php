<?php
ob_start();
include('partials/menu.php');

$current_staff_id = $_SESSION['u_id'];

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        // Tambah d.STAFF_ID dalam SELECT
        $sql = "SELECT o.ORDER_ID, d.DELIVERY_STATUS, d.STAFF_ID, p.PAYMENT_STATUS 
        FROM ORDERS o 
        LEFT JOIN DELIVERY d ON o.ORDER_ID = d.ORDER_ID 
        LEFT JOIN PAYMENT p ON o.ORDER_ID = p.ORDER_ID 
        WHERE o.ORDER_ID = :id";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":id", $id);
        oci_execute($stmt);

        $row = oci_fetch_array($stmt, OCI_ASSOC);
        oci_free_statement($stmt);

        // Ambil semua staff untuk dropdown
        $sql_riders = "SELECT STAFF_ID, STAFF_FIRST_NAME || ' ' || STAFF_LAST_NAME AS STAFF_NAME 
                       FROM STAFF";
        $stmt_riders = oci_parse($conn, $sql_riders);
        oci_execute($stmt_riders);
    } else {
        header('location:' . SITEURL . 'admin/manage-order.php');
        exit();
    }
}

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $p_status = $_POST['payment_status'];
    $d_status = $_POST['delivery_status'];
    $rider_id = $_POST['rider_id']; // Ambil terus dari dropdown

    // 1. UPDATE ORDERS
    $sql1 = "UPDATE ORDERS SET STAFF_ID = :staff_id WHERE ORDER_ID = :order_id";
    $stmt1 = oci_parse($conn, $sql1);
    oci_bind_by_name($stmt1, ":staff_id", $current_staff_id);
    oci_bind_by_name($stmt1, ":order_id", $id);
    oci_execute($stmt1, OCI_DEFAULT);

    // 2. UPSERT DELIVERY
    $check_q = oci_parse($conn, "SELECT COUNT(*) AS CNT FROM DELIVERY WHERE ORDER_ID = :id");
    oci_bind_by_name($check_q, ":id", $id);
    oci_execute($check_q);
    $check_res = oci_fetch_array($check_q, OCI_ASSOC);

    if ($p_status == "Failed") {
        $final_d_status = "Cancelled";
        $final_rider_id = null;
    } else {
        $final_d_status = $d_status;
        $final_rider_id = !empty($rider_id) ? $rider_id : null;
    }
    // ---------------------------

    if ($check_res['CNT'] == 0) {
        $sql_d = "INSERT INTO DELIVERY (ORDER_ID, DELIVERY_STATUS, STAFF_ID) VALUES (:order_id, :status, :rider_id)";
    } else {
        $sql_d = "UPDATE DELIVERY SET DELIVERY_STATUS = :status, STAFF_ID = :rider_id WHERE ORDER_ID = :order_id";
    }

    $stmt_d = oci_parse($conn, $sql_d);
    oci_bind_by_name($stmt_d, ":status", $final_d_status);
    oci_bind_by_name($stmt_d, ":rider_id", $final_rider_id);
    oci_bind_by_name($stmt_d, ":order_id", $id);
    oci_execute($stmt_d, OCI_DEFAULT);

    // 3. UPDATE PAYMENT
    $sql_p = "UPDATE PAYMENT SET PAYMENT_STATUS = :p_status WHERE ORDER_ID = :order_id";
    $stmt_p = oci_parse($conn, $sql_p);
    oci_bind_by_name($stmt_p, ":p_status", $p_status);
    oci_bind_by_name($stmt_p, ":order_id", $id);
    oci_execute($stmt_p, OCI_DEFAULT);

    oci_commit($conn);
    header('location:' . SITEURL . 'admin/manage-order.php');
    exit();
}
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 600px; background: white; padding: 40px; border-radius: 15px;">
        <h2 class="text-center">Update Order #<?php echo $id; ?></h2>
        <p class="text-center" style="font-size: 0.8rem; color: #a4b0be;">Logged by Staff ID: <?php echo $current_staff_id; ?></p>

        <form action="" method="POST">
            <label>Payment Status</label>
            <select name="payment_status" class="form-control" style="width: 100%; padding: 10px; margin: 10px 0;">
                <option <?php if ($row['PAYMENT_STATUS'] == "Pending") echo "selected"; ?> value="Pending">Pending</option>
                <option <?php if ($row['PAYMENT_STATUS'] == "Verified") echo "selected"; ?> value="Verified">Verified</option>
                <option <?php if ($row['PAYMENT_STATUS'] == "Failed") echo "selected"; ?> value="Failed">Failed</option>
            </select>

            <label>Delivery Status</label>
            <select name="delivery_status" class="form-control" style="width: 100%; padding: 10px; margin: 10px 0;">
                <?php $d_status = $row['DELIVERY_STATUS'] ?? 'Ordered'; ?>
                <option <?php if ($d_status == "Ordered") echo "selected"; ?> value="Ordered">Ordered</option>
                <option <?php if ($d_status == "On Delivery") echo "selected"; ?> value="On Delivery">On Delivery</option>
                <option <?php if ($d_status == "Delivered") echo "selected"; ?> value="Delivered">Delivered</option>
            </select>

            <label>Assign Rider</label>
            <select name="rider_id" class="form-control" style="width: 100%; padding: 10px; margin: 10px 0;">
                <option value="">Select Staff</option>
                <?php
                oci_execute($stmt_riders);
                while ($rider = oci_fetch_array($stmt_riders, OCI_ASSOC)):
                ?>
                    <option <?php if (isset($row['STAFF_ID']) && $row['STAFF_ID'] == $rider['STAFF_ID']) echo "selected"; ?> value="<?php echo $rider['STAFF_ID']; ?>">
                        <?php echo $rider['STAFF_NAME']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" name="submit" value="Update Order" class="btn-primary" style="width: 100%; background: #2f3542; color: white; border: none; padding: 15px; border-radius: 5px; cursor: pointer; margin-top: 20px;">
        </form>
    </div>
</div>
<?php include('partials/footer.php'); ?>
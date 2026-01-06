<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('config/constants.php');

$showAlert = false;
$showError = false;
$exists = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    $cpassword = $_POST["cpassword"] ?? '';
    $customer_name = $_POST["customer_name"] ?? '';
    $customer_contact = $_POST["customer_contact"] ?? '';
    $customer_address = $_POST["customer_address"] ?? '';

    // 1. CHECK IF USER EXISTS
    $sql = "SELECT COUNT(*) AS CNT FROM CUSTOMER WHERE CUST_USERNAME = :un";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":un", $username);
    oci_execute($stid);

    $row = oci_fetch_array($stid, OCI_ASSOC);
    $num = $row['CNT'];

    if ($num == 0) {
        if ($password === $cpassword && !empty($password)) {
            // 2. INSERT NEW CUSTOMER
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    $cpassword = $_POST["cpassword"] ?? '';
    $customer_name = $_POST["customer_name"] ?? ''; // First Name
    $customer_last_name = $_POST["customer_last_name"] ?? ''; // Tambah ini
    $customer_contact = $_POST["customer_contact"] ?? '';
    $customer_address = $_POST["customer_address"] ?? '';

    // ... (logic check num == 0 kekal sama) ...

    if ($num == 0) {
        if ($password === $cpassword && !empty($password)) {
            // 2. INSERT NEW CUSTOMER (Tambah CUST_LAST_NAME)
            $sql_ins = "INSERT INTO CUSTOMER (
                            CUST_USERNAME, 
                            CUST_PASSWORD, 
                            CUST_FIRST_NAME, 
                            CUST_LAST_NAME, 
                            CUST_CONTACT_NO, 
                            CUST_DORM
                        ) VALUES (
                            :un, :pw, :fn, :ln, :cn, :dr
                        )";

            $stid_ins = oci_parse($conn, $sql_ins);
            oci_bind_by_name($stid_ins, ":un", $username);
            oci_bind_by_name($stid_ins, ":pw", $password);
            oci_bind_by_name($stid_ins, ":fn", $customer_name);
            oci_bind_by_name($stid_ins, ":ln", $customer_last_name); // Bind Last Name
            oci_bind_by_name($stid_ins, ":cn", $customer_contact);
            oci_bind_by_name($stid_ins, ":dr", $customer_address);

            // ... (baki execute code sama) ...

            if (oci_execute($stid_ins, OCI_COMMIT_ON_SUCCESS)) {
                $showAlert = true;
            } else {
                $e = oci_error($stid_ins);
                $showError = "Database Error: " . $e['message'];
            }
        } else {
            $showError = "Passwords do not match or are empty";
        }
    } else {
        $exists = "Username not available";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>MCOS - Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="<?php echo SITEURL; ?>">
                    <img src="images/mcoslogo.png" alt="Logo" class="img-responsive">
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
    </section>

    <div class="container">
        <?php
        if ($showAlert) echo '<div class="alert alert-success mt-3">Success! Account created. <a href="login.php">Login here</a></div>';
        if ($showError) echo '<div class="alert alert-danger mt-3">Error! ' . $showError . '</div>';
        if ($exists) echo '<div class="alert alert-warning mt-3">' . $exists . '</div>';
        ?>
    </div>

    <div class="container my-4">
        <h2 class="text-center">Signup Here</h2>
        <form action="" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" name="customer_name" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" name="customer_last_name" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" class="form-control" name="cpassword" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="number" class="form-control" name="customer_contact" required>
            </div>
            <div class="form-group">
                <label>Address / Dorm</label>
                <textarea name="customer_address" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">SignUp</button>
        </form>
    </div>

    <?php include('partials-front/footer.php'); ?>
</body>

</html>
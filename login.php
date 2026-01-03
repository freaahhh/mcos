<?php
// 1. Initialize session and output buffering
ob_start();
session_start();

// 2. Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "staff") {
        header("location: admin/index.php");
    } else {
        header("location: index.php");
    }
    exit;
}

// 3. Include database connection
include('config/constants.php');

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {

        // --- STEP 1: CHECK CUSTOMER TABLE ---
        $sql_cust = "SELECT CUST_ID, CUST_USERNAME, CUST_PASSWORD FROM CUSTOMER WHERE CUST_USERNAME = :username";
        $stid = oci_parse($conn, $sql_cust);
        oci_bind_by_name($stid, ":username", $username);
        oci_execute($stid);

        // CORRECTED: Use oci_fetch_array with OCI_ASSOC
        $row = oci_fetch_array($stid, OCI_ASSOC);

        if ($row) {
            // Oracle returns keys in UPPERCASE
            if ($password === $row['CUST_PASSWORD']) {
                $_SESSION["loggedin"] = true;
                $_SESSION["u_id"] = $row['CUST_ID'];
                $_SESSION["username"] = $row['CUST_USERNAME'];
                $_SESSION["user_role"] = "customer";

                header("location: index.php");
                exit;
            }
        }
        oci_free_statement($stid);

        // --- STEP 2: CHECK STAFF TABLE ---
        $sql_staff = "SELECT STAFF_ID, STAFF_USERNAME, STAFF_PASSWORD FROM STAFF WHERE STAFF_USERNAME = :username";
        $stid = oci_parse($conn, $sql_staff);
        oci_bind_by_name($stid, ":username", $username);
        oci_execute($stid);

        $row = oci_fetch_array($stid, OCI_ASSOC);

        if ($row) {
            if ($password === $row['STAFF_PASSWORD']) {
                $_SESSION["loggedin"] = true;
                $_SESSION["u_id"] = $row['STAFF_ID'];
                $_SESSION["username"] = $row['STAFF_USERNAME'];
                $_SESSION["user_role"] = "staff";

                header("location: admin/index.php");
                exit;
            }
        }
        oci_free_statement($stid);

        $login_err = "Invalid username or password.";
    }
    // Note: Do not close $conn here if you use it in included files later, 
    // but usually it's fine at the end of the script.
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="http://localhost/mcos/" title="Logo">
                    <img src="images/mcoslogo.png" alt="Restaurant Logo" class="img-responsive">
                </a>
            </div>
            <br>
            <div class="clearfix"></div>
        </div>
    </section>

    <div class="wrapper">
        <div class="container my-4 ">
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>

            <?php
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
            </form>
        </div>
    </div>
    <?php include('partials-front/footer.php'); ?>
</body>

</html>
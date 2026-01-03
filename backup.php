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
        $sql_cust = "SELECT cust_id, cust_username, cust_password FROM customer WHERE cust_username = ?";

        if ($stmt = mysqli_prepare($conn, $sql_cust)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $db_username, $db_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if ($password === $db_password) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["u_id"] = $id;
                            $_SESSION["username"] = $db_username;
                            $_SESSION["user_role"] = "customer";

                            header("location: index.php");
                            exit;
                        }
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }

        // --- STEP 2: CHECK STAFF TABLE (If Customer check didn't redirect) ---
        $sql_staff = "SELECT staff_ID, staff_username, staff_password FROM STAFF WHERE staff_username = ?";

        if ($stmt = mysqli_prepare($conn, $sql_staff)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $db_username, $db_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if ($password === $db_password) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["u_id"] = $id;
                            $_SESSION["username"] = $db_username;
                            $_SESSION["user_role"] = "staff";

                            header("location: admin/index.php");
                            exit;
                        }
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }

        // If neither table matched
        $login_err = "Invalid username or password.";
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
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
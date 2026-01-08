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

        $sql_cust = "SELECT CUST_ID, CUST_USERNAME, CUST_PASSWORD FROM CUSTOMER WHERE CUST_USERNAME = :username";
        $stid = oci_parse($conn, $sql_cust);
        oci_bind_by_name($stid, ":username", $username);
        oci_execute($stid);
        $row = oci_fetch_array($stid, OCI_ASSOC);

        if ($row) {
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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MelatiChillz</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

    <style>
        .social ul,
        .footer ul {
            padding: 0;
            list-style-type: none;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 25px;
        }

        .social ul li,
        .footer ul li {
            margin: 0 12px;
        }

        body {
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .main-card {
            display: flex;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 780px;
            min-height: 480px;
        }

        .illustration-side {
            flex: 0.9;
            position: relative;
            background-color: #6c5ce7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .illustration-side img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(108, 92, 231, 0.75);
            z-index: 2;
        }

        .overlay-text {
            position: relative;
            z-index: 3;
            padding: 20px;
        }

        .overlay-text h3 {
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .overlay-text p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .form-side {
            flex: 1.1;
            padding: 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-container img {
            max-width: 130px;
            margin-bottom: 15px;
        }

        h2 {
            font-weight: 700;
            color: #2d3436;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #636e72;
            font-size: 0.85rem;
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #444;
        }

        .form-control {
            border-radius: 8px;
            height: 42px;
            border: 1px solid #ddd;
        }

        .btn-login {
            background: #2d3436;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #000;
            color: #fff;
        }

        .signup-link {
            font-size: 0.85rem;
            margin-top: 20px;
            text-align: center;
        }

        .signup-link a {
            color: #6c5ce7;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .illustration-side {
                display: none;
            }

            .main-card {
                max-width: 400px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="main-card">

            <div class="illustration-side">
                <img src="images/food-delivery.jpeg" alt="Food Delivery">
                <div class="overlay"></div>
                <div class="overlay-text">
                    <h3>Fastest Delivery</h3>
                    <p>Hot meals delivered to you</p>
                </div>
            </div>

            <div class="form-side">
                <div class="logo-container text-center">
                    <a href="index.php">
                        <img src="images/mcoslogo.png" alt="Logo">
                    </a>
                </div>

                <div class="text-center">
                    <h2>Welcome Back</h2>
                    <p class="subtitle">Log in to start your order</p>
                </div>

                <?php
                if (!empty($login_err)) {
                    echo '<div class="alert alert-danger py-2" style="font-size:0.8rem; border-radius:8px;">' . $login_err . '</div>';
                }
                ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" placeholder="Username">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Password">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>

                    <button type="submit" class="btn btn-login">Login to Order</button>

                    <div class="signup-link">
                        Don't have an account? <a href="register.php">Sign up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('partials-front/footer.php'); ?>
</body>

</html>
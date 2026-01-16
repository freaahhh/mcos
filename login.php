<?php
ob_start();
session_start();

// 1. Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "staff") {
        header("location: admin/index.php");
    } else {
        header("location: index.php");
    }
    exit;
}

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
        // Customer Check
        $sql_cust = "SELECT CUST_ID, CUST_USERNAME, CUST_PASSWORD FROM CUSTOMER WHERE CUST_USERNAME = :username";
        $stid = oci_parse($conn, $sql_cust);
        oci_bind_by_name($stid, ":username", $username);
        oci_execute($stid);
        $row = oci_fetch_array($stid, OCI_ASSOC);

        if ($row && $password === $row['CUST_PASSWORD']) {
            $_SESSION["loggedin"] = true;
            $_SESSION["u_id"] = $row['CUST_ID'];
            $_SESSION["username"] = $row['CUST_USERNAME'];
            $_SESSION["user_role"] = "customer";
            header("location: index.php");
            exit;
        }
        oci_free_statement($stid);

        // Staff Check
        $sql_staff = "SELECT STAFF_ID, STAFF_USERNAME, STAFF_PASSWORD FROM STAFF WHERE STAFF_USERNAME = :username";
        $stid = oci_parse($conn, $sql_staff);
        oci_bind_by_name($stid, ":username", $username);
        oci_execute($stid);
        $row = oci_fetch_array($stid, OCI_ASSOC);

        if ($row && $password === $row['STAFF_PASSWORD']) {
            $_SESSION["loggedin"] = true;
            $_SESSION["u_id"] = $row['STAFF_ID'];
            $_SESSION["username"] = $row['STAFF_USERNAME'];
            $_SESSION["user_role"] = "staff";
            header("location: admin/index.php");
            exit;
        }
        header("location: login.php?error=invalidusernameorpwd");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Melati Chillz</title>

    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style/alert.css">

    <style>
        :root {
            --primary: #6c5ce7;
            --dark: #2d3436;
            --light: #f4f7f6;
            --white: #ffffff;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: var(--light);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .active-link {
            color: var(--primary) !important;
        }

        /* Navbar Layout */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 8%;
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .logo img {
            height: 70px;
        }

        nav ul {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        nav ul li a {
            text-decoration: none;
            color: var(--dark);
            margin: 0 15px;
            font-weight: 600;
        }

        .login-signup a {
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 5px;
            font-weight: 600;
            margin-left: 10px;
        }

        .actives {
            background: var(--dark);
            color: #fff !important;
        }

        /* Login Card */
        .main {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .main-top {
            text-align: center;
            margin-bottom: 25px;
        }

        .main-top h2 {
            font-weight: 800;
            color: var(--dark);
            margin: 0;
        }

        .main-wrap {
            display: flex;
            justify-content: center;
            align-items: stretch;
            gap: 30px;
            max-width: 1000px;
            width: 100%;
            margin: 15px auto;
            padding: 0 15px;
        }

        /* Left Card: Image */
        .main-left {
            flex: 1.5;
            background: var(--white);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 450px;
        }

        .main-left img {
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
            background: rgba(108, 92, 231, 0.7);
            z-index: 2;
        }

        .overlay-text {
            position: relative;
            z-index: 3;
            color: white;
            text-align: center;
            padding: 30px;
            width: 100%;
        }

        .overlay-text h3 {
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .overlay-text p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Right Card: Form */
        .main-right {
            flex: 1;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }


        .login-form h3 {
            font-size: 1.5rem;
            margin-bottom: 40px;
            color: var(--dark);
            text-align: center;
            font-weight: 800;
        }

        .logbox {
            display: flex;
            align-items: center;
            border: 1.5px solid #eee;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 5px 15px;
            background: #fff;
        }

        .logbox i {
            color: #b2bec3;
            width: 20px;
            text-align: center;
        }

        .linebox {
            width: 1px;
            height: 20px;
            background: #eee;
            margin: 0 15px;
        }

        .inputbox {
            border: none;
            outline: none;
            flex: 1;
            padding: 10px 0;
            font-size: 1rem;
        }

        .btnLogin {
            width: 100%;
            padding: 14px;
            background: var(--dark);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btnLogin:hover {
            background: #000;
        }

        .footer {
            background: var(--dark);
            color: #fff;
            padding: 40px 0;
            text-align: center;
            margin-top: auto;
        }

        .footer-img {
            height: 45px;
            margin: 0 15px;
        }

        @media (max-width: 768px) {
            .main-left {
                display: none;
            }

            header nav {
                display: none;
            }
        }

        .error-msg {
            color: #e74c3c;
            /* A nice modern red */
            background: #fdf2f2;
            /* Light red background to make it pop */
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            margin-top: -20px;
            /* Pulls it closer to the h3 */
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <header>
        <div class="header-left">
            <div class="logo"><img src="images/mcoslogo.png" alt="Logo"></div>
        </div>
        <nav>
            <ul>
                <li><a href="login.php" class="active-link">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="developers.php">Developers</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="login-signup">
                <a href="login.php" class="actives">Log In</a>
                <a href="register.php">Sign Up</a>
            </div>
        </div>
    </header>

    <section class="main">
        <div class="main-top" style="text-align:center; margin-top:5px;">
            <h2>WELCOME!</h2>
            <p>To Melati Chillz Ordering System</p>
        </div>

        <div class="main-wrap">
            <div class="main-left">
                <img src="images/food-delivery.jpeg" alt="Food Delivery">
                <div class="overlay"></div>
                <div class="overlay-text">
                    <h3>Fastest Delivery</h3>
                    <p>Hot meals delivered to your doorstep</p>
                </div>
            </div>

            <div class="main-right">
                <div class="login-form">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <h3>Log In</h3>

                        <?php if (isset($_GET["error"]) && $_GET["error"] == "invalidusernameorpwd"): ?>
                            <p class="error-msg">Invalid username or password!</p>
                        <?php endif; ?>

                        <div class="logbox">
                            <i class="fa fa-user"></i>
                            <div class="linebox"></div>
                            <input name="username" type="text" placeholder="Username" class="inputbox" required>
                        </div>

                        <div class="logbox">
                            <i class="fa fa-lock"></i>
                            <div class="linebox"></div>
                            <input name="password" type="password" placeholder="Password" class="inputbox" required>
                        </div>

                        <input type="submit" value="Login" class="btnLogin">
                    </form>

                    <div style="margin-top: 20px; text-align: center;">
                        <span style="font-size: 0.9rem;">Not registered? <a href="register.php" style="color:var(--primary); font-weight:700; text-decoration:none;">Create an account</a></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include('partials-front/footer.php'); ?>

    <script src="js/alert-notification.js"></script>
</body>

</html>
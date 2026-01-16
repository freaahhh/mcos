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
    $customer_last_name = $_POST["customer_last_name"] ?? '';
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
            $sql_ins = "INSERT INTO CUSTOMER (CUST_USERNAME, CUST_PASSWORD, CUST_FIRST_NAME, CUST_LAST_NAME, CUST_CONTACT_NO, CUST_DORM) 
                        VALUES (:un, :pw, :fn, :ln, :cn, :dr)";
            $stid_ins = oci_parse($conn, $sql_ins);
            oci_bind_by_name($stid_ins, ":un", $username);
            oci_bind_by_name($stid_ins, ":pw", $password);
            oci_bind_by_name($stid_ins, ":fn", $customer_name);
            oci_bind_by_name($stid_ins, ":ln", $customer_last_name);
            oci_bind_by_name($stid_ins, ":cn", $customer_contact);
            oci_bind_by_name($stid_ins, ":dr", $customer_address);

            if (oci_execute($stid_ins, OCI_COMMIT_ON_SUCCESS)) {
                $showAlert = true;
            } else {
                $e = oci_error($stid_ins);
                $showError = "Database Error: " . $e['message'];
            }
        } else {
            $showError = "Passwords do not match!";
        }
    } else {
        $exists = "Username not available";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Melati Chillz</title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

        /* Main Section */
        .main {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }


        .main-wrap {
            display: flex;
            justify-content: center;
            align-items: stretch;
            gap: 30px;
            max-width: 1100px;
            width: 100%;
            margin: 15px auto;
        }

        .main-left {
            flex: 0.8;
            background: var(--white);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 500px;
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
        }

        .main-right {
            flex: 1.5;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--dark);
            text-align: center;
            font-weight: 800;
        }

        /* Custom Input Boxes to match Login */
        .logbox {
            display: flex;
            align-items: center;
            border: 1.5px solid #eee;
            border-radius: 10px;
            margin-bottom: 15px;
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
            font-size: 0.95rem;
        }

        .form-row-custom {
            display: flex;
            gap: 15px;
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
            margin-top: 10px;
        }

        .btnLogin:hover {
            background: #000;
        }

        .alert-msg {
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 15px;
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


        @media (max-width: 900px) {
            .main-left {
                display: none;
            }

            header nav {
                display: none;
            }
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
                <li><a href="login.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="developers.php">Developers</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="login-signup">
                <a href="login.php">Log In</a>
                <a href="register.php" class="actives">Sign Up</a>
            </div>
        </div>
    </header>

    <section class="main">

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
                    <form method="post" action="">
                        <h3>Sign Up</h3>

                        <?php if ($showAlert): ?>
                            <div class="alert-msg" style="background: #d4edda; color: #155724;">Success! Account created. <a href="login.php">Login here</a></div>
                        <?php endif; ?>
                        <?php if ($showError): ?>
                            <div class="alert-msg" style="background: #f8d7da; color: #721c24;"><?php echo $showError; ?></div>
                        <?php endif; ?>
                        <?php if ($exists): ?>
                            <div class="alert-msg" style="background: #fff3cd; color: #856404;"><?php echo $exists; ?></div>
                        <?php endif; ?>

                        <div class="logbox">
                            <i class="fa fa-user-circle"></i>
                            <div class="linebox"></div>
                            <input name="username" type="text" placeholder="Username" class="inputbox" required>
                        </div>

                        <div class="form-row-custom">
                            <div class="logbox" style="flex:1;">
                                <i class="fa fa-user"></i>
                                <div class="linebox"></div>
                                <input name="customer_name" type="text" placeholder="First Name" class="inputbox" required>
                            </div>
                            <div class="logbox" style="flex:1;">
                                <i class="fa fa-user"></i>
                                <div class="linebox"></div>
                                <input name="customer_last_name" type="text" placeholder="Last Name" class="inputbox" required>
                            </div>
                        </div>

                        <div class="form-row-custom">
                            <div class="logbox" style="flex:1;">
                                <i class="fa fa-lock"></i>
                                <div class="linebox"></div>
                                <input name="password" type="password" placeholder="Password" class="inputbox" required>
                            </div>
                            <div class="logbox" style="flex:1;">
                                <i class="fa fa-check-circle"></i>
                                <div class="linebox"></div>
                                <input name="cpassword" type="password" placeholder="Confirm" class="inputbox" required>
                            </div>
                        </div>

                        <div class="logbox">
                            <i class="fa fa-phone"></i>
                            <div class="linebox"></div>
                            <input name="customer_contact" type="text" placeholder="Phone Number" class="inputbox" required>
                        </div>

                        <div class="logbox">
                            <i class="fa fa-home"></i>
                            <div class="linebox"></div>
                            <input name="customer_address" type="text" placeholder="Dorm / Address" class="inputbox" required>
                        </div>

                        <input type="submit" value="Create Account" class="btnLogin">
                    </form>

                    <div style="margin-top: 20px; text-align: center;">
                        <span style="font-size: 0.9rem;">Already registered? <a href="login.php" style="color:var(--primary); font-weight:700; text-decoration:none;">Log In</a></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('partials-front/footer.php'); ?>
</body>

</html>
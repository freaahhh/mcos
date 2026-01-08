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
    $customer_name = $_POST["customer_name"] ?? ''; // First Name
    $customer_last_name = $_POST["customer_last_name"] ?? ''; // TAMBAH INI
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
            oci_bind_by_name($stid_ins, ":ln", $customer_last_name); // BIND LAST NAME
            oci_bind_by_name($stid_ins, ":cn", $customer_contact);
            oci_bind_by_name($stid_ins, ":dr", $customer_address);

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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | MelatiChillz</title>
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
            padding: 40px 20px;
        }

        .main-card {
            display: flex;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            min-height: 600px;
        }

        .illustration-side {
            flex: 0.8;
            position: relative;
            background-color: #6c5ce7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
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
        }

        .form-side {
            flex: 1.2;
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-container img {
            max-width: 120px;
            margin-bottom: 10px;
        }

        h2 {
            font-weight: 700;
            color: #2d3436;
            font-size: 1.5rem;
        }

        .subtitle {
            color: #636e72;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .form-row .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #444;
            margin-bottom: 2px;
        }

        .form-control {
            border-radius: 8px;
            height: 38px;
            border: 1px solid #ddd;
            font-size: 0.9rem;
        }

        textarea.form-control {
            height: auto;
        }

        .btn-signup {
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

        .btn-signup:hover {
            background: #000;
            color: #fff;
        }

        .login-link {
            font-size: 0.85rem;
            margin-top: 15px;
            text-align: center;
        }

        .login-link a {
            color: #6c5ce7;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .illustration-side {
                display: none;
            }

            .main-card {
                max-width: 450px;
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
                    <h3>Join the Community</h3>
                    <p>Fastest Delivery, Hot Meals</p>
                </div>
            </div>

            <div class="form-side">
                <div class="logo-container text-center">
                    <img src="images/mcoslogo.png" alt="Logo">
                </div>

                <div class="text-center">
                    <h2>Signup Here</h2>
                    <p class="subtitle">Create your account to start ordering</p>
                </div>

                <?php
                if ($showAlert) echo '<div class="alert alert-success py-2" style="font-size:0.8rem;">Success! Account created. <a href="login.php">Login here</a></div>';
                if ($showError) echo '<div class="alert alert-danger py-2" style="font-size:0.8rem;">' . $showError . '</div>';
                if ($exists) echo '<div class="alert alert-warning py-2" style="font-size:0.8rem;">' . $exists . '</div>';
                ?>

                <form action="" method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="customer_name" placeholder="e.g. Siti" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="customer_last_name" placeholder="e.g. Aminah" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="cpassword" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="number" class="form-control" name="customer_contact" required>
                    </div>

                    <div class="form-group">
                        <label>Address / Dorm</label>
                        <textarea name="customer_address" class="form-control" rows="2" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-signup">Create Account</button>

                    <div class="login-link">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('partials-front/footer.php'); ?>
</body>

</html>
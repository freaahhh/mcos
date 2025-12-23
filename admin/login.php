<?php 
// 1. Include constants first to initialize $conn and session_start()
include('../config/constants.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Food Order System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body style="background-color: #f1f2f6;">

    <div class="container" style="margin-top: 10%;">
        <div class="login-box" style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto;">
            
            <h2 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Admin Login</h2>

            <?php 
                // Display session messages if they exist
                if(isset($_SESSION['no-login-message'])) {
                    echo "<div class='alert alert-danger'>".$_SESSION['no-login-message']."</div>";
                    unset($_SESSION['no-login-message']);
                }

                // --- LOGIN LOGIC ---
                if(isset($_POST['submit'])) {
                    $username = trim($_POST['username']);
                    $password = trim($_POST['password']);

                    // Updated Query to recognize STAFF table only
                    $sql = "SELECT staff_ID, staff_username, staff_password FROM STAFF WHERE staff_username = ?";
                    
                    if($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $param_username);
                        $param_username = $username;

                        if(mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_store_result($stmt);

                            if(mysqli_stmt_num_rows($stmt) == 1) {
                                mysqli_stmt_bind_result($stmt, $id, $db_username, $db_password);
                                if(mysqli_stmt_fetch($stmt)) {
                                    // Plain text comparison to match your registration change
                                    if($password === $db_password) {
                                        // Set Sessions
                                        $_SESSION['loggedin'] = true;
                                        $_SESSION['u_id'] = $id;
                                        $_SESSION['username'] = $db_username;
                                        $_SESSION['user_role'] = 'staff';

                                        header('location:'.SITEURL.'admin/index.php');
                                        exit;
                                    } else {
                                        echo "<div class='alert alert-danger'>Invalid Password.</div>";
                                    }
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Username not found.</div>";
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                </div>

                <input type="submit" name="submit" value="Login" class="btn btn-primary btn-block" style="background-color: #ff4757; border: none; padding: 10px;">
            </form>

            <div class="text-center" style="margin-top: 20px;">
                <a href="../index.php" style="color: #747d8c; text-decoration: none;">&larr; Back to Site</a>
            </div>
        </div>
    </div>

</body>
</html>

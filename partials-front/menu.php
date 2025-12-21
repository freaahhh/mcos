<?php include('config/constants.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCOS</title>
    <link rel="icon" type="image/png" href="../images/mcoslogo.png">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="<?php echo SITEURL; ?>" title="Logo">
                    <img src="images/mcoslogo.png" alt="Restaurant Logo" class="img-responsive">
                </a>
            </div>

            <div class="menu text-right">
                <ul>
                    <li>
                        <a href="<?php echo SITEURL; ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo SITEURL; ?>categories.php">Categories</a>
                    </li>
                    <li>
                        <a href="<?php echo SITEURL; ?>foods.php">Foods</a>
                    </li>
                    
                    <?php
                        if(empty($_SESSION["u_id"]))
                        {
                            // Displayed when NOT logged in
                            echo '<li><a href="login.php">Login</a></li>';
                        }
                        else
                        {
                            // Displayed when logged in
                            echo '<li><a href="myorders.php">My Orders</a></li>';
                            echo '<li><a href="profile.php">Profile</a></li>'; // Added Profile link
                            echo '<li><a href="logout.php">Logout</a></li>';
                        }
                    ?>
                </ul>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Melati Chillz</title>

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


        .active-link {
            color: var(--primary) !important;
        }

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
            align-items: center;
            gap: 30px;
            max-width: 1000px;
            width: 100%;
            margin: 10px auto;
        }

        /* FIXED LEFT CARD FOR LOGO */
        .main-left {
            flex: 1;
            background: var(--white);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            min-height: 200px;
        }

        .main-left img {
            width: 100%;
            height: 100%;
            /* 'contain' ensures the whole logo is visible without cropping */
            object-fit: contain;
        }

        .main-right {
            flex: 1.2;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 50px;
            display: flex;
            flex-direction: column;
        }

        .about-content h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--primary);
            font-weight: 800;
        }

        .about-content p {
            line-height: 1.6;
            color: #636e72;
            font-size: 1.05rem;
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
            .main-wrap {
                flex-direction: column;
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
                <li><a href="about.php" class="active-link">About</a></li>
                <li><a href="developers.php">Developers</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="login-signup">
                <a href="login.php">Log In</a>
                <a href="register.php">Sign Up</a>
            </div>
        </div>
    </header>

    <section class="main">
        <div class="main-top">
            <h2>ABOUT</h2>
            <p>Melati Chillz Ordering System</p>
        </div>

        <div class="main-wrap">
            <div class="main-left">
                <img src="images/mcoslogo.png" alt="MCOS Logo">
            </div>

            <div class="main-right">
                <div class="about-content">
                    <h3>What is MCOS?</h3>
                    <p>
                        The Melati Chillz Ordering System (MCOS) is a dedicated platform designed to
                        streamline the food ordering process for students and staff.
                    </p>
                    <p>
                        Our mission is to provide the fastest delivery and freshest meals
                        straight to your dorm, ensuring you can focus on your studies while
                        we handle the cravings.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php include('partials-front/footer.php'); ?>
</body>

</html>
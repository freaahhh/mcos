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

        /* Team Section */
        .team {
            flex: 1;
            padding: 60px 20px;
            text-align: center;
        }

        .team-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }

        .team-col {
            flex: 1;
            min-width: 250px;
            max-width: 280px;
            background: var(--white);
            padding: 30px 20px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .team-col:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(108, 92, 231, 0.2);
        }

        .profileImg {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid var(--light);
            transition: border-color 0.3s;
        }

        .team-col:hover .profileImg {
            border-color: var(--primary);
        }

        .role {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .name {
            font-size: 1.1rem;
            color: var(--dark);
            font-weight: 600;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            header nav {
                display: none;
            }

            .team-col {
                flex: 100%;
                max-width: 100%;
            }
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
                <li><a href="developers.php" class="active-link">Developers</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="login-signup">
                <a href="login.php">Log In</a>
                <a href="register.php">Sign Up</a>
            </div>
        </div>
    </header>

    <section class="team" id="team">
        <div class="team-container">
            <div class="main-top">
                <h2>DEVELOPERS</h2><br>
            </div>
            <div class="row">
                <div class="team-col">
                    <!--<img src="images/members/Farah.png" alt="Farah" class="profileImg" />-->
                    <h3 class="role">2025395433</h3>
                    <p class="name">Farah Adibah Binti Mustafa Kamal</p>
                </div>
                <div class="team-col">
                    <!--<img src="images/Haziera.png" alt="Haziera" class="profileImg" />-->
                    <h3 class="role">2025368369</h3>
                    <p class="name">Hariesa Haziera Binti Adam Afkar</p>
                </div>
                <div class="team-col">
                    <!--<img src="images/Nabilah.png" alt="Nabilah" class="profileImg" />-->
                    <h3 class="role">2025395299</h3>
                    <p class="name">Nurul Nabilah Binti Abd Jalil</p>
                </div>
                <div class="team-col">
                    <!--<img src="images/Liyana.png" alt="Liyana" class="profileImg" />-->
                    <h3 class="role">2025159581</h3>
                    <p class="name">Nadiratul Liyana Binti Mohd Yusman</p>
                </div>
            </div>
        </div>
    </section>

    <?php include('partials-front/footer.php'); ?>
</body>

</html>
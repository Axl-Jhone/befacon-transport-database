<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BeFaCon Transportation Co.</title>

        <link rel="stylesheet" href="assets/css/index.css">
        <link rel="stylesheet" href="assets/css/main.css">
        <script defer src="assets/js/main.js"></script>

        
        <script src="https://kit.fontawesome.com/da8e65aaa6.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php require("includes/db_connect.php"); ?>

        <div id="top-bar">
            <div id="location">
                <i class="fa-solid fa-location-pin"></i>
                <p>Baguio City, Philippines</p>
            </div>
            <nav id="nav-menu">
                <ul>
                    <li><a href="#landing-content">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
            <div id="login">
                <a href="pages/login.php">Login</a>
            </div>
        </div>

        <div id="landing-content">
            <img src="assets/img/landing_page/van.png" alt="Van Image">

            <h2>Welcome to BeFaCon Transportation Co.</h2>
            <p>Your Partner in Motion.</p>
            <a href="#about" class="btn">Learn More</a>
            <a href="#contact" class="btn">Contact Us</a>
        </div>

        <div id="about" class="info">
            <h2>About Us</h2>
            <p>BeFaCon Transport Co. was  founded by Leonardo Benando, Miguel Jr. Factora, and Ax’l Conchada on the year December 25, 2005. Based in Baguio City, Philippines.</p>
        </div>

        <div id="contact" class="info">
            <h2>Contact Us</h2>
            <p>support@befacon.com</p>
            <p>(000) 123-4567</p>
        </div>

        <div id="footer">
            <p>&copy; 2025 BeFaCon Transportation Co. All rights reserved.</p>
        </div>
    </body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFaCon Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="main.js"></script>
    <script src="https://kit.fontawesome.com/da8e65aaa6.js" crossorigin="anonymous"></script> 
</head>
<body>
    <?php require("db_connect.php"); ?>
    <div class="sidebar">
        <div class="logo-header">
            <div class="logo-icon">B</div>
            <p>BeFaCon</p>
            <i class="fa-solid fa-bars" id="hamburger-icon"></i>
        </div>

        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search">
        </div>

        <div class="menu-section">
            <p class="section-title">DASHBOARD</p>
            <ul>
                <li><i class="fa-solid fa-chart-pie"></i>Dashboard</li>
                <li><i class="fa-solid fa-route"></i>Trips</li>
                <li><i class="fa-solid fa-users"></i>Drivers</li>
                <li><i class="fa-solid fa-bus"></i>Vehicles</li>
            </ul>
        </div>
        
        <div class="menu-section bottom">
            <p class="section-title">OTHERS</p>
            <ul>
                <li><i class="fa-solid fa-gear"></i>Settings</li>
                <li><i class="fa-solid fa-circle-question"></i>Help</li>
                <li><i class="fa-solid fa-right-from-bracket"></i>Log out</li>
            </ul>
        </div>
    </div>
</body>
</html>
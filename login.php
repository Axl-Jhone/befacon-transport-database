<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFaCon Transportation Services</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <!-- LOGIN SCREEN -->
    <div id="login-screen"><br><br>
        <h1>BeFaCon Transportation Services</h1>
        <br><br><br>
        <div class="login-box">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <button id="login-btn">Login</button>
        </div>
    </div>

    <!-- MAIN APP (hidden at first) -->
    <div id="main-app" style="display:none;">
        <h2>Welcome to BeFaCon Database</h2>
        <!-- Your DB interface goes here -->
    </div>

    <script src="script.js"></script>
</body>
</html>

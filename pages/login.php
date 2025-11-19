<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">  
</head>
<body>
    <div class = "left-side"></div>

    <div class="right-side">

        <div class="avatar"></div>
        <div class="tagline">Your Partner in motion</div>

        <div class="login-box">
            <h2>Welcome Back!</h2>
            <p>Please enter your credentials to continue</p>

            <form action="login.php" method="POST">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter email">

                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">

                <div class="remember-row">
                    <div><input type="checkbox" name="remember">Remember me</div>
                    <a href="#">Forgot Password</a>
                </div>

                <button class="login-btn" type="submit">LOGIN</button>
            </form>
        </div>

        <div class="footer-links">
            <a href="#">Help</a>
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
        </div>

    </div>


</body>
</html>
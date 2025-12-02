<?php
    session_start();
    require '../includes/db_connect.php';

    if (isset($_SESSION['user'])) {
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin-view/home.php'); 
        } else {
            header('Location: driver-view/home.php');
        }
        exit;
    }

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $sql = "SELECT * FROM user_login WHERE email = ? AND passcode = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            $_SESSION['user'] = $user['email']; 
            $_SESSION['role'] = $user['role'];  
            $_SESSION['driver_id'] = $user['driver_id'];  


            if ($user['role'] === 'admin') {
                header('Location: admin-view/home.php');
            } else {
                header('Location: driver-view/home.php');
            }
            exit;
        } else {
            $error = "Invalid username/email or password.";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">  
    <script defer src="../assets/js/login.js"></script>
</head>
<body>
    <div class="left-side"></div>

    <div class="right-side">

        <a href="../index.php" class="logo-link" aria-label="Home">
            <div class="avatar"></div>
        </a>
        <div class="tagline">Your Partner in motion</div>

        <div class="login-box">

            <h2>Welcome!</h2>
            <p>Please enter your credentials to continue</p>

            <?php if (!empty($error)) : ?>
            <div style="color: red; margin-bottom: 10px;">
            <?= $error ?>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter email" maxlength="30">

                <label>Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter password" maxlength="30">
                    <img src="../assets/img/login_page/closed.png" id="eyeIcon" class="eye-icon" alt="Toggle password">
                </div>

                <div class="forgot-pass-wrapper">
                    <a href="#" id="forgotPasswordLink" class="forgot-link">Forgot Password?</a>
                </div>
                
                <button class="login-btn" type="submit">LOGIN</button>
            </form>
        </div>

    </div>


</body>
</html>
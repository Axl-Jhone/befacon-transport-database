<?php
session_start();

// Redirect to index.php if already logged in
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Handle login form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Dummy login check (replace with DB check in production)
    if ($username === 'Admin' && $password === 'Admin') {
        $_SESSION['user'] = $username;
        header('Location: index.php'); // Redirect after successful login
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFaCon Transportation Services Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h1>BeFaCon Transportation Services</h1>

        <?php if (!empty($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form id="loginForm" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="input-group">
                <button type="submit">Login</button>
            </div>
        </form>
    </div>

    <script src="login.js"></script>
</body>
</html>

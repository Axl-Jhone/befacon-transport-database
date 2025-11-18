<?php 
    $routes = [
        'dashboard' => 'pages/dashboard.php',
        'trips' => 'pages/trips.php',
        'vehicles' => 'pages/vehicles.php'
    ];

    $page_key = $_GET['page'] ?? 'dashboard';

    if (array_key_exists($page_key, $routes)) {
        $content_path = $routes[$page_key];
    } else {
        $content_path = 'pages/error404.php';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBeFaCon</title>
    <link rel="stylesheet" href="/befacon-transport-database/assets/css/navbar.css">
    <link rel="stylesheet" href="/befacon-transport-database/assets/css/sidebar.css">
    <link rel="stylesheet" href="/befacon-transport-database/assets/css/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=circle" />
    <script defer src="https://kit.fontawesome.com/da8e65aaa6.js" crossorigin="anonymous"></script>
    <script  typpe="text/javascript" defer src="/befacon-transport-database/assets/js/main.js"></script>
</head>
<body>
    <?php include ('components/navbar.php') ?>
    <div class="main-container">
        <?php include ('components/sidebar.php') ?>
        <div class="content-area">
            <main>
                <div class="container">
                    <?php  
                        if (file_exists($content_path)) {
                            include $content_path;
                        } else {
                            echo "<h2>Page not found</h2>";
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
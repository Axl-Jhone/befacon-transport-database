<?php 
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../login.php");
        exit();
    }
    
    require_once '../../includes/db_connect.php'; 
    include '../../includes/modals/modal_shell.php'; 

    $routes = [
        // Ensure 'dashboard.php' here refers to your WIDGET/CONTENT file, 
        // NOT this main layout file (which should be named home.php or index.php)
        'dashboard' => 'dashboard.php', 
        'trips'     => 'trips.php',
        'drivers'   => 'drivers.php',
        'vehicles'  => 'vehicles.php',
        'about' => 'about.php',
        'acc-settings' => 'acc-settings.php'
    ];

    $page_key = $_GET['page'] ?? 'dashboard';

    if (array_key_exists($page_key, $routes)) {
        $content_path = $routes[$page_key];
    } else {
        $content_path = 'error404.php'; 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBeFaCon</title>

    <!-- Styles -->
    <link rel="stylesheet" href="/befacon-transport-database/assets/css/main.css">
    <link rel="stylesheet" href="/befacon-transport-database/assets/css/modal_shell.css">
    <link rel="stylesheet" href="/befacon-transport-database/assets/css/navbar.css">
    <link rel="stylesheet" href="/befacon-transport-database/assets/css/sidebar.css">

    <!-- External Styles and Libraries -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=circle" />
    <script defer src="https://kit.fontawesome.com/da8e65aaa6.js" crossorigin="anonymous"></script>
    
    <!-- Scripts -->
    <script type="text/javascript" defer src="/befacon-transport-database/assets/js/modal.js"></script>
    <script type="text/javascript" defer src="/befacon-transport-database/assets/js/main.js"></script>
</head>
<body>
    <?php include ('../../components/navbar.php') ?>

    <div class="main-container">
        <?php include ('../../components/sidebar.php') ?>

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
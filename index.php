<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFaCon Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="main.js"></script>
    <script src="https://kit.fontawesome.com/da8e65aaa6.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <?php require("db_connect.php"); ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'includes/topbar.php' ?>
        <div class="content-area">
            <?php
                $page = $_GET['page'] ?? 'dashboard'; // show dashboard.php as the home (default) page
                $file = "partials/{$page}.php";
                if (file_exists($file)) {
                    include $file;
                } else {
                    echo "<div class='content'><p>Page not found.</p></div>";
                }
                include 'partials/modal-add-trip.php';
            ?>
        </div>
    </div>
</div>
</body>
</html>
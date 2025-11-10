<?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "befacon";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);

    // Check Connection
    if($connection->connect_error){
        die("Connection failed: " . $connection->connect_error);
    }
?>



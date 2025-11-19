<?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "befacon_transpo_records";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check Connection
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
?>
<?php
require_once '../../includes/db_connect.php'; 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID

    // 1. Prepare SQL
    $sql = "DELETE FROM driver_info WHERE driver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // 2. Execute & Redirect
    if ($stmt->execute()) {
        
        // Auto-detect previous page to preserve pagination/filters
        $previous_page = $_SERVER['HTTP_REFERER'] ?? '../drivers.php'; 
        
        if (strpos($previous_page, '?') !== false) {
            header("Location: " . $previous_page . "&status=deleted");
        } else {
            header("Location: " . $previous_page . "?status=deleted");
        }
        exit();
        
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Invalid Request: No ID provided.";
}
?>
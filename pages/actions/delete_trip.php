<?php
require_once '../../includes/db_connect.php'; 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID

    // 1. Prepare SQL
    $sql = "DELETE FROM trip_info WHERE trip_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // 2. Execute & Redirect
    if ($stmt->execute()) {
        
        // Get the URL of the page that submitted this form
        $previous_page = $_SERVER['HTTP_REFERER'] ?? '../index.php'; // Added a fallback just in case
        
        // Check if it already has query params (like ?page=1) so we append correctly
        if (strpos($previous_page, '?') !== false) {
            // CHANGED 'saved' TO 'deleted'
            header("Location: " . $previous_page . "&status=deleted");
        } else {
            // CHANGED 'saved' TO 'deleted'
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
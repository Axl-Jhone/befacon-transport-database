<?php
require_once '../../includes/db_connect.php'; 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM vehicle_info WHERE vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $previous_page = $_SERVER['HTTP_REFERER'] ?? '../vehicles.php'; 
        
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
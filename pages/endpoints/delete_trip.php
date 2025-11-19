<?php
    require("../../../db_connect.php"); // Adjust path as necessary

    // 1. Check if the trip_id was submitted via POST
    if (isset($_POST["trip_id"]) && $connection) {
        
        // Sanitize and validate the ID
        $trip_id = filter_input(INPUT_POST, 'trip_id', FILTER_VALIDATE_INT);

        if ($trip_id !== false && $trip_id !== null) {
            
            // --- Secure Deletion Logic ---
            $sql = "DELETE FROM trip_info WHERE ID = ?";
            
            $stmt = $connection->prepare($sql);
            
            if ($stmt) {
                // 'i' for integer type
                $stmt->bind_param("i", $trip_id); 
                $stmt->execute();
                $stmt->close();
                
                // Optional: Store success message in a session variable
                // $_SESSION['message'] = "Trip ID $trip_id deleted successfully!";
            } else {
                 error_log("Failed to prepare DELETE statement: " . $connection->error);
            }
        }
    }

    // 2. Redirect back to the trip listing page after deletion
    // Assuming your main trip page is at ../../index.php?page=trips
    header("Location: ../../index.php?page=trips");
    exit;
?>
<?php
require_once '../../includes/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Capture Inputs
    $trip_id     = $_POST['trip_id'] ?? ''; 
    $driver_id   = $_POST['driver_id'];
    $vehicle_id  = $_POST['vehicle_id'];
    $origin      = $_POST['origin'];
    $destination = $_POST['destination'];
    $depart      = $_POST['sched_depart_datetime'];
    $arrival     = $_POST['sched_arrival_datetime'];
    $purpose_id  = $_POST['purpose_id'];
    $status_id   = $_POST['trip_status_id'];
    $cost        = !empty($_POST['trip_cost']) ? $_POST['trip_cost'] : 0.00;

    // 2. Prepare SQL
    if (empty($trip_id)) {
        $sql = "INSERT INTO trip_info (driver_id, vehicle_id, origin, destination, sched_depart_datetime, sched_arrival_datetime, purpose_id, trip_status_id, trip_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssiid", $driver_id, $vehicle_id, $origin, $destination, $depart, $arrival, $purpose_id, $status_id, $cost);
    } else {
        $sql = "UPDATE trip_info SET driver_id=?, vehicle_id=?, origin=?, destination=?, sched_depart_datetime=?, sched_arrival_datetime=?, purpose_id=?, trip_status_id=?, trip_cost=? WHERE trip_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssiidi", $driver_id, $vehicle_id, $origin, $destination, $depart, $arrival, $purpose_id, $status_id, $cost, $trip_id);
    }

    // 3. Execute & Redirect
    if ($stmt->execute()) {
        // --- THE FIX: AUTO-DETECT PREVIOUS PAGE ---
        
        // Get the URL of the page that submitted this form
        $previous_page = $_SERVER['HTTP_REFERER'];
        
        // Check if it already has query params (like ?page=1) so we append correctly
        if (strpos($previous_page, '?') !== false) {
            header("Location: " . $previous_page . "&status=saved");
        } else {
            header("Location: " . $previous_page . "?status=saved");
        }
        exit();
        
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
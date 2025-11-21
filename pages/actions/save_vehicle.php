<?php
require_once '../../includes/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vehicle_id           = $_POST['vehicle_id'] ?? ''; 
    $plate_no             = $_POST['plate_no'];
    $vehicle_type_id      = $_POST['vehicle_type_id'];
    $access_id            = $_POST['access_id'];
    $license_type_id      = $_POST['license_type_id'];
    $vehicle_condition_id = $_POST['vehicle_condition_id'];
    $current_location     = $_POST['current_location'];
    $vehicle_status_id    = $_POST['vehicle_status_id'];

    if (empty($vehicle_id)) {
        // INSERT
        $sql = "INSERT INTO vehicle_info 
                (plate_no, vehicle_type_id, access_id, license_type_id, vehicle_condition_id, current_location, vehicle_status_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        // Types: s i i i i s i
        $stmt->bind_param("siiiisi", $plate_no, $vehicle_type_id, $access_id, $license_type_id, $vehicle_condition_id, $current_location, $vehicle_status_id);
        
    } else {
        // UPDATE
        $sql = "UPDATE vehicle_info SET 
                plate_no=?, vehicle_type_id=?, access_id=?, license_type_id=?, vehicle_condition_id=?, current_location=?, vehicle_status_id=? 
                WHERE vehicle_id=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiiisii", $plate_no, $vehicle_type_id, $access_id, $license_type_id, $vehicle_condition_id, $current_location, $vehicle_status_id, $vehicle_id);
    }

    if ($stmt->execute()) {
        $previous_page = $_SERVER['HTTP_REFERER'];
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
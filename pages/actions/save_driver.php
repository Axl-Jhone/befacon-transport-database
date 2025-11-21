<?php
require_once '../../includes/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Capture Inputs
    $driver_id       = $_POST['driver_id'] ?? ''; // Hidden Field
    
    $driver_fname    = $_POST['driver_fname'];
    $driver_lname    = $_POST['driver_lname'];
    $driver_mi       = $_POST['driver_mi'];
    $contact_no      = $_POST['contact_no'];
    $birthdate       = $_POST['birthdate'];
    $license_no      = $_POST['license_no'];
    $license_type_id = $_POST['license_type_id'];
    $driver_sex      = $_POST['driver_sex'];
    $driver_status_id= $_POST['driver_status_id'];

    // 2. Prepare SQL based on ID
    if (empty($driver_id)) {
        // --- INSERT (ADD NEW) ---
        $sql = "INSERT INTO driver_info 
                (driver_fname, driver_lname, driver_mi, contact_no, birthdate, license_no, license_type_id, driver_sex, driver_status_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        // Types: s=string, i=int
        // Structure: s s s s s s i s i
        $stmt->bind_param("ssssssisi", $driver_fname, $driver_lname, $driver_mi, $contact_no, $birthdate, $license_no, $license_type_id, $driver_sex, $driver_status_id);
        
    } else {
        // --- UPDATE (EDIT EXISTING) ---
        $sql = "UPDATE driver_info SET 
                driver_fname=?, driver_lname=?, driver_mi=?, contact_no=?, birthdate=?, license_no=?, license_type_id=?, driver_sex=?, driver_status_id=? 
                WHERE driver_id=?";
        
        $stmt = $conn->prepare($sql);
        // Structure: s s s s s s i s i + i (id)
        $stmt->bind_param("ssssssisii", $driver_fname, $driver_lname, $driver_mi, $contact_no, $birthdate, $license_no, $license_type_id, $driver_sex, $driver_status_id, $driver_id);
    }

    // 3. Execute & Redirect
    if ($stmt->execute()) {
        
        // Auto-detect previous page to preserve pagination/filters
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
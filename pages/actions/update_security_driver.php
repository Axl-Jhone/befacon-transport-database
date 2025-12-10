<?php
// 1. ENSURE SESSION IS STARTED
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../includes/db_connect.php'; 

$redirect_url = "../driver-view/home.php?page=acc-settings";

// 2. CHECK LOGIN STATUS
if (!isset($_SESSION['driver_id'])) {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "Error: You are not logged in.";
    header("Location: " . $redirect_url);
    exit();
}

// THIS IS THE driver ID (e.g., 1 or 6)
$current_driver_id = $_SESSION['driver_id']; 

try {
    

    // Password
    if (isset($_POST['update_pass'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "New passwords do not match.";
            header("Location: " . $redirect_url);
            exit();
        }

        // CORRECTION 4: Check password using driver_id
        $stmt = $conn->prepare("SELECT passcode FROM user_login WHERE driver_id = ?");
        $stmt->bind_param("i", $current_driver_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || $current_password !== $user['passcode']) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "The current password you entered is incorrect.";
            header("Location: " . $redirect_url);
            exit();
        }

        // CORRECTION 5: Update using driver_id
        $update = $conn->prepare("UPDATE user_login SET passcode = ? WHERE driver_id = ?");
        $update->bind_param("si", $new_password, $current_driver_id);

        if ($update->execute()) {
            $_SESSION['status'] = "success";
            $_SESSION['message'] = "Password changed successfully.";
        } else {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Database error updating password.";
        }
    }

} catch (Exception $e) {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "System Error: " . $e->getMessage();
}

header("Location: " . $redirect_url);
exit();
?>
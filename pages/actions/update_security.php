<?php
// 1. ENSURE SESSION IS STARTED
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../includes/db_connect.php'; 

$redirect_url = "../admin-view/home.php?page=acc-settings";

// 2. CHECK LOGIN STATUS
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "Error: You are not logged in.";
    header("Location: " . $redirect_url);
    exit();
}

// THIS IS THE ADMIN ID (e.g., 1 or 6)
$current_admin_id = $_SESSION['admin_id']; 

try {
    // email
    if (isset($_POST['update_email'])) {
        $new_email = trim($_POST['new_email']);
        $current_password = $_POST['current_password'];

        // CORRECTION 1: Check password using admin_id
        $stmt = $conn->prepare("SELECT passcode FROM user_login WHERE admin_id = ?");
        $stmt->bind_param("i", $current_admin_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || $current_password !== $user['current_password']) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Incorrect password. Email was not updated.";
            header("Location: " . $redirect_url);
            exit();
        }

        // CORRECTION 2: Check if email taken (Exclude current admin)
        $check = $conn->prepare("SELECT user_id FROM user_login WHERE email = ? AND admin_id != ?");
        $check->bind_param("si", $new_email, $current_admin_id);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "This email is already taken.";
            header("Location: " . $redirect_url);
            exit();
        }

        // CORRECTION 3: Update using admin_id
        $update = $conn->prepare("UPDATE user_login SET email = ? WHERE admin_id = ?");
        $update->bind_param("si", $new_email, $current_admin_id);
        
        if ($update->execute()) {
            $_SESSION['status'] = "success";
            $_SESSION['message'] = "Email updated successfully.";
        } else {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Database error updating email.";
        }
    }

    // Password
    elseif (isset($_POST['update_pass'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "New passwords do not match.";
            header("Location: " . $redirect_url);
            exit();
        }

        // CORRECTION 4: Check password using admin_id
        $stmt = $conn->prepare("SELECT passcode FROM user_login WHERE admin_id = ?");
        $stmt->bind_param("i", $current_admin_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || $current_password !== $user['passcode']) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "The Current Password you entered is incorrect.";
            header("Location: " . $redirect_url);
            exit();
        }

        // CORRECTION 5: Update using admin_id
        $update = $conn->prepare("UPDATE user_login SET passcode = ? WHERE admin_id = ?");
        $update->bind_param("si", $new_password, $current_admin_id);

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
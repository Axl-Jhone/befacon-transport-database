<?php
    $currentUser_id = $_SESSION['driver_id'];

    $sql = "SELECT d.*, u.email, u.passcode 
        FROM driver_info d 
        JOIN user_login u ON d.driver_id = u.admin_id 
        WHERE d.driver_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $currentUser_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $lname = $row['driver_fname'];
        $fname = $row['driver_fname'];
        $mi    = $row['driver_mi'];
        $bday  = $row['birthdate'];
        $sex   = $row['driver_sex'];
        $email = $row['email'];
        $pass = $row['passcode'];
    } else {
        echo "Driver not found.";
        exit();
    }
?>

<div class="account-settings-wrapper">
    <div class="left-column">
        <h1 class="page-title">Account Settings</h1>

        <div class="settings-content">
            <div class="section-header">Personal Information</div>
            <div class="user-info-grid">
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" value=<?php echo htmlspecialchars($lname)?> readonly>
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" value=<?php echo htmlspecialchars($fname) ?> readonly>
                </div>
                <div class="form-group">
                    <label>Middle Initial</label>
                    <input type="text" value="<?php echo !empty($mi) ? htmlspecialchars($mi) . '.' : ''; ?>" placeholder="N/A" readonly>
                </div>
                <div class="form-group">
                    <label>Birthday</label>
                    <input type="text" value=<?php echo htmlspecialchars($bday) ?> readonly>
                </div>
                <div class="form-group">
                    <label>Sex</label>
                    <input type="text" value=<?php echo htmlspecialchars($sex) ?> readonly>
                </div>
            </div>

            <hr>

            <div class="section-header">Account Security</div>
            
            <div class="form-group full-width">
                <label>Email Address</label>
                <div class="input-group">
                    <input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                    
                    <button type="button" 
                            class="btn-edit" 
                            onclick="openModal('email-security-template', 'Change Email Address')">
                        Change Email
                    </button>
                </div>
            </div>

            <div class="form-group full-width">
                <label>Password</label>
                <div class="input-group">
                    <input type="password" value="********" readonly> 
                    
                    <button type="button" 
                            class="btn-edit" 
                            onclick="openModal('password-security-template', 'Change Password')">
                        Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="right-column">
        <div class="settings-logo">
            <img src="../../assets/img/navbar/logo-nobg.png" alt="logo">
        </div>
    </div>
</div>

<!-- MODAL SHELL -->
<div id="universal-modal" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modal-title">Title</h3>
            <button onclick="closeModal()" class="close-btn">&times;</button>
        </div>
        <div id="modal-body"></div>
    </div>
</div>

<template id="email-security-template">
    <form action="../actions/update_security.php" method="POST" onsubmit="validatePasswordForm(event)">
        <div class="form-group modal-input">
            <label>New Email Address</label>
            <input type="email" name="new_email" required placeholder="Enter new email" class="modal-input">
        </div>
        <div class="form-group modal-input">
            <label>Current Password (for verification)</label>
            <input type="password" name="current_password" required placeholder="Confirm your password" class="modal-input">
        </div>
        <div class="modal-actions">
            <button type="submit" name="update_email" class="btn-primary">Update Email</button>
        </div>
    </form>
</template>

<template id="password-security-template">
    <form action="../actions/update_security.php" method="POST" >
        <div class="form-group modal-input">
            <label>Current Password</label>
            <input type="password" name="current_password" required placeholder="Enter old password" class="modal-input">
        </div>
        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
        <div class="form-group modal-input">
            <label>New Password</label>
            <input type="password" name="new_password" required placeholder="Enter new password" class="modal-input">
        </div>
        <div class="form-group modal-input">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required placeholder="Retype new password" class="modal-input">
        </div>
        <div class="modal-actions">
            <button type="submit" name="update_pass" class="btn-primary">Change Password</button>
        </div>
    </form>
</template>

<?php if (isset($_SESSION['message'])): ?>
    <div style="
        padding: 15px; 
        margin: 20px auto; 
        width: 90%;
        max-width: 800px;
        border-radius: 5px; 
        font-family: sans-serif;
        font-weight: bold;
        text-align: center;
        background-color: <?php echo $_SESSION['status'] == 'success' ? '#d4edda' : '#f8d7da'; ?>; 
        color: <?php echo $_SESSION['status'] == 'success' ? '#155724' : '#721c24'; ?>; 
        border: 1px solid <?php echo $_SESSION['status'] == 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
        
        <?php echo $_SESSION['message']; ?>
        
    </div>
    
    <?php unset($_SESSION['status'], $_SESSION['message']); ?>
<?php endif; ?>
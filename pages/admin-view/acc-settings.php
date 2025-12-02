<?php
    $currentUser_id = $_SESSION['admin_id'];
    $triggerModal = false;
    $msgContent = '';
    $statusClass = '';

    $sql = "SELECT a.*, u.email, u.passcode 
        FROM admin_info a 
        JOIN user_login u ON a.admin_id = u.admin_id 
        WHERE a.admin_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $currentUser_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $lname = $row['admin_lname'];
        $fname = $row['admin_fname'];
        $mi    = $row['admin_mi'];
        $bday  = $row['birthdate'];
        $sex   = $row['admin_sex'];
        $email = $row['email'];
        $pass = $row['passcode'];
    } else {
        echo "Admin not found.";
        exit();
    }

    if (isset($_SESSION['message'])) {
        $triggerModal = true;
        $msgContent = $_SESSION['message'];
        
        if ($_SESSION['status'] == 'success') {
            $statusClass = 'status-success';
        } else {
            $statusClass = 'status-error';
        }
        
        // Clear session so it doesn't pop up again on refresh
        unset($_SESSION['status'], $_SESSION['message']);
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
                    <div class="password-wrapper">
                        <input type="password" value="<?php echo htmlspecialchars($pass); ?>" readonly>       
                        <img src="../../assets/img/login_page/closed.png" 
                            class="password-toggle-icon" 
                            onclick="toggleModalPassword(this)" 
                            alt="toggle password">
                    </div>
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
            <label>Current Password</label>
            <div class="password-modal">
                <input type="password" name="confirm_password" required class="modal-input">
                <img src="../../assets/img/login_page/closed.png" 
                     class="password-toggle-icon" 
                     onclick="toggleModalPassword(this)"
                     alt="toggle password">
            </div>
        </div>
        <div class="modal-actions">
            <button type="submit" name="update_email" class="btn-primary">Update Email</button>
        </div>
    </form>
</template>

<template id="password-security-template">
    <form action="../actions/update_security.php" method="POST">
        
        <div class="form-group modal-input">
            <label>Current Password</label>
            <div class="password-modal">
                <input type="password" name="current_password" required class="modal-input">
                <img src="../../assets/img/login_page/closed.png" 
                     class="password-toggle-icon" 
                     onclick="toggleModalPassword(this)" 
                     alt="toggle password">
            </div>
        </div>

        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

        <div class="form-group modal-input">
            <label>New Password</label>
            <div class="password-modal">
                <input type="password" name="new_password" required class="modal-input">
                <img src="../../assets/img/login_page/closed.png" 
                     class="password-toggle-icon" 
                     onclick="toggleModalPassword(this)"
                     alt="toggle password">
            </div>
        </div>

        <div class="form-group modal-input">
            <label>Confirm New Password</label>
            <div class="password-modal">
                <input type="password" name="confirm_password" required class="modal-input">
                <img src="../../assets/img/login_page/closed.png" 
                     class="password-toggle-icon" 
                     onclick="toggleModalPassword(this)"
                     alt="toggle password">
            </div>
        </div>

        <div class="modal-actions">
            <button type="submit" name="update_pass" class="btn-primary">Change Password</button>
        </div>
    </form>
</template>

<template id="status-message-template">
    <div class="status-modal-content">
        <div class="status-message-box <?php echo $triggerModal ? $statusClass : ''; ?>">
            <?php echo $triggerModal ? $msgContent : ''; ?>
        </div>
    </div>
</template>

<?php if ($triggerModal): ?>
    <div id="server-message-data" 
         style="display:none;" 
         data-status="<?php echo $statusClass; ?>" 
         data-message="<?php echo htmlspecialchars($msgContent); ?>">
    </div>
<?php endif; ?>



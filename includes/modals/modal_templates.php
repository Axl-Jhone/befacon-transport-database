<template id="view-details-template">
    <div class="trip-details-grid" style="padding: 20px; display: grid; gap: 15px;">
        
        <div class="detail-section" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Driver Information</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Name:</span>
                <span class="value" data-key="driverName"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Contact:</span>
                <span class="value" data-key="contact"></span>
            </div>
        </div>

        <div class="detail-section" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Vehicle Information</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Type:</span>
                <span class="value" data-key="vehicleType"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Plate No:</span>
                <span class="value" data-key="plateNo"></span>
            </div>
        </div>

        <div class="detail-section full-width" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Trip Schedule</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Route:</span>
                <span class="value" data-key="route"></span>
            </div>
            
            <div class="schedule-grid" style="display: flex; gap: 20px; margin-top: 10px;">
                <div>
                    <span class="label small-label" style="font-size: 0.85em; color: #888;">Departure:</span><br>
                    <strong class="value" data-key="departure"></strong>
                </div>
                <div>
                    <span class="label small-label" style="font-size: 0.85em; color: #888;">Arrival:</span><br>
                    <strong class="value" data-key="arrival"></strong>
                </div>
            </div>
        </div>

        <div class="detail-section full-width financial-section">
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Status:</span>
                <span class="value badge" data-key="status"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Purpose:</span>
                <span class="value" data-key="purpose"></span>
            </div>
            <div class="detail-row cost-row" style="margin-top: 10px; font-size: 1.1em;">
                <span class="label" style="font-weight:bold; color:#666;">Total Cost:</span>
                <span class="value cost-highlight" data-key="totalCost" style="color: #28a745; font-weight: bold;"></span>
            </div>
        </div>

    </div>

    <div class="modal-footer" style="padding: 15px; text-align: right; border-top: 1px solid #eee;">
        <button type="button" class="cancel-btn" onclick="closeModal()">Close</button>
    </div>
</template>

<template id="delete-template">
    <div class="delete-confirmation" style="text-align: center; padding: 20px;">
        <svg class="warning-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="50" height="50" style="color: #dc3545; margin-bottom: 15px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        
        <h3 style="margin-top: 0;">Confirm Deletion</h3>
        <p class="warning-text" style="color: #666; margin-bottom: 20px;">Are you sure you want to delete this trip?<br>This action cannot be undone.</p>
        
        <div class="delete-actions" style="display: flex; justify-content: center; gap: 10px;">
            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            <a id="confirm-delete-btn" href="#" class="btn-danger" style="padding: 8px 16px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px;">Yes, Delete</a>
        </div>
    </div>
</template>

<template id="trip-form-template">
    <form action="actions/save_trip.php" method="POST" class="modal-form-grid" onsubmit="return validateDates(event)">
        
        <input type="hidden" name="trip_id" data-key="tripId">

        <div class="form-group">
            <label>Driver</label>
            <select name="driver_id" data-key="driverId" required>
                <option value="" disabled selected>-- Select Driver --</option>
                <?php 
                    // Use the result sets from trips.php
                    if(isset($drivers_result)) {
                        $drivers_result->data_seek(0); 
                        while($d = $drivers_result->fetch_assoc()): 
                ?>
                    <option value="<?php echo $d['driver_id']; ?>">
                        <?php echo $d['full_name']; ?>
                    </option>
                <?php endwhile; } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Vehicle
<!-- View Details -->
<template id="view-details-template">
    <div class="trip-details-grid">
        
        <div class="detail-section">
            <h4>Driver Information</h4>
            <div class="detail-row">
                <span class="label">Name:</span>
                <span class="value" data-key="driverName"></span>
            </div>
            <div class="detail-row">
                <span class="label">Contact:</span>
                <span class="value" data-key="contact"></span>
            </div>
        </div>

        <div class="detail-section">
            <h4>Vehicle Information</h4>
            <div class="detail-row">
                <span class="label">Type:</span>
                <span class="value" data-key="vehicleType"></span>
            </div>
            <div class="detail-row">
                <span class="label">Plate No:</span>
                <span class="value" data-key="plateNo"></span>
            </div>
        </div>

        <div class="detail-section full-width">
            <h4>Trip Schedule</h4>
            <div class="detail-row">
                <span class="label">Route:</span>
                <span class="value" data-key="route"></span>
            </div>
            
            <div class="schedule-grid">
                <div>
                    <span class="label small-label">Departure:</span>
                    <strong class="value" data-key="departure"></strong>
                </div>
                <div>
                    <span class="label small-label">Arrival:</span>
                    <strong class="value" data-key="arrival"></strong>
                </div>
            </div>
        </div>

        <div class="detail-section full-width financial-section">
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value badge" data-key="status"></span>
            </div>
            <div class="detail-row">
                <span class="label">Purpose:</span>
                <span class="value" data-key="purpose"></span>
            </div>
            <div class="detail-row cost-row">
                <span class="label">Total Cost:</span>
                <span class="value cost-highlight" data-key="totalCost"></span>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button onclick="closeModal()" class="btn-secondary">Close</button>
    </div>
</template>

<!-- Delete Trip -->
<template id="delete-template">
    <div class="delete-warning-box">
        <svg class="warning-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        
        <h3>Confirm Deletion</h3>
        <p class="warning-text">Are you sure you want to delete this record?<br>This action cannot be undone.</p>
        
        <div class="delete-actions">
            <button onclick="closeModal()" class="btn-secondary">Cancel</button>
            <a href="#" id="confirm-delete-btn" class="btn-danger">Yes, Delete</a>
        </div>
    </div>
</template>

<!-- Edit Trip -->
<template id="trip-form-template">
    <form action="actions/save_trip.php" method="POST" class="styled-form">
        <input type="hidden" name="trip_id" data-key="tripId">
        
        <div class="form-group">
            <label>Destination / Route</label>
            <input type="text" name="destination" data-key="destination" required placeholder="e.g. Manila to Baguio">
        </div>
        
        <div class="form-row-grid">
            <div class="form-group">
                <label>Departure Date</label>
                <input type="datetime-local" name="sched_date" data-key="departure" required>
            </div>
            <div class="form-group">
                <label>Estimated Cost</label>
                <input type="number" step="0.01" name="trip_cost" data-key="totalCostRaw" placeholder="0.00">
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" onclick="closeModal()" class="btn-secondary btn-spacer">Cancel</button>
            <button type="submit" class="btn-save">Save Trip</button>
        </div>
    </form>
</template>
<?php
/**
 * Generates all reusable HTML <template> tags for various modal views.
 */
function generateContentTemplates() {
    $output = '
        <template id="trip-view-template">
            <input type="hidden" id="modalTripIdHidden" value="">
            <div class="trip-details-grid">
                
                <div class="detail-item">
                    <p class="detail-label">Driver Name</p>
                    <p id="modalDriver" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Driver Contact No.</p>
                    <p id="modalContact" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Vehicle Type</p>
                    <p id="modalVehicleType" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Plate Number</p>
                    <p id="modalPlateNo" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Status</p>
                    <p id="modalStatus" class="detail-value"></p>
                </div>
                
                <div class="detail-item">
                    <p class="detail-label">Purpose</p>
                    <p id="modalPurpose" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Total Cost</p>
                    <p id="modalTotalCost" class="detail-value"></p>
                </div>

                <div class="detail-item full-width-item">
                    <p class="detail-label">Origin/Destination</p>
                    <p id="modalRoute" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Scheduled Departure</p>
                    <p id="modalDeparture" class="detail-value"></p>
                </div>
                
                <div class="detail-item">
                    <p class="detail-label">Arrival Time</p>
                    <p id="modalArrival" class="detail-value"></p>
                </div>
            </div>

            <div class="modal-footer">
                <button class="close-btn-footer" onclick="closeModal(\'universalModal\')">Close</button>
            </div>
        </template>
        
        <template id="vehicle-view-template">
            <input type="hidden" id="modalVehicleIdHidden" value="">
            <div class="vehicle-details-grid">
                
                <div class="detail-item">
                    <p class="detail-label">Model</p>
                    <p id="modalVehicleModel" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Year/Color</p>
                    <p id="modalVehicleYearColor" class="detail-value"></p>
                </div>

                <div class="detail-item">
                    <p class="detail-label">Owner</p>
                    <p id="modalVehicleOwner" class="detail-value"></p>
                </div>
                
                <div class="detail-item">
                    <p class="detail-label">Registration Status</p>
                    <p id="modalVehicleRegStatus" class="detail-value"></p>
                </div>

                <div class="detail-item full-width-item">
                    <p class="detail-label">Last Maintenance Date</p>
                    <p id="modalVehicleLastMaint" class="detail-value"></p>
                </div>
            </div>
        </template>
    ';
    echo $output;

    $output = '
        <template id="confirm-delete-template">
            
            <form id="deleteActionForm" method="POST" action="pages/endpoints/delete_trip.php">
                <input type="hidden" name="trip_id" id="deleteTripIdHidden" value="">
            </form>

            <div class="confirm-message-container">
                <h4 class="confirm-title">Are you sure you want to delete this trip?</h4>
                <p>This action is irreversible and will permanently remove Trip ID: <strong id="deleteTripIdDisplay"></strong> from the database.</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="close-btn-footer" onclick="closeModal(\'universalModal\')">Cancel</button>
                <button type="button" class="delete-btn-action" onclick="proceedWithSimpleDeletion()">Delete</button>
            </div>
        </template>
    ';
    echo $output;
}
?>
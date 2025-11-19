/**
 * Handles the opening sequence with CSS transitions.
 * @param {string} modalId - The ID of the modal backdrop ('universalModal').
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalInner = document.getElementById('universalModalInner'); 

    // 1. Make the modal available for transition (Equivalent to removing 'hidden')
    modal.classList.remove('modal-hidden');

    // 2. Timeout to trigger the CSS transition
    setTimeout(() => {
        modal.classList.add('is-active'); // Trigger fade-in
        if (modalInner) {
            modalInner.classList.add('is-active'); // Trigger scale-in
        }
    }, 10); 
    
    // Add event listener to close when clicking outside the content
    modal.addEventListener('click', handleBackdropClick);
}

/**
 * Handles the closing sequence with CSS transitions.
 * @param {string} modalId - The ID of the modal backdrop ('universalModal').
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalInner = document.getElementById('universalModalInner');

    // 1. Remove the active classes to trigger the fade/scale-out transition
    modal.classList.remove('is-active');
    if (modalInner) {
        modalInner.classList.remove('is-active');
    }
    
    // Remove backdrop click listener
    modal.removeEventListener('click', handleBackdropClick);

    // 2. Wait for the transition (300ms in CSS) before hiding the element entirely
    setTimeout(() => {
        modal.classList.add('modal-hidden'); // Equivalent to adding 'hidden' class
        
        // Optional cleanup
        document.getElementById('modalContentArea').innerHTML = ''; 
        document.getElementById('universalModalTitle').innerText = '';
        
    }, 300); 
}

// Function to handle clicking outside the modal content
function handleBackdropClick(event) {
    if (event.target.id === 'universalModal') { 
        closeModal('universalModal');
    }
}


// --- Data Population Functions ---

function populateTripView(data) {
    if (document.getElementById('modalTripIdHidden')) { document.getElementById('modalTripIdHidden').value = data.tripId; }
    document.getElementById('modalDriver').innerText = data.driverName;
    document.getElementById('modalContact').innerText = data.contact;
    document.getElementById('modalVehicleType').innerText = data.vehicleType;
    document.getElementById('modalPlateNo').innerText = data.plateNo;
    document.getElementById('modalStatus').innerText = data.status;
    document.getElementById('modalPurpose').innerText = data.purpose;
    document.getElementById('modalTotalCost').innerText = data.totalCost;
    document.getElementById('modalRoute').innerText = data.route;
    document.getElementById('modalDeparture').innerText = data.departure;
    document.getElementById('modalArrival').innerText = data.arrival;
}

function populateVehicleView(data) {
    if (document.getElementById('modalVehicleIdHidden')) { document.getElementById('modalVehicleIdHidden').value = data.vehicleId; }
    document.getElementById('modalVehicleModel').innerText = data.model;
    document.getElementById('modalVehicleYearColor').innerText = `${data.year} / ${data.color}`;
    document.getElementById('modalVehicleOwner').innerText = data.ownerName;
    document.getElementById('modalVehicleRegStatus').innerText = data.registrationStatus;
    document.getElementById('modalVehicleLastMaint').innerText = data.lastMaintenanceDate;
}


/**
 * The main function to control the modal content and display.
 * @param {string} templateId - The ID of the template to load.
 * @param {string} title - The title to display in the modal header.
 * @param {object} data - Optional: The data object needed to populate the fields.
 */
function openUniversalModal(templateId, title, data = null) {
    const contentArea = document.getElementById('modalContentArea');
    const template = document.getElementById(templateId);

    // 1. Set the Title
    document.getElementById('universalModalTitle').innerText = title;

    // 2. Inject the New Content
    if (template) {
        const templateContent = template.content.cloneNode(true); 
        contentArea.innerHTML = '';
        contentArea.appendChild(templateContent);

        // 3. Populate Data based on the templateId
        if (templateId === 'trip-view-template' && data) {
            populateTripView(data);
        } else if (templateId === 'vehicle-view-template' && data) {
            populateVehicleView(data);
        } 
        // Add more template population logic here
    }
    
    // 4. Show the Modal with transition
    openModal('universalModal');
}

/**
 * 1. Opens the universal modal and populates the delete template
 * with the specific ID of the trip to be deleted.
 * * @param {number} tripId The ID of the trip row to be deleted.
 */
function openDeleteModal(tripId) {
    // 1. Get the template content
    const template = document.getElementById('confirm-delete-template');
    const content = template.content.cloneNode(true);

    // 2. Locate the dynamic elements within the cloned content
    const hiddenInput = content.getElementById('deleteTripIdHidden');
    const displayElement = content.getElementById('deleteTripIdDisplay');

    // 3. Update the elements with the correct trip ID
    if (hiddenInput) {
        hiddenInput.value = tripId;
    }
    if (displayElement) {
        displayElement.textContent = tripId;
    }

    // 4. Assuming you have a function to populate and display your modal
    // (You will need to implement or adapt this function)
    if (typeof showModal === 'function') {
        showModal('universalModal', 'Delete Confirmation', content);
    } else {
        console.error("showModal function is not defined.");
    }
}

/**
 * 2. Submits the hidden form when the user confirms deletion.
 */
function proceedWithSimpleDeletion() {
    // 1. Get the form element by its ID
    const form = document.getElementById('deleteActionForm');
    
    if (form) {
        // 2. Submit the form
        // This will trigger a POST request to 'pages/endpoints/delete_trip.php'
        form.submit();
        
        // 3. Optional: Close the modal immediately after submission
        if (typeof closeModal === 'function') {
            closeModal('universalModal');
        }
    } else {
        console.error("Delete form element not found.");
    }
}


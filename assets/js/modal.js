
// 1. Handle "Vehicle Type" -> "Plate Number" filtering
function filterPlates() {
    const typeSelect = document.getElementById('type-select');
    const plateSelect = document.getElementById('plate-select');
    if (!typeSelect || !plateSelect) return;

    const selectedTypeId = typeSelect.value;

    // If the user manually changes the type, reset the plate
    if (window.event && window.event.type === 'change') {
        plateSelect.value = ""; 
    }

    if (!selectedTypeId) {
        plateSelect.disabled = true;
        return;
    }

    plateSelect.disabled = false;
    
    // Show only plates that match the selected type ID
    Array.from(plateSelect.options).forEach(option => {
        if (option.value === "") return; 
        const optionTypeId = option.getAttribute('data-type-id');
        option.style.display = (optionTypeId === selectedTypeId) ? 'block' : 'none';
    });
}

// 2. Prevent "Arrival" being before "Departure"
function validateDates(event) {
    const form = event.target;
    const d = new Date(form.querySelector('[name="sched_depart_datetime"]').value);
    const a = new Date(form.querySelector('[name="sched_arrival_datetime"]').value);
    
    if (d >= a) {
        alert("Error: Arrival time must be AFTER Departure time.");
        event.preventDefault();
        return false;
    }
    return true;
}

// 3. Universal Modal Opener
function openModal(templateId, title, data = null, deleteUrl = null) {
    const modal = document.getElementById('universal-modal');
    const modalBody = document.getElementById('modal-body');
    const template = document.getElementById(templateId);

    if (!modal || !template) return;

    // Set Title
    document.getElementById('modal-title').textContent = title;
    
    // Clear previous content and Clone new template
    modalBody.innerHTML = '';
    const content = template.content.cloneNode(true);

    // If it's a DELETE modal, update the Yes button
    if (deleteUrl) {
        const btn = content.getElementById('confirm-delete-btn');
        if (btn) btn.href = deleteUrl;
    }

    // Inject content so we can access elements
    modalBody.appendChild(content);

    // If data is provided (Edit or View), populate fields
    if (data) {
        const elements = modalBody.querySelectorAll('[data-key]');
        elements.forEach(el => {
            const key = el.getAttribute('data-key');
            if (data[key] !== undefined) {
                // Inputs/Selects
                if (['INPUT', 'SELECT', 'TEXTAREA'].includes(el.tagName)) {
                    el.value = data[key];
                } 
                // Text Elements (Spans, Divs)
                else {
                    el.textContent = data[key];
                    // Add color to status badge if specific class exists
                    if(key === 'status' && el.classList.contains('badge')) {
                        setStatusColor(el, data[key]);
                    }
                }
            }
        });

        // SPECIAL CASE: Trigger dependent dropdown for "Edit Mode"
        const typeSelect = modalBody.querySelector('#type-select');
        const plateSelect = modalBody.querySelector('#plate-select');
        if (typeSelect && plateSelect && data.vehicleTypeId) {
            filterPlates(); // Unhide correct options
            plateSelect.value = data.vehicleId; // Select the correct plate
        }
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('universal-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function setStatusColor(element, status) {
    status = status.toLowerCase().trim();
    // Remove old color classes
    element.classList.remove('badge-success', 'badge-warning', 'badge-danger');
    
    if (status === 'completed' || status === 'active') {
        element.classList.add('badge-success'); // Green
    } else if (status === 'pending' || status === 'scheduled') {
        element.classList.add('badge-warning'); // Yellow/Orange
    } else {
        element.classList.add('badge-danger');  // Red
    }
}

document.addEventListener("DOMContentLoaded", function() {
    // 1. Try to find the hidden data div
    const msgData = document.getElementById('server-message-data');

    // 2. ONLY run if the div exists AND has a message
    if (msgData) {
        const messageText = msgData.getAttribute('data-message');
        const statusClass = msgData.getAttribute('data-status');

        // Extra safety: Check if text is not empty/null
        if (messageText && messageText.trim() !== "") {
            
            // 3. Open the modal
            if (typeof openModal === 'function') {
                openModal('status-message-template', 'System Notification');
            }

            // 4. Inject the text and color
            const modalBody = document.getElementById('modal-body');
            const statusBox = modalBody.querySelector('.status-message-box');

            if (statusBox) {
                statusBox.classList.add(statusClass);
                statusBox.textContent = messageText;
            }
        }
    }
});
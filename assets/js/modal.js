/**
 * Universal Modal Logic
 * Handles View, Add, Edit, and Delete actions using HTML <template> tags.
 */

// Main function to open the modal
function openModal(templateId, title, data = null, deleteUrl = null) {
    const modal = document.getElementById('universal-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    const template = document.getElementById(templateId);

    // Safety check
    if (!modal || !template) {
        console.error(`Error: Modal or Template ('${templateId}') not found.`);
        return;
    }

    // 1. Set the Title
    if (modalTitle) modalTitle.textContent = title;

    // 2. Clone the Template Content
    // We clone it so we don't modify the original template
    const content = template.content.cloneNode(true);

    // 3. HANDLE DELETE LINKS
    // If a deleteUrl is passed, find the "Yes, Delete" button and set its href
    if (deleteUrl) {
        const deleteBtn = content.getElementById('confirm-delete-btn');
        if (deleteBtn) {
            deleteBtn.href = deleteUrl;
        }
    }

    // 4. HANDLE DATA POPULATION (View Details or Edit Forms)
    if (data) {
        // Loop through every element inside the template that has a "data-key" attribute
        // Example: <span data-key="driverName"></span> or <input data-key="tripId">
        const elements = content.querySelectorAll('[data-key]');
        
        elements.forEach(element => {
            const key = element.getAttribute('data-key');
            
            // Check if our data object actually has this key
            if (data[key] !== undefined && data[key] !== null) {
                
                // CASE A: It's an Input, Select, or Textarea (For Forms)
                if (['INPUT', 'SELECT', 'TEXTAREA'].includes(element.tagName)) {
                    element.value = data[key];
                } 
                // CASE B: It's a standard text element (For View Details)
                else {
                    element.textContent = data[key];
                    
                    // Optional: Add color styling to Status badges
                    if (key === 'status') {
                        setStatusColor(element, data[key]);
                    }
                }
            }
        });
    }

    // 5. Inject Content and Show Modal
    modalBody.innerHTML = ''; // Clear previous content
    modalBody.appendChild(content); // Add new content
    modal.classList.remove('hidden'); // Show modal
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

// Function to Close Modal
function closeModal() {
    const modal = document.getElementById('universal-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
}

// Helper: Colorize Status Badges (Optional)
function setStatusColor(element, status) {
    const s = status.toLowerCase().trim();
    element.className = 'value badge'; // Reset classes
    
    if (s === 'completed' || s === 'active') {
        element.classList.add('badge-success');
    } else if (s === 'pending' || s === 'scheduled') {
        element.classList.add('badge-warning');
    } else if (s === 'cancelled' || s === 'inactive') {
        element.classList.add('badge-danger');
    } else {
        element.classList.add('badge-secondary');
    }
}

// Close Modal when clicking the dark overlay (outside the box)
window.addEventListener('click', function(event) {
    const modal = document.getElementById('universal-modal');
    if (event.target === modal) {
        closeModal();
    }
});

// Close Modal on 'Escape' key press
window.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
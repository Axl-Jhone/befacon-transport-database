const sidebar = document.getElementById('sidebar');
const toggleButton = document.getElementById('toggle-btn');

function toggleSideBar() {
    sidebar.classList.toggle('close');

    if (sidebar.classList.contains('close')) {
        Array.from(sidebar.getElementsByClassName('show')).forEach(ul => {
            ul.classList.remove('show');
        });

        Array.from(sidebar.getElementsByClassName('rotate')).forEach(btn => {
            btn.classList.remove('rotate');
        });
    }
}

function toggleSubMenu(button) {
    button.nextElementSibling.classList.toggle('show');
    button.classList.toggle('rotate');
    
    if (sidebar.classList.contains('close')) {
        sidebar.classList.toggle('close');
    }
}

document.addEventListener("DOMContentLoaded", () => {

    const settingsBtn = document.getElementById("settingsMenuBtn");
    const settingsDropdown = document.getElementById("settingsDropdown");

    if (settingsBtn) {
        settingsBtn.addEventListener("click", (event) => {
            event.stopPropagation();
            settingsDropdown.style.display =
                settingsDropdown.style.display === "block" ? "none" : "block";
        });
    }

    document.addEventListener("click", () => {
        if (settingsDropdown) settingsDropdown.style.display = "none";
    });
});

document.addEventListener("DOMContentLoaded", () => {

    // SELECT ALL CELLS — You can change this selector if needed.
    document.querySelectorAll('.table-display td').forEach(cell => {

        // Detect if ellipsis is applied
        if (cell.scrollWidth > cell.clientWidth) {
            cell.setAttribute('title', cell.textContent.trim());
        }
    });

});

function validatePasswordForm(event) {
    const form = event.target;
    // Get values
    const newPass = form.querySelector('[name="new_password"]').value;
    const confirmPass = form.querySelector('[name="confirm_password"]').value;

    // Check match
    if (newPass !== confirmPass) {
        alert("Error: New passwords do not match!");
        event.preventDefault(); // Stop submission
        return false;
    }
    return true;
}

// Place this in your main.js file
function toggleModalPassword(imgElement) {
    // 1. Find the parent wrapper of the icon we clicked
    const wrapper = imgElement.parentElement;
    
    // 2. Find the input field inside that same wrapper
    const passwordField = wrapper.querySelector('input');

    // 3. Reuse your logic to swap Type and Image Source
    if (passwordField.type === "password") {
        passwordField.type = "text";
        // NOTE: Check if you need '../' or '../../' depending on your folder depth
        imgElement.src = "../../assets/img/login_page/open.png"; 
    } else {
        passwordField.type = "password";
        imgElement.src = "../../assets/img/login_page/closed.png";
    }
}
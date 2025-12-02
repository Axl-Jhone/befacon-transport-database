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

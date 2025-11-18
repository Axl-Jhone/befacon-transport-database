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
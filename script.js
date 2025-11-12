// Only run this code if we're on the login.php page
if (document.body.classList.contains('login-page')) {

    const loginBtn = document.getElementById('login-btn');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    function login() {
        const username = usernameInput.value;
        const password = passwordInput.value;

        // Replace this with real authentication later
        if (username === "admin" && password === "admin") {
            // Redirect to your PHP page after successful login
            window.location.href = "http://localhost/befacon-transport-database/index.php";
        } else {
            alert("Incorrect username or password!");
        }
    }

    loginBtn.addEventListener('click', login);

    // Optional: allow pressing Enter key to login
    [usernameInput, passwordInput].forEach(input => {
        input.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                login();
            }
        });
    });
}

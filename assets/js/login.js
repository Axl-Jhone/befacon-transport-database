document.addEventListener("DOMContentLoaded", function() {
    const passwordField = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    eyeIcon.addEventListener("click", function() {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.src = "../assets/img/login_page/open.png";
        } else {
            passwordField.type = "password";
            eyeIcon.src = "../assets/img/login_page/closed.png";
        }
    });
});

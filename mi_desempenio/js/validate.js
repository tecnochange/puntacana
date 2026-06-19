document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let isValid = true;

    document.querySelectorAll(".form-control").forEach(input => {
        let errorMessage = input.nextElementSibling;
        if (input.value.trim() === "") {
            errorMessage.style.display = "block";
            input.classList.add("error");
            isValid = false;
        } else {
            errorMessage.style.display = "none";
            input.classList.remove("error");
        }
    });

});

function togglePassword() {
    var passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
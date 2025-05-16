document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("login-form");
    const email = document.getElementById("login-email");
    const password = document.getElementById("login-password");

    function showError(input, message) {
        let errorSpan = input.nextElementSibling;
        if (!errorSpan || !errorSpan.classList.contains("error")) {
            errorSpan = document.createElement("span");
            errorSpan.classList.add("error");
            input.parentNode.appendChild(errorSpan);
        }
        errorSpan.textContent = message;
        input.style.border = "2px solid #dc3545";
        input.style.backgroundColor = "#f8d7da";
    }

    function showSuccess(input) {
        let errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains("error")) {
            errorSpan.textContent = "";
        }
        input.style.border = "2px solid #28a745";
        input.style.backgroundColor = "#d4edda";
    }

    function validateEmail() {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value)) {
            showError(email, "Invalid email format");
            return false;
        }
        showSuccess(email);
        return true;
    }

    function validatePassword() {
        const passwordPattern = /^(?!.*\s).{6,}$/;  // Checks for at least 6 characters, no spaces
        if (!passwordPattern.test(password.value)) {
            showError(password, "Password must be at least 6 characters and contain no spaces");
            return false;
        }
        showSuccess(password);
        return true;
    }

    email.addEventListener("blur", validateEmail);
    password.addEventListener("blur", validatePassword);

    form.addEventListener("submit", function (event) {
        // Only prevent form submission if validation fails
        if (!validateEmail() || !validatePassword()) {
            event.preventDefault();  // Prevent form submission only if validation fails
        }
        // Otherwise, let the form submit to the server
    });
});
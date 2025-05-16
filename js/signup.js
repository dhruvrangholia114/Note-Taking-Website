document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const email = document.getElementById("email");
    const screenname = document.getElementById("screenname");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");

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

    function validateScreenname() {
        const screennamePattern = /^\w+$/;
        if (!screennamePattern.test(screenname.value)) {
            showError(screenname, "Screenname must contain only letters, numbers, and underscores");
            return false;
        }
        showSuccess(screenname);
        return true;
    }

    function validatePassword() {
        const passwordPattern = /^(?=.*[^a-zA-Z]).{6,}$/;
        if (!passwordPattern.test(password.value)) {
            showError(password, "Password must be at least 6 characters long and contain at least one non-letter");
            return false;
        }
        showSuccess(password);
        return true;
    }

    function validateConfirmPassword() {
        if (confirmPassword.value !== password.value) {
            showError(confirmPassword, "Passwords do not match");
            return false;
        }
        showSuccess(confirmPassword);
        return true;
    }

    email.addEventListener("blur", validateEmail);
    screenname.addEventListener("blur", validateScreenname);
    password.addEventListener("blur", validatePassword);
    confirmPassword.addEventListener("blur", validateConfirmPassword);

    // Avatar selection 
    const avatarOptions = document.querySelectorAll('.avatar-option input');

    avatarOptions.forEach(option => {
        option.addEventListener('change', function () {
            // Remove selected class from all options
            document.querySelectorAll('.avatar-option label').forEach(label => {
                label.classList.remove('selected');
            });

            // Add selected class to the chosen avatar
            if (this.checked) {
                this.nextElementSibling.classList.add('selected');
            }
        });
    });

    // Trigger change on the checked avatar to highlight it initially
    document.querySelector('.avatar-option input:checked').dispatchEvent(new Event('change'));

    form.addEventListener("submit", function (event) {
        // Validate all fields and prevent submission if any fail
        if (
            !validateEmail() ||
            !validateScreenname() ||
            !validatePassword() ||
            !validateConfirmPassword()
        ) {
            event.preventDefault();
        }
    });
});
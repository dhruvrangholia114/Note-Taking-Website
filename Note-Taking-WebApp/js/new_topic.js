document.addEventListener("DOMContentLoaded", function () {
    const topicInput = document.getElementById("topicName");
    const createBtn = document.querySelector(".create-btn");
    const infoText = document.querySelector(".info-text");
    const form = document.querySelector("form");

    // Character counter
    topicInput.addEventListener("input", function () {
        let charCount = topicInput.value.length;
        let remainingChars = 256 - charCount;

        // Update info text with character count
        infoText.innerHTML = `The max number of characters input is <b>256</b>. <span style="color:${remainingChars < 20 ? 'red' : 'black'};">${remainingChars} remaining.</span>`;
    });

    // Form validation
    form.addEventListener("submit", function (event) {
        let topicValue = topicInput.value.trim();

        // Remove previous error messages
        let errorMsg = document.getElementById("error-message");
        if (errorMsg) errorMsg.remove();

        // Validate input
        if (topicValue === "") {
            event.preventDefault();
            showError("Topic name cannot be empty.");
        } else if (topicValue.length > 256) {
            event.preventDefault();
            showError("Topic name must be 256 characters or fewer.");
        }
        // If valid, the form submits to the server
    });

    // click handler for cancel button
    const cancelBtn = document.querySelector(".cancel-btn");
    if (cancelBtn) {
        cancelBtn.addEventListener("click", function () {
            window.location.href = "topiclist.php";
        });
    }

    function showError(message) {
        let errorDiv = document.createElement("p");
        errorDiv.id = "error-message";
        errorDiv.style.color = "red";
        errorDiv.style.fontSize = "15px";
        errorDiv.style.marginTop = "-21px";
        errorDiv.style.marginBottom = "17px";
        errorDiv.style.textAlign = "center";
        errorDiv.textContent = message;
        infoText.parentNode.insertBefore(errorDiv, infoText.nextSibling);
    }
});
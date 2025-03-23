document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const formMessage = document.getElementById("form-message");

    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            
            const firstName = document.getElementById("first-name").value.trim();
            const lastName = document.getElementById("last-name").value.trim();
            const question = document.getElementById("question").value.trim();

            // Validate inputs
            if (!validateName(firstName)) {
                showMessage("First Name should only contain letters and spaces.", "error");
                return false;
            }

            if (!validateName(lastName)) {
                showMessage("Last Name should only contain letters and spaces.", "error");
                return false;
            }

            if (!validateQuestion(question)) {
                showMessage("Your question should only contain letters, numbers, spaces, and '@'.", "error");
                return false;
            }

            // Create form data
            const formData = new FormData();
            formData.append("first-name", firstName);
            formData.append("last-name", lastName);
            formData.append("question", question);

            try {
                const response = await fetch("contact_handler.php", {
                    method: "POST",
                    body: formData
                });

                const data = await response.json();

                if (data.error) {
                    showMessage(data.message, "error");
                } else {
                    showMessage(data.message, "success");
                    form.reset();
                }
            } catch (error) {
                showMessage("An error occurred. Please try again later.", "error");
            }
        });
    }
});

function showMessage(message, type) {
    const formMessage = document.getElementById("form-message");
    formMessage.textContent = message;
    formMessage.className = `form-message ${type}`;
}

/**
 * Validates name input (First Name & Last Name)
 * Only allows letters (A-Z, a-z), numbers (0-9), and spaces
 */
function validateName(input) {
    const regex = /^[a-zA-Z ]+$/;
    return regex.test(input);
}

function validateQuestion(input) {
    const regex = /^[a-zA-Z0-9\s@.,!?-]+$/;
    return regex.test(input);
}



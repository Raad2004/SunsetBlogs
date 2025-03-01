document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.blog-form');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission until validation passes
        
        // Get all required fields
        const blogTitle = document.getElementById('blogTitle').value.trim();
        const blogDate = document.getElementById('blogDate').value.trim();
        const blogAuthor = document.getElementById('blogAuthor').value.trim();
        const firstSubtitle = document.getElementById('firstSubtitle').value.trim();
        const firstContent = document.getElementById('firstContent').value.trim();

        // Reset previous error messages
        clearErrors();

        // Validate each required field
        let isValid = true;

        if (!blogTitle) {
            displayError('blogTitle', 'Blog title is required');
            isValid = false;
        }

        if (!blogDate) {
            displayError('blogDate', 'Date is required');
            isValid = false;
        }

        if (!blogAuthor) {
            displayError('blogAuthor', 'Author name is required');
            isValid = false;
        }

        if (!firstSubtitle) {
            displayError('firstSubtitle', 'At least one subtitle is required');
            isValid = false;
        }

        if (!firstContent) {
            displayError('firstContent', 'Content for the first subtitle is required');
            isValid = false;
        }

        // If all validations pass, submit the form
        if (isValid) {
            form.submit();
        }
    });

    // Function to display error message
    function displayError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.8em';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        // Insert error message after the field
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
        
        // Add red border to the field
        field.style.borderColor = 'red';
    }

    // Function to clear all error messages
    function clearErrors() {
        // Remove all error messages
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.remove());
        
        // Reset all field borders
        const fields = form.querySelectorAll('input, textarea');
        fields.forEach(field => field.style.borderColor = '');
    }
}); 
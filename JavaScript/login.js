document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password }),
            credentials: 'same-origin' // This ensures cookies are sent with the request
        });
        
        const data = await response.json();
        console.log('Login Response:', data);
        
        if (data.success) {
            console.log('Logged in as:', data.username, 'isAdmin:', data.isAdmin);
            
            // Get the base URL
            const baseUrl = window.location.href.split('/Pages/')[0] + '/Pages/';
            
            if (data.isAdmin) {
                console.log('Admin user detected, redirecting to profile.php');
                // Use absolute path for redirection
                window.location.href = baseUrl + 'profile.php';
            } else {
                console.log('Regular user detected, redirecting to home.html');
                window.location.href = baseUrl + 'home.html';
            }
        } else {
            console.error('Login failed:', data.message);
            showError(data.message);
        }
    } catch (error) {
        console.error('Login error:', error);
        showError('An error occurred while logging in. Please try again.');
    }
});

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    const form = document.getElementById('login-form');
    const existingError = form.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    form.insertBefore(errorDiv, form.firstChild);
} 
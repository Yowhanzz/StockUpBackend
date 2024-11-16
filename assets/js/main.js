// Import necessary modules (if using Node.js)
const fetch = require('node-fetch'); // For sending HTTP requests, if not using native fetch in server environments

// Register User Function for REST API
async function registerUser(full_name, username, password) {
    try {
        const response = await fetch('model/register.model.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ full_name, username, password })
        });

        const data = await response.json();

        if (data.message) {
            console.log('Registration successful:', data.message);
            return { success: true, message: data.message };
        } else {
            console.log('Registration failed:', data.error || "Unknown error");
            return { success: false, message: data.error || "Registration failed" };
        }
    } catch (error) {
        console.error('Error during registration:', error);
        return { success: false, message: "Server error during registration" };
    }
}

// Login User Function for REST API
async function loginUser(username, password) {
    try {
        const response = await fetch('model/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        // Log the raw response for debugging
        const text = await response.text();
        console.log('Raw Response:', text);

        // Try parsing the response as JSON
        try {
            const data = JSON.parse(text);

            if (data.token) {
                console.log('Login successful, JWT received');
                return { success: true, token: data.token };
            } else {
                console.log('Login failed:', data.error || "Unknown error");
                return { success: false, message: data.error || "Login failed" };
            }
        } catch (err) {
            console.error("Failed to parse JSON:", err);
            return { success: false, message: "Unexpected server response. Please check server logs." };
        }
    } catch (error) {
        console.error('Error during login:', error);
        return { success: false, message: "Server error during login" };
    }
}

// Example usage of the functions (e.g., for testing or within other server logic)
(async () => {
    // Test the registration function
    const registrationResult = await registerUser("John Doe", "john_doe", "securepassword123");
    console.log("Registration Result:", registrationResult);

    // Test the login function
    const loginResult = await loginUser("john_doe", "securepassword123");
    console.log("Login Result:", loginResult);
})();
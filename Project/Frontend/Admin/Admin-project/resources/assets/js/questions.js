
/**
* A reusable function to send API requests.
* @param {string} url - The endpoint URL.
* @param {string} method - HTTP method (GET, POST, PUT, DELETE, etc.).
* @param {Object} [data] - Request body (for POST, PUT, etc.).
* @param {Object} [headers] - Custom headers for the request.
* @param {Function} [onSuccess] - Callback function for successful response.
* @param {Function} [onError] - Callback function for error handling.
* @returns {Promise<void>} - Resolves when the request completes.
*/
async function apiRequest({ url, method = 'GET', data = null, headers = {}, onSuccess, onError }) {
    try {
        // Default headers
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        // Prepare fetch options
        const options = {
            method,
            headers: { ...defaultHeaders, ...headers },
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
            options.body = JSON.stringify(data);
        }

        // Send the request
        const response = await fetch(url, options);

        // Handle HTTP errors
        if (!response.ok) {
            const errorMessage = `HTTP Error: ${response.status} - ${response.statusText}`;
            if (onError) onError(errorMessage);
            throw new Error(errorMessage);
        }

        // Parse JSON response
        const responseData = await response.json();

        // Trigger success callback if provided
        if (onSuccess) onSuccess(responseData);

        return responseData;
    } catch (error) {
        // Trigger error callback if provided
        if (onError) onError(error.message);
        console.error('API Request Error:', error);
    }
}
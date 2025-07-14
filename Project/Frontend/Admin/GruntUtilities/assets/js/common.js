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
function reloadAfter1Second() {
    setTimeout(function () {
        window.location.reload();
    }, 1000);
}
function reloadAfter2Second() {
    setTimeout(function () {
        window.location.reload();
    }, 2000);
}
async function apiRequest({ url, method = 'GET', data = null, headers = {}, onSuccess, onError }) {
    try {
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        const options = {
            method,
            headers: { ...defaultHeaders, ...headers },
        };
        if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
            options.body = JSON.stringify(data);
        }
        const response = await fetch(url, options);
        if (!response.ok) {
            const errorResponse = await response.json();
            const errorMessage = errorResponse.message || `HTTP Error: ${response.status} - ${response.statusText}`;
            if (onError) onError(errorMessage);
            throw new Error(errorMessage);
        }
        const responseData = await response.json();
        if (onSuccess) onSuccess(responseData);
        return responseData;
    } catch (error) {
        if (onError) onError(error.message);
        // console.error('API Request Error:', error);
    }
}

var toastCount = 0;
var $toastlast;

var getMessage = function (type) {
    if (type === 'success') {
        return "Operation was successful!";
    } else if (type === 'error') {
        return "An error occurred!";
    } else {
        return type;
    }
    return "";
};
function showToast(type, title, message) {
    type = type.toLowerCase();
    var msg = message || getMessage(type);
    var toastTitle = title || ''; // Use title if provided

    // Add a custom class based on type
    var customClass = `toast-${type}`;

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        showDuration: 300,
        hideDuration: 1000,
        timeOut: 5000,
        extendedTimeOut: 1000,
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
        // Custom class applied to the toast
        toastClass: `toastr ${customClass}`,
    };

    // Build the title and message content
    var toastHtml = `<strong>${toastTitle}</strong><br>${msg}`;

    // Display the toast
    var $toast = toastr[type](toastHtml);
    $toastlast = $toast;
}

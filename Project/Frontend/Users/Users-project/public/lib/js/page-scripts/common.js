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
        const isFormData = data instanceof FormData;
        const defaultHeaders = isFormData
            ? { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            : {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };
        const options = {
            method,
            headers: { ...defaultHeaders, ...headers },
        };
        if (data && ['POST', 'PUT', 'DELETE'].includes(method.toUpperCase())) {
            options.body = isFormData ? data : JSON.stringify(data);
        }
        const response = await fetch(url, options);
        if (!response.ok) {
            const errorResponse = await response.json().catch(() => ({}));
            const errorMessage = errorResponse.message || `HTTP Error: ${response.status} - ${response.statusText}`;
            if (onError) onError(errorMessage);
            throw new Error(errorMessage);
        }
        const contentType = response.headers.get('Content-Type') || '';
        const responseData = contentType.includes('application/json')
            ? await response.json()
            : await response.text();
        if (onSuccess) onSuccess(responseData);
        return responseData;
    } catch (error) {
        if (onError) onError(error.message);
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
    var toastTitle = title || '';
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
        toastClass: `toastr ${customClass}`,
    };
    var toastHtml = `<strong>${toastTitle}</strong><br>${msg}`;
    var $toast = toastr[type](toastHtml);
    $toastlast = $toast;
}

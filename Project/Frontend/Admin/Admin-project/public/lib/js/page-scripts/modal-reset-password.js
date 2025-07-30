document.addEventListener('DOMContentLoaded', function () {
    const toggleIcons = document.querySelectorAll('.input-group-text.cursor-pointer');
    toggleIcons.forEach(function (icon) {
        icon.addEventListener('click', function () {
            const input = this.previousElementSibling; // Get the input element
            const iconElement = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                iconElement.classList.remove('ti-eye-off');
                iconElement.classList.add('ti-eye');
            } else {
                input.type = 'password';
                iconElement.classList.remove('ti-eye');
                iconElement.classList.add('ti-eye-off');
            }
        });
    });
});

function encryptAndSubmit(currentPassword) {
    const publicKey = `-----BEGIN PUBLIC KEY-----
                    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmbxM48khEYD5JcqcSBLA
                    hbq7OAyX55wekJlOM0iG22uZjk5XsXd2K0DxJDIL8ocAlZBV9rLzsHQA3MQ9S3Qn
                    um8Hi8cNKcNX7MZMgwYr5+zzVGwPTu0UlK2YfCmqo61gvXNI04dEF7KYtzHZx23y
                    AdIM1TRHRi0Md+JbyQ5RfqPEoCQvLyN4//MPmxLWFM9MnpvuC+aImSG6OmVuYk1A
                    cH6oPW2UokgFuK3ie6IObDvZIWfjHMgWUtPP+PFQ4OLgqDCs64Vvx7q8M8RJZUdS
                    zFOWEOtSDaAnkYPi0T+o41KbOpYbZY7hk5gYG0vyZeYu5zvvQMNTnLNCdC0SuidP
                    EwIDAQAB
                    -----END PUBLIC KEY-----`;
    try {
        const encryptor = new JSEncrypt();
        encryptor.setPublicKey(publicKey);
        if (!currentPassword) {
            showToast("error", "Current password is required!");
            return false;
        }
        const encryptedPassword = encryptor.encrypt(currentPassword);
        if (encryptedPassword) {
            return encryptedPassword;
        } else {
            showToast("info", "Encryption failed!");
            return false;
        }
    } catch (err) {
        showToast("error", "Encryption failed: " + err.message);
        return false;
    }
}
document.getElementById('requestTokenButton').addEventListener('click', function () {
    const currentPassword = document.getElementById('currentPassword').value;
    if (!currentPassword || currentPassword.length < 8) {
        showToast("error", "Current password must be at least 8 characters long.");
        return;
    }
    const encryptedPassword = encryptAndSubmit(currentPassword);
    if (!encryptedPassword) return;
    const requestButton = document.getElementById('requestTokenButton');
    const spinner = requestButton.querySelector('.spinner-grow');
    spinner.style.display = 'inline-block';
    requestButton.disabled = true;
    apiRequest({
        url: '/auth/request-token',
        method: 'POST',
        data: {
            encryptedPassword: btoa(encryptedPassword)
        },
        onSuccess: (response) => {
            spinner.style.display = 'none';
            requestButton.disabled = false;
            if (response.result === 'error') {
                showToast("error", response.message || "An error occurred while requesting token.");
            } else {
                showToast("success", "Password reset link sent successfully to your email.");
                const modal = bootstrap.Modal.getInstance(document.getElementById('enableOTP'));
                modal.hide();
            }
        },
        onError: (errorMessage) => {
            spinner.style.display = 'none';
            requestButton.disabled = false;
            showToast("error", errorMessage || "An error occurred while processing your request.");
            const modal = bootstrap.Modal.getInstance(document.getElementById('enableOTP'));
            modal.hide();
        },
    });
});

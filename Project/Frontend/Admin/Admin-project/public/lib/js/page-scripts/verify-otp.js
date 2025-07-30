document.addEventListener('DOMContentLoaded', function () {
    const resendButton = document.getElementById('resend-otp-link');
    const verifyButton = document.getElementById('verify-my-account-btn');
    const otpInputs = document.querySelectorAll('.auth-input');
    const otpHiddenField = document.querySelector('input[name="otp"]');
    function clearOtpInputs() {
        otpInputs.forEach(input => {
            input.value = '';
        });
    }
    resendButton.addEventListener('click', function (event) {
        event.preventDefault();
        resendButton.textContent = 'Resending...';
        resendButton.style.pointerEvents = 'none';
        apiRequest({
            url: '/auth/resend-otp',
            method: 'GET',
            onSuccess: (data) => {
                if (data.success) {

                    showToast('success', data.message || 'OTP resent successfully.');

                } else {
                    showToast('error', data.message || 'Failed to resend OTP.');

                }
                clearOtpInputs();
            },
            onError: (error) => {
                showToast('error', error || 'An error occurred while resending OTP.');
                clearOtpInputs();
            }
        }).finally(() => {
            resendButton.textContent = 'Resend';
            resendButton.style.pointerEvents = 'auto';
        });
    });
    verifyButton.addEventListener('click', function (event) {
        event.preventDefault();
        let otp = Array.from(otpInputs).map(input => input.value).join('');
        if (!otp || otp.length !== 6 || isNaN(otp)) {
            showToast('error', 'Please enter a valid 6-digit OTP.');

            clearOtpInputs();
            return;
        }
        otpHiddenField.value = otp;
        isToastShown = false;
        apiRequest({
            url: `/auth/validate-otp/${otp}`,
            method: 'GET',
            onSuccess: (data) => {
                if (data.result === true) {
                    window.location.href = data.redirect_url;
                } else {
                    showToast('error', data.message || 'Failed to verify OTP.');

                }
                clearOtpInputs();
            },
            onError: (error) => {
                showToast('error', error || 'An error occurred while verifying the OTP.');

                clearOtpInputs();
            }
        });
    });
    otpInputs.forEach(input => {
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                verifyButton.click();
            }
        });
    });
});

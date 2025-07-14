$(document).ready(function () {
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: '/reload-captcha',
            success: function (data) {
                $(".captcha span").html(data.captcha)
            }
        })
    });
    toastr.options = {
        "progressBar": true,
        "closeButton": true,
        "positionClass": "toast-top-right"
    };
    const captchaType = document.getElementById("captchaType").value;
    if (captchaType !== "google_v3" && captchaType !== "anCaptcha") {
        alert(captchaType);
        showToast("Error", "Invalid Captcha, " + captchaType);
        event.preventDefault();
        return false;
    }
    if (captchaType === "google_v3" && window.CAPTCHA_SITE_KEY !== 'none') {
        grecaptcha.ready(function () {
            grecaptcha
                .execute(window.CAPTCHA_SITE_KEY, {
                    action: "contact",
                })
                .then(function (token) {
                    if (token) {
                        document.getElementById("g-recaptcha").value = token;
                        const submitButton = document.getElementById("submitButton");
                        const spinner = document.getElementById("letting-in-spinner");
                        submitButton.disabled = false;
                        spinner.remove();
                        submitButton.textContent = "Login";
                    }
                });
        });
    }
    $(document).on("submit", "#formAuthentication", function (event) {
        event.preventDefault();
        if (encryptAndSubmit()) {
            this.submit();
        }
    });
    function encryptAndSubmit() {
        const publicKey = `-----BEGIN PUBLIC KEY-----
                    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmbxM48khEYD5JcqcSBLA
                    hbq7OAyX55wekJlOM0iG22uZjk5XsXd2K0DxJDIL8ocAlZBV9rLzsHQA3MQ9S3Qn
                    um8Hi8cNKcNX7MZMgwYr5+zzVGwPTu0UlK2YfCmqo61gvXNI04dEF7KYtzHZx23y
                    AdIM1TRHRi0Md+JbyQ5RfqPEoCQvLyN4//MPmxLWFM9MnpvuC+aImSG6OmVuYk1A
                    cH6oPW2UokgFuK3ie6IObDvZIWfjHMgWUtPP+PFQ4OLgqDCs64Vvx7q8M8RJZUdS
                    zFOWEOtSDaAnkYPi0T+o41KbOpYbZY7hk5gYG0vyZeYu5zvvQMNTnLNCdC0SuidP
                    EwIDAQAB
                    -----END PUBLIC KEY-----`;
        const encryptor = new JSEncrypt();
        encryptor.setPublicKey(publicKey);
        const email = document.getElementById("email-username").value;
        const password = document.getElementById("password").value;
        if (!email && !password) {
            showToast("error", "Username or Password cannot be empty.");
            event.preventDefault();
            return false;
        }
        if (!email) {
            showToast("error", "Email is required!");
            event.preventDefault();
            return false;
        }
        if (!password) {
            showToast("error", "Password is required!");
            event.preventDefault();
            return false;
        }
        const encryptedEmail = encryptor.encrypt(email);
        const encryptedPassword = encryptor.encrypt(password);
        if (encryptedEmail && encryptedPassword) {
            document.getElementById("email-username").value = btoa(encryptedEmail);
            document.getElementById("password").value = btoa(encryptedPassword);
            return true;
        } else {
            toastr.options = {
                progressBar: true,
                closeButton: true,
            };
            showToast("info", "Invalid Request");
            return false;
        }
    }
});
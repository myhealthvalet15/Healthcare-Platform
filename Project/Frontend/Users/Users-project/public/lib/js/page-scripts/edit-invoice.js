$(document).ready(function () {
    $("#invoice_date, #entry_date, #ohc_verify_date, #hr_verify_date, #ses_release_date, #submission_date, #payment_date, #cash_invoice_date, #cash_entry_date,#ses_date,#head_verify_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today"
    });
    function formatDate(dateString) {
        if (dateString) {
            var dateParts = dateString.split('-');
            return dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
        }
        return '';
    }
    $('#add_invoice').click(function (event) {
        event.preventDefault();
        var selectedInvoiceType = $('input[name="invoice_type"]:checked').val();
        console.log("Selected Invoice Type: " + selectedInvoiceType);
        var invoice_date = $('#invoice_date').val();
        var invoice_number = $('#invoice_number').val();
        var invoice_amount = $('#invoice_amount').val();
        var entry_date = $('#entry_date').val();
        var ohc_verify_date = $('#ohc_verify_date').val();
        var hr_verify_date = $('#hr_verify_date').val();
        var ses_number = $('#ses_number').val();
        var ses_date = $('#ses_date').val();
        var head_verify_date = $('#head_verify_date').val();
        var ses_release_date = $('#ses_release_date').val();
        var submission_date = $('#submission_date').val();
        var payment_date = $('#payment_date').val();
        var cash_invoice_date = $('#cash_invoice_date').val();
        var cash_vendor = $('#cash_vendor').val();
        var cash_invoice_number = $('#cash_invoice_number').val();
        var cash_amount = $('#cash_amount').val();
        var cash_entry_date = $('#cash_entry_date').val();
        var cash_invoice_details = $('#cash_invoice_details').val();
        var formIsValid = true;
        var firstInvalidField = null;
        function containsSpecialCharacters(value) {
            var regex = /[^a-zA-Z0-9 ]/;
            return regex.test(value.trim());
        }
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        if (!invoice_date || invoice_date.trim() === '') {
            formIsValid = false;
            $('#invoice_date').addClass('is-invalid');
            $('#invoice_date').after('<div class="invalid-feedback">Please Select Invoice Date</div>');
            if (!firstInvalidField) firstInvalidField = '#invoice_date';
        }
        if (!invoice_number || invoice_number.trim() === '') {
            formIsValid = false;
            $('#invoice_number').addClass('is-invalid');
            $('#invoice_number').after('<div class="invalid-feedback">Invoice Number is required.</div>');
            if (!firstInvalidField) firstInvalidField = '#invoice_number';
        } else if (containsSpecialCharacters(invoice_number)) {
            formIsValid = false;
            $('#invoice_number').addClass('is-invalid');
            $('#invoice_number').after('<div class="invalid-feedback">Invoice Number should not contain special characters.</div>');
            if (!firstInvalidField) firstInvalidField = '#invoice_number';
        }
        if (!invoice_amount || invoice_amount.trim() === '' || isNaN(invoice_amount)) {
            formIsValid = false;
            $('#invoice_amount').addClass('is-invalid');
            $('#invoice_amount').after('<div class="invalid-feedback">The Invoice Amount is required and must be a valid number.</div>');
            if (!firstInvalidField) firstInvalidField = '#invoice_amount';
        }
        if (!entry_date || entry_date.trim() === '') {
            formIsValid = false;
            $('#entry_date').addClass('is-invalid');
            $('#entry_date').after('<div class="invalid-feedback">Entry Date is required for PO invoice.</div>');
            if (!firstInvalidField) firstInvalidField = '#entry_date';
        }
        if (selectedInvoiceType === 'cash') {
            console.log('cash_invoice_date:', cash_invoice_date);
            console.log('cash_vendor:', cash_vendor);
            console.log('cash_invoice_number:', cash_invoice_number);
            console.log('cash_amount:', cash_amount);
            console.log('cash_entry_date:', cash_entry_date);
            if (!cash_invoice_date || cash_invoice_date.trim() === '') {
                formIsValid = false;
                $('#cash_invoice_date').addClass('is-invalid');
                $('#cash_invoice_date').after('<div class="invalid-feedback">Invoice Date is required for cash invoice.</div>');
                if (!firstInvalidField) firstInvalidField = '#cash_invoice_date';
            }
            if (!cash_vendor || cash_vendor.trim() === '') {
                formIsValid = false;
                $('#cash_vendor').addClass('is-invalid');
                $('#cash_vendor').after('<div class="invalid-feedback">Cash Vendor is required.</div>');
                if (!firstInvalidField) firstInvalidField = '#cash_vendor';
            } else if (containsSpecialCharacters(cash_vendor)) {
                console.log("Contains special characters check for cash_vendor:", containsSpecialCharacters(cash_vendor));
                formIsValid = false;
                $('#cash_vendor').addClass('is-invalid');
                $('#cash_vendor').after('<div class="invalid-feedback">Cash Vendor should not contain special characters.</div>');
                if (!firstInvalidField) firstInvalidField = '#cash_vendor';
            }
            if (!cash_invoice_number || cash_invoice_number.trim() === '') {
                formIsValid = false;
                $('#cash_invoice_number').addClass('is-invalid');
                $('#cash_invoice_number').after('<div class="invalid-feedback">Cash Invoice Number is required.</div>');
                if (!firstInvalidField) firstInvalidField = '#cash_invoice_number';
            } else if (containsSpecialCharacters(cash_invoice_number)) {
                console.log("Contains special characters check for cash_invoice_number:", containsSpecialCharacters(cash_invoice_number));
                formIsValid = false;
                $('#cash_invoice_number').addClass('is-invalid');
                $('#cash_invoice_number').after('<div class="invalid-feedback">Cash Invoice Number should not contain special characters.</div>');
                if (!firstInvalidField) firstInvalidField = '#cash_invoice_number';
            }
            if (!cash_amount || isNaN(cash_amount) || cash_amount <= 0) {
                formIsValid = false;
                $('#cash_amount').addClass('is-invalid');
                $('#cash_amount').after('<div class="invalid-feedback">Cash Amount is required and must be a positive number.</div>');
                if (!firstInvalidField) firstInvalidField = '#cash_amount';
            }
            if (!cash_entry_date || cash_entry_date.trim() === '') {
                formIsValid = false;
                $('#cash_entry_date').addClass('is-invalid');
                $('#cash_entry_date').after('<div class="invalid-feedback">Entry Date is required for cash invoice.</div>');
                if (!firstInvalidField) firstInvalidField = '#cash_entry_date';
            }
        }
        if (!formIsValid) {
            console.log('Form is invalid, first invalid field:', firstInvalidField);
            $(firstInvalidField).focus();
        }
        if (formIsValid) {
            var formData = {
                _token: csrfToken,
                selectedInvoiceType: selectedInvoiceType,
                invoice_date: invoice_date,
                invoice_number: invoice_number,
                invoice_amount: invoice_amount,
                entry_date: entry_date,
                ohc_verify_date: ohc_verify_date,
                hr_verify_date: hr_verify_date,
                ses_number: ses_number,
                ses_date: ses_date,
                head_verify_date: head_verify_date,
                ses_release_date: ses_release_date,
                submission_date: submission_date,
                payment_date: payment_date,
            };
            if (selectedInvoiceType === 'cash') {
                if (cash_invoice_date && cash_invoice_date.trim() !== '') {
                    formData.cash_invoice_date = cash_invoice_date;
                }
                if (cash_vendor && cash_vendor.trim() !== '') {
                    formData.cash_vendor = cash_vendor;
                }
                if (cash_invoice_number && cash_invoice_number.trim() !== '') {
                    formData.cash_invoice_number = cash_invoice_number;
                }
                if (cash_amount && cash_amount.trim() !== '' && !isNaN(cash_amount) && cash_amount > 0) {
                    formData.cash_amount = cash_amount;
                }
                if (cash_entry_date && cash_entry_date.trim() !== '') {
                    formData.cash_entry_date = cash_entry_date;
                }
                if (cash_invoice_details && cash_invoice_details.trim() !== '') {
                    formData.cash_invoice_details = cash_invoice_details;
                }
            }
            var currentUrl = window.location.pathname;
            var invoiceId = currentUrl.split('/').pop();
            var requestUrl = '/others/updateInvoice/' + invoiceId;
            apiRequest({
                url: requestUrl,
                method: 'POST',
                data: formData,
                onSuccess: function (response) {
                    console.log("Full Response:", response);
                    console.log("Type of response:", typeof response);

                    if (response.result === true) {
                        showToast("success", "Data saved successfully!");
                        console.log('Redirecting to:', 'https://login-users.hygeiaes.com/others/invoice');
                        window.location.href = 'https://login-users.hygeiaes.com/others/invoice';
                    } else {
                        if (response.details) {
                            try {
                                const details = typeof response.details === 'string'
                                    ? JSON.parse(response.details)
                                    : response.details;

                                if (details.message) {
                                    showToast("error", details.message);
                                } else {
                                    showToast("error", "An error occurred while saving the data.");
                                }
                            } catch (e) {
                                showToast("error", "An error occurred while saving the data.");
                            }
                        } else {
                            showToast("error", response.message || "An error occurred while saving the data.");
                        }
                    }
                },
                onError: function (errorMessage) {
                    showToast("error", errorMessage || "An error occurred during the request.");
                }
            });
        }
    });
    var fields = [
        '#invoice_date', '#invoice_number', '#invoice_amount', '#entry_date',
        '#cash_invoice_date', '#cash_invoice_number', '#cash_amount', '#cash_entry_date',
        '#cash_vendor'
    ];
    fields.forEach(function (field) {
        $(field).on('focus', function () {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
    });
});

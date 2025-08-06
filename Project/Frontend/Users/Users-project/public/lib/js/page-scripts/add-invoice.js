$(document).ready(function () {
    $("#invoice_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#entry_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#cash_invoice_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#cash_entry_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#ohc_verify_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#hr_verify_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#ses_release_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#submission_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#payment_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#head_verify_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#head_verify_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $('#corporate_po_id, #po_number').on('change', function () {
        var corporatePoId = $('#corporate_po_id').val().trim();
        var poNumber = $('#po_number').val().trim();
        console.log("corporatePoId:", corporatePoId);
        console.log("poNumber:", poNumber);
        if (!corporatePoId || !poNumber) {
            $('#remaining_balance').text('Select PO Number');
            return;
        }
        var formData = {
            corporate_po_id: corporatePoId,
            po_number: poNumber
        };
        console.log("formData:", formData);
        apiRequest({
            url: '/others/getPoBalance',
            method: 'GET',
            data: formData,
            onSuccess: (response) => {
                console.log("Response.result:", response.result);
                console.log("Response.remainingBalance:", response.remainingBalance);
                if (response && response.result === true && response.remainingBalance !== undefined) {
                    const remainingBalance = response.remainingBalance;
                    $('#remaining_balance').text('Remaining PO Balance: ' + remainingBalance);
                    const enteredAmount = $('#invoice_amount').val().trim();
                    console.log("Entered Amount:", enteredAmount);
                    if (parseFloat(enteredAmount) > remainingBalance) {
                        alert('Entered amount exceeds the remaining PO balance!');
                        return;
                    }
                } else {
                    $('#remaining_balance').text(response ? response.message : 'Unknown error');
                }
            },
            onError: (error) => {
                console.error('An error occurred: ' + error);
                $('#remaining_balance').text('An error occurred while fetching the PO balance.');
            }
        });
    });
    $('#add_invoice').click(function () {
        var selectedInvoiceType = $('input[name="invoice_type"]:checked').val();
        console.log("Selected Invoice Type: " + selectedInvoiceType);
        var corporate_po_id = $('#corporate_po_id').val();
        var po_number = $('#po_number').val();
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
        if (!corporate_po_id || corporate_po_id.trim() === '') {
            formIsValid = false;
            $('#corporate_po_id').addClass('is-invalid');
            $('#corporate_po_id').after('<div class="invalid-feedback">Please select Vendor</div>');
            if (!firstInvalidField) firstInvalidField = '#corporate_po_id';
        }
        if (!po_number || po_number.trim() === '') {
            formIsValid = false;
            $('#po_number').addClass('is-invalid');
            $('#po_number').after('<div class="invalid-feedback">Please Select PO Number</div>');
            if (!firstInvalidField) firstInvalidField = '#po_number';
        }
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
                if (!firstInvalidField) firstInvalidField = '#cash_invoice_date';
            }
        }
        if (firstInvalidField) {
            $(firstInvalidField).focus();
        }
        if (!formIsValid) {
            $(firstInvalidField).focus();
        }
        apiRequest({
            url: '/others/getPoBalance',
            method: 'GET',
            data: {
                corporate_po_id: corporate_po_id,
                po_number: po_number
            },
            onSuccess: (response) => {
                console.log("Response.result:", response.result);
                console.log("Response.remainingBalance:", response.remainingBalance);
                if (response && response.result === true && response.remainingBalance !== undefined) {
                    const remainingBalance = response.remainingBalance;
                    $('#remaining_balance').text('Remaining PO Balance: ' + remainingBalance);
                    const enteredAmount = parseFloat(invoice_amount);
                    console.log("Entered Amount:", enteredAmount);
                    if (enteredAmount > remainingBalance) {
                        alert('Insufficient balance. Entered amount exceeds the remaining PO balance!');
                        return;
                    }
                    if (formIsValid) {
                        const formData = {
                            selectedInvoiceType: selectedInvoiceType,
                            corporate_po_id: corporate_po_id,
                            po_number: po_number,
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
                            payment_date: payment_date
                        };
                        if (selectedInvoiceType === 'cash') {
                            formData.cash_invoice_date = cash_invoice_date;
                            formData.cash_vendor = cash_vendor;
                            formData.cash_invoice_number = cash_invoice_number;
                            formData.cash_amount = cash_amount;
                            formData.cash_entry_date = cash_entry_date;
                            formData.cash_invoice_details = cash_invoice_details;
                        }
                        apiRequest({
                            url: '/others/insertInvoice',
                            method: 'POST',
                            data: formData,
                            onSuccess: (response) => {
                                console.log("Full Response:", response);
                                if (response.success === true) {
                                    showToast("success", "Data saved successfully!");
                                    window.location.href = 'https://login-users.hygeiaes.com/others/inventory';
                                } else {
                                    alert('An error occurred while saving the data.');
                                }
                            },
                            onError: (error) => {
                                console.error('An error occurred: ' + error);
                                alert('An error occurred while saving the data.');
                            }
                        });
                    }
                } else {
                    alert('Unable to fetch PO balance or invalid response.');
                }
            },
            onError: (error) => {
                console.error('Error fetching PO balance:', error);
                alert('An error occurred while fetching the PO balance.');
            }
        });
    });
    $('#corporate_po_id').on('change', function () {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    $('#po_number').on('change', function () {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    $('#invoice_date').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#invoice_number').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#invoice_amount').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#entry_date').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#cash_invoice_date').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#cash_invoice_number').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#cash_amount').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#cash_entry_date').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#cash_vendor').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});
document.querySelectorAll('input[name="invoice_type"]').forEach((radio) => {
    radio.addEventListener('change', function () {
        if (this.value === 'po') {
            document.getElementById('po_invoice_form').style.display = 'block';
            document.getElementById('cash_invoice_form').style.display = 'none';
        } else {
            document.getElementById('po_invoice_form').style.display = 'none';
            document.getElementById('cash_invoice_form').style.display = 'block';
        }
    });
});
window.onload = function () {
    document.getElementById('po_invoice').checked = true;
    document.getElementById('po_invoice_form').style.display = 'block';
    document.getElementById('cash_invoice_form').style.display = 'none';
};
let vendorData = [];
apiRequest({
    url: 'https://login-users.hygeiaes.com/others/getVendorDetails',
    method: 'GET',
    onSuccess: (response) => {
        console.log('Full Response:', response);
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                return;
            }
        }
        console.log('Response Result:', response.result);
        $('#corporate_po_id').empty();
        $('#corporate_po_id').append('<option value="">-Select Vendor-</option>');
        if (response.result === true || response.result === "true") {
            if (Array.isArray(response.data) && response.data.length > 0) {
                const vendors = response.data;
                vendorData = vendors;
                vendors.forEach(function (vendor) {
                    console.log('Vendor:', vendor);
                    const option = $('<option></option>')
                        .val(vendor.corporate_po_id)
                        .text(vendor.vendor_name);
                    $('#corporate_po_id').append(option);
                });
            } else {
                console.log('No vendor data found in the response');
            }
        } else {
            console.log('Response result is not true:', response.result);
        }
    },
    onError: (error) => {
        console.log('Error fetching vendor data:', error);
    }
});
$('#corporate_po_id').change(function () {
    const selectedVendorId = $(this).val();
    if (selectedVendorId) {
        const selectedVendor = vendorData.find(vendor => vendor.corporate_po_id == selectedVendorId);
        console.log(selectedVendor);
        $('#po_number').empty();
        $('#po_number').append('<option value="">Select PO Number</option>');
        if (selectedVendor && selectedVendor.po_number) {
            const poNumbers = Array.isArray(selectedVendor.po_number) ? selectedVendor.po_number : [selectedVendor.po_number];
            poNumbers.forEach(function (poNumber) {
                const poOption = $('<option></option>')
                    .val(poNumber)
                    .text(poNumber);
                $('#po_number').append(poOption);
            });
        }
    } else {
        $('#po_number').empty();
        $('#po_number').append('<option value="">Select PO Number</option>');
    }
});

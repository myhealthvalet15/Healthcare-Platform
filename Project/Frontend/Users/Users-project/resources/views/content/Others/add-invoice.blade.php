@extends('layouts/layoutMaster')

@section('title', 'Add New Invoice - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss','resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


@section('content')
<div class="col-12 mb-6">
    <div id="wizard-validation" class="bs-stepper mt-2">
        

        <div class="bs-stepper-content">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" class="btn btn-primary" id="back-to-list" onclick="window.location.href='/others/invoice'" style="margin-right: 20px;">Back to Invoice</button>
            </div>
            <form id="wizard-validation-form" method="post">
                <div class="row g-6">
                    <div class="col-sm-6">
                        <label for="invoice_type" class="form-label">
                            <h5>Invoice Type</h5>
                        </label>
                        <div style="color: #4444e5;">
                            <input type="radio" id="cash_invoice" name="invoice_type" value="cash" checked>&nbsp;Cash Invoice&nbsp;&nbsp;
                            <input type="radio" id="po_invoice" name="invoice_type" value="po"> &nbsp;PO Invoice
                        </div>
                    </div>
                </div>

                <br />


                <h5>Invoice Details</h5>
                <!-- PO Invoice Fields (Default) -->
                <div id="po_invoice_form" class="content">

                    <div class="row g-6">
                        <div class="col-sm-6">
                            <label for="invoice_date" class="form-label">Select Vendor</label>
                            <select id="corporate_po_id" class="form-control" required>
                                <option value="">-Select Vendor-</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="po_number" class="form-label" required>Select PO Number</label>
                            <select id="po_number" class="form-control">
                                <option value="">Select PO Number</option>
                            </select>
                        </div>
                        <div id="remaining_balance" style="Color:green;"></div>
                        <div class="col-sm-6">
                            <label for="invoice_date" class="form-label">Invoice Date</label>
                            <input type="text" id="invoice_date" class="form-control dob-picker flatpickr-input active" placeholder="DD-MM-YYYY" readonly="readonly" fdprocessedid="4n8qxi">
                            </div>
                        <div class="col-sm-6">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" id="invoice_number" name="invoice_number" class="form-control" placeholder="Enter Invoice Number" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="invoice_amount" class="form-label">Amount</label>
                            <input type="number" id="invoice_amount" name="invoice_amount" class="form-control" placeholder="Enter Amount" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="entry_date" class="form-label">Entry Date</label>
                            <input type="date" id="entry_date" name="entry_date" class="form-control">
                        </div>
                    </div>
                    <br />
                    <h5>Invoice Process</h5>
                    <div class="row g-6">
                        <div class="col-sm-6">
                            <label for="ohc_verify_date" class="form-label">OHC Verification Date</label>
                            <input type="date" id="ohc_verify_date" name="ohc_verify_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="hr_verify_date" class="form-label">HR Verification Date</label>
                            <input type="date" id="hr_verify_date" name="hr_verify_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="ses_number" class="form-label">SES Number</label>
                            <input type="text" id="ses_number" name="ses_number" class="form-control" placeholder="Enter SES Number">
                        </div>
                        <div class="col-sm-6">
                            <label for="ses_date" class="form-label">SES Date</label>
                            <input type="date" id="ses_date" name="ses_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="dept_head_verification" class="form-label">Dept.Head Verification Date</label>
                            <input type="date" id="head_verify_date" name="head_verify_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="ses_release_date" class="form-label">SES Released Date</label>
                            <input type="date" id="ses_release_date" name="ses_release_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="submission_date" class="form-label">Bill Submission Date</label>
                            <input type="date" id="submission_date" name="submission_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="payment_date" class="form-label">Payment Advance Date</label>
                            <input type="date" id="payment_date" name="payment_date" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Cash Invoice Fields -->
                <div id="cash_invoice_form" class="content" style="display:none;">
                    <div class="row g-6">
                        <div class="col-sm-6">
                            <label for="cash_invoice_date" class="form-label">Cash Invoice Date</label>
                            <input type="date" id="cash_invoice_date" name="cash_invoice_date" class="form-control" required>
                        </div>
                       
                        <div class="col-sm-6">
                            <label for="cash_invoice_number" class="form-label">Cash Invoice Number</label>
                            <input type="text" id="cash_invoice_number" name="cash_invoice_number" class="form-control" placeholder="Enter Invoice Number" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="cash_amount" class="form-label">Amount</label>
                            <input type="number" id="cash_amount" name="cash_amount" class="form-control" placeholder="Enter Amount" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="cash_entry_date" class="form-label">Entry Date</label>
                            <input type="date" id="cash_entry_date" name="cash_entry_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="cash_vendor" class="form-label">Cash Invoice Vendor</label>
                            <input type="text" id="cash_vendor" name="cash_vendor" class="form-control" placeholder="Enter Cash Vendor" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="cash_invoice_details" class="form-label">Cash Invoice Details</label>
                            <input type="text" id="cash_invoice_details" name="cash_invoice_details" class="form-control" placeholder="Enter Description">
                        </div>
                    </div>
                </div>
                <br /><br />
                <!-- Submit Button for Both Forms -->
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-primary" id="add_invoice">Save</button>
                    <button type="reset" class="btn btn-label-danger waves-effect" onclick="window.location.href='/others/invoice'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Output the URL to check if it's correct
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
    
    $('#corporate_po_id, #po_number').on('change', function() {
    var corporatePoId = $('#corporate_po_id').val().trim(); // Remove leading/trailing spaces
    var poNumber = $('#po_number').val().trim(); // Remove leading/trailing spaces

    console.log("corporatePoId:", corporatePoId);  // Check the value
    console.log("poNumber:", poNumber);  // Check the value

    // If either field is empty, do not proceed
    if (!corporatePoId || !poNumber) {
        $('#remaining_balance').text('Select PO Number');
        return; // Prevent the AJAX call if any field is empty
    }

    var formData = {
        _token: csrfToken, // Ensure csrfToken is defined
        corporate_po_id: corporatePoId,
        po_number: poNumber
    };

    console.log("formData:", formData);  // Log the formData to check its content

    $.ajax({
        url: '/others/getPoBalance',
        method: 'GET',
        data: formData,
        success: function(response) {
            console.log("Response.result:", response.result);
            console.log("Response.remainingBalance:", response.remainingBalance);

            if (response && response.result === true && response.remainingBalance !== undefined) {
                var remainingBalance = response.remainingBalance;
                $('#remaining_balance').text('Remaining PO Balance: ' + remainingBalance);

                // Now check if the entered value exceeds the remaining balance
                var enteredAmount = $('#invoice_amount').val().trim(); // Replace with the correct input field for the amount

                console.log("Entered Amount:", enteredAmount);

                if (parseFloat(enteredAmount) > remainingBalance) {
                    alert('Entered amount exceeds the remaining PO balance!');
                    return; // Prevent further actions if amount exceeds balance
                }

                // Proceed with the insert logic if the balance check passes
                // For example, if it's an invoice insertion, you can trigger the insert here
                // insertInvoice(enteredAmount); // Replace with your actual insertion logic
            } else {
                $('#remaining_balance').text(response ? response.message : 'Unknown error');
            }
        },
        error: function(xhr, status, error) {
            console.error('An error occurred: ' + error);
            $('#remaining_balance').text('An error occurred while fetching the PO balance.');
        }
    });
});


        // CSRF Token
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $('#add_invoice').click(function() {
    // Fetch the selected invoice type
    var selectedInvoiceType = $('input[name="invoice_type"]:checked').val();
    console.log("Selected Invoice Type: " + selectedInvoiceType);

    // Fetch all form values
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

    // Cash invoice specific fields
    var cash_invoice_date = $('#cash_invoice_date').val();
    var cash_vendor = $('#cash_vendor').val();
    var cash_invoice_number = $('#cash_invoice_number').val();
    var cash_amount = $('#cash_amount').val();
    var cash_entry_date = $('#cash_entry_date').val();
    var cash_invoice_details = $('#cash_invoice_details').val();

    // Validation
    var formIsValid = true;
    var firstInvalidField = null;
    
    // Validation logic...
    function containsSpecialCharacters(value) {
    var regex = /[^a-zA-Z0-9 ]/; // Accepts only alphanumeric characters and spaces
    return regex.test(value.trim());
}

// Clear previous errors
$('.is-invalid').removeClass('is-invalid');
$('.invalid-feedback').remove();

// General validations (for both invoice types)
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

// Validate cash invoice fields if the selected type is cash
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
    $(firstInvalidField).focus(); // Focus the first invalid field
} // Add more validation for PO invoice fields if needed

            // Focus the first invalid field if any
            if (!formIsValid) {
                $(firstInvalidField).focus();
            }

    // Fetch the remaining balance by sending a request if not already fetched
    $.ajax({
        url: '/others/getPoBalance',
        method: 'GET',
        data: {
            _token: csrfToken,
            corporate_po_id: corporate_po_id,
            po_number: po_number
        },
        success: function(response) {
            console.log("Response.result:", response.result);
            console.log("Response.remainingBalance:", response.remainingBalance);

            if (response && response.result === true && response.remainingBalance !== undefined) {
                var remainingBalance = response.remainingBalance;
                $('#remaining_balance').text('Remaining PO Balance: ' + remainingBalance);

                // Now check if the entered value exceeds the remaining balance
                var enteredAmount = parseFloat(invoice_amount);

                console.log("Entered Amount:", enteredAmount);

                if (enteredAmount > remainingBalance) {
                    alert('Insufficient balance. Entered amount exceeds the remaining PO balance!');
                    return; // Prevent further actions if amount exceeds balance
                }

                // Proceed with form submission if everything is valid
                if (formIsValid) {
                    var formData = {
                        _token: csrfToken,
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
                        payment_date: payment_date,
                    };

                    // If it's a cash invoice, add those specific fields
                    if (selectedInvoiceType === 'cash') {
                        // Include cash-specific data if it's a cash invoice
                        formData.cash_invoice_date = cash_invoice_date;
                        formData.cash_vendor = cash_vendor;
                        formData.cash_invoice_number = cash_invoice_number;
                        formData.cash_amount = cash_amount;
                        formData.cash_entry_date = cash_entry_date;
                        formData.cash_invoice_details = cash_invoice_details;
                    }

                    // Send the data via AJAX to save it
                    $.ajax({
                        url: '/others/insertInvoice',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log("Full Response:", response);
                            if (response.success === true) {
                                showToast("success", "Data saved successfully!");
                                window.location.href = 'https://login-users.hygeiaes.com/others/inventory'; // Adjust the URL as needed
                            } else {
                                alert('An error occurred while saving the data.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('An error occurred: ' + error);
                            alert('An error occurred while saving the data.');
                        }
                    });
                }
            } else {
                alert('Unable to fetch PO balance or invalid response.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching PO balance:', error);
            alert('An error occurred while fetching the PO balance.');
        }
    });
});









        // Remove error messages on focus for various fields
        $('#corporate_po_id').on('change', function() {
    // Check if the field is valid
    if ($(this).val().trim() !== '') {
        // Remove the validation error message and style
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    }
});

$('#po_number').on('change', function() {
    if ($(this).val().trim() !== '') {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    }
});

        $('#invoice_date').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#invoice_number').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#invoice_amount').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#entry_date').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#cash_invoice_date').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#cash_invoice_number').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#cash_amount').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#cash_entry_date').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#cash_vendor').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        
    });
    document.querySelectorAll('input[name="invoice_type"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            if (this.value === 'po') {
                document.getElementById('po_invoice_form').style.display = 'block';
                document.getElementById('cash_invoice_form').style.display = 'none';
            } else {
                document.getElementById('po_invoice_form').style.display = 'none';
                document.getElementById('cash_invoice_form').style.display = 'block';
            }
        });
    });
    window.onload = function() {
        document.getElementById('po_invoice').checked = true;
        document.getElementById('po_invoice_form').style.display = 'block';
        document.getElementById('cash_invoice_form').style.display = 'none';
    };
    let vendorData = []; // Initialize an empty array to store vendor data

    $.ajax({
        url: 'https://login-users.hygeiaes.com/others/getVendorDetails',
        method: 'GET',
        success: function(response) {
            // Log the entire response object to see its structure
            console.log('Full Response:', response);

            // Ensure that the response is parsed as a JavaScript object
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response); // Parse the response if it's a string
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    return;
                }
            }

            // Log the response result field
            console.log('Response Result:', response.result);

            // Clear any existing options (including the empty ones)
            $('#corporate_po_id').empty();

            // Add the default option
            $('#corporate_po_id').append('<option value="">-Select Vendor-</option>');

            // Check if the response result is true (or "true" as a string)
            if (response.result === true || response.result === "true") {
                // Check if data exists and is an array
                if (Array.isArray(response.data) && response.data.length > 0) {
                    const vendors = response.data; // The vendors array is inside the 'data' field

                    // Store the vendor data for later use
                    vendorData = vendors;

                    // Loop through each vendor and append to the dropdown
                    vendors.forEach(function(vendor) {
                        console.log('Vendor:', vendor); // Log each vendor to check data

                        const option = $('<option></option>')
                            .val(vendor.corporate_po_id) // Set the value of the option to corporate_po_id
                            .text(vendor.vendor_name); // Set the text of the option to vendor_name

                        // Append the option to the select dropdown
                        $('#corporate_po_id').append(option);
                    });

                } else {
                    console.log('No vendor data found in the response');
                }
            } else {
                console.log('Response result is not true:', response.result);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching vendor data:', error);
        }
    });

    $('#corporate_po_id').change(function() {
        const selectedVendorId = $(this).val();

        if (selectedVendorId) {
            // Find the selected vendor from the stored vendorData array
            const selectedVendor = vendorData.find(vendor => vendor.corporate_po_id == selectedVendorId);
            console.log(selectedVendor);

            // Clear any existing PO number options
            $('#po_number').empty();
            $('#po_number').append('<option value="">Select PO Number</option>');

            if (selectedVendor && selectedVendor.po_number) {
                // Prefill PO numbers based on the selected vendor
                const poNumbers = Array.isArray(selectedVendor.po_number) ? selectedVendor.po_number : [selectedVendor.po_number]; // Ensure po_number is an array
                poNumbers.forEach(function(poNumber) {
                    const poOption = $('<option></option>')
                        .val(poNumber) // Set the value to the PO number
                        .text(poNumber); // Set the text to PO number
                    $('#po_number').append(poOption);
                });
            }
        } else {
            // Clear PO number options if no vendor is selected
            $('#po_number').empty();
            $('#po_number').append('<option value="">Select PO Number</option>');
        }
    });
</script>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
@extends('layouts/layoutMaster')

@section('title', 'Edit Invoice - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss','resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js','resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


@section('content')
<div class="col-12 mb-6">
    <div id="wizardb-validation" class="bs-stepper mt-2">
       

        <div class="bs-stepper-content">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" class="btn btn-primary" id="back-to-list" onclick="window.location.href='/others/invoice'" style="margin-right: 20px;">Back to Invoice</button>
            </div>
            <form id="wizard-validation-form">
                <div class="row g-6">
                    <div class="col-sm-6">
                        <label for="invoice_type" class="form-label">
                            <h5>Invoice Type</h5>
                        </label>
                        <div style="color: #4444e5;">
                            <input type="radio" id="cash_invoice" name="invoice_type" value="cash"  style="color:blue;" checked>&nbsp; Cash Invoice&nbsp;
                            <input type="radio" id="po_invoice" name="invoice_type" value="po">&nbsp;PO Invoice
                        </div>
                    </div>
                </div>

                <br />
              

                <h5>Invoice Details</h5>
                <!-- PO Invoice Fields (Default) -->
                <div id="po_invoice_form" class="content">

                    <div class="row g-6">
                    <div class="col-sm-6">
                    <strong>Vendor : </strong> <span id="vendor_name_edit"></span>
                          </div>
                        <div class="col-sm-6">
                        <strong>Po Number : </strong> <span id="ponumber_edit"></span>
                            </div>
                       
                        <div class="col-sm-6">
                            <label for="invoice_date" class="form-label">Invoice Date</label>
                            <input type="date" id="invoice_date" name="invoice_date" class="form-control" required>
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
                            <input type="date" id="ohc_verify_date" name="  " class="form-control">
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
                            <label for="head_verify_date" class="form-label">Dept.Head Verification Date</label>
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
        $("#invoice_date, #entry_date, #ohc_verify_date, #hr_verify_date, #ses_release_date, #submission_date, #payment_date, #cash_invoice_date, #cash_entry_date,#ses_date,#head_verify_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today"
    });
    function formatDate(dateString) {
        if (dateString) {
            var dateParts = dateString.split('-'); // Split 'Y-m-d' format
            return dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]; // Convert to 'd-m-yyyy'
        }
        return ''; // Return empty if no date exists
    }
    $('#vendor_name_edit').text("{{ $invoice['vendor_name'] ?? '' }} ");
    $('#ponumber_edit').text("{{ $invoice['po_number'] ?? '' }} ");
    // Set formatted values for the input fields
    var cashInvoiceDate = "{{ $invoice['invoice_date'] ?? '' }}";
    if (cashInvoiceDate) {
        $('#cash_invoice_date').val(formatDate(cashInvoiceDate)); // Set formatted value in cash invoice date input
    }

    var cashEntryDate = "{{ $invoice['entry_date'] ?? '' }}";
    if (cashEntryDate) {
        $('#cash_entry_date').val(formatDate(cashEntryDate)); // Set formatted value in cash entry date input
    }

    var invoiceDate = "{{ $invoice['invoice_date'] ?? '' }}";
    if (invoiceDate) {
        $('#invoice_date').val(formatDate(invoiceDate)); // Set formatted value in invoice date input
    }

    var entryDate = "{{ $invoice['entry_date'] ?? '' }}";
    if (entryDate) {
        $('#entry_date').val(formatDate(entryDate)); // Set formatted value in entry date input
    }

    var ohcVerifyDate = "{{ $invoice['ohc_verify_date'] ?? '' }}";
    if (ohcVerifyDate) {
        $('#ohc_verify_date').val(formatDate(ohcVerifyDate)); // Set formatted value in OHC verify date input
    }

    var hrVerifyDate = "{{ $invoice['hr_verify_date'] ?? '' }}";
    if (hrVerifyDate) {
        $('#hr_verify_date').val(formatDate(hrVerifyDate)); // Set formatted value in HR verify date input
    }

    var sesDate = "{{ $invoice['ses_date'] ?? '' }}";
    if (sesDate) {
        $('#ses_date').val(formatDate(sesDate)); // Set formatted value in SES date input
    }

    var headVerifyDate = "{{ $invoice['head_verify_date'] ?? '' }}";
    if (headVerifyDate) {
        $('#head_verify_date').val(formatDate(headVerifyDate)); // Set formatted value in head verify date input
    }

    var sesReleaseDate = "{{ $invoice['ses_release_date'] ?? '' }}";
    if (sesReleaseDate) {
        $('#ses_release_date').val(formatDate(sesReleaseDate)); // Set formatted value in SES release date input
    }

    var submissionDate = "{{ $invoice['submission_date'] ?? '' }}";
    if (submissionDate) {
        $('#submission_date').val(formatDate(submissionDate)); // Set formatted value in submission date input
    }

    var paymentDate = "{{ $invoice['payment_date'] ?? '' }}";
    if (paymentDate) {
        $('#payment_date').val(formatDate(paymentDate)); // Set formatted value in payment date input
    }
    

    $('input[name="invoice_type"]').change(function() {
        if (this.value === 'po') {
            $('#po_invoice_form').show();
            $('#cash_invoice_form').hide();
        } else {
            $('#po_invoice_form').hide();
            $('#cash_invoice_form').show();
        }
    }); 
    $('input[name="invoice_type"]').prop('checked', false);

var corporatePoId = "{{ $invoice['corporate_po_id'] ?? '' }}";  // Check if corporate_po_id exists
console.log('corporatePoId:', corporatePoId);

let invoiceType = corporatePoId && corporatePoId.trim() !== '' ? 'po' : 'cash';

$('#' + invoiceType + '_invoice').prop('checked', true).trigger('change');


    $('#invoice_number').val("{{ $invoice['invoice_number'] ?? '' }}");
    $('#invoice_amount').val("{{ $invoice['invoice_amount'] ?? '' }}");
   
    $('#ses_number').val("{{ $invoice['ses_number'] ?? '' }}");
    
   
    
    $('#cash_invoice_number').val("{{ $invoice['invoice_number'] ?? '' }}");
    $('#cash_amount').val("{{ $invoice['invoice_amount'] ?? '' }}");
   
    $('#cash_vendor').val("{{ $invoice['cash_vendor'] ?? '' }}");
    $('#cash_invoice_details').val("{{ $invoice['cash_invoice_details'] ?? '' }}");
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
   
    $('#add_invoice').click(function(event) {
    event.preventDefault();
    var selectedInvoiceType = $('input[name="invoice_type"]:checked').val();
    console.log("Selected Invoice Type: " + selectedInvoiceType);
    // Fetch all form values
    //var corporate_po_id = $('#corporate_po_id').val();
    //var po_number = $('#po_number').val();
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

    var formIsValid = true;
    var firstInvalidField = null;

    function containsSpecialCharacters(value) {
        var regex = /[^a-zA-Z0-9 ]/; // Accepts only alphanumeric characters and spaces
        return regex.test(value.trim());
    }

    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    // General validations (for both invoice types)
    

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
    // Log the values
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

    // No need for return false here, we want to continue executing
}

if (!formIsValid) {
    console.log('Form is invalid, first invalid field:', firstInvalidField);
    $(firstInvalidField).focus();
}

    // If all validations pass, send the form data via AJAX
    if (formIsValid) {
       
        var formData = {
            _token: csrfToken, // Assuming csrfToken is defined elsewhere in your script
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
           // alert('AM Here');
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
        // AJAX request to save the data
        $.ajax({
            url: requestUrl,
            method: 'POST',
            data: formData,
            success: function(response) {
    console.log("Full Response:", response);
    console.log("Type of response:", typeof response);

    if (response.result === true) {
        // Success Toast
        showToast("success", "Data saved successfully!");

        console.log('Redirecting to:', 'https://login-users.hygeiaes.com/others/invoice');
        window.location.href = 'https://login-users.hygeiaes.com/others/invoice';
    } else {
        // If the error response contains a 'details' field with a JSON string
        if (response.details) {
            try {
                // Parse the details field to extract the error message
                var details = JSON.parse(response.details);
                if (details.message) {
                    showToast("error", details.message);  // Display the error message in a toast
                } else {
                    showToast("error", "An error occurred while saving the data.");
                }
            } catch (e) {
                // Handle cases where JSON parsing fails (invalid JSON)
                showToast("error", "An error occurred while saving the data.");
            }
        } else {
            showToast("error", response.message || "An error occurred while saving the data.");
        }
    }
},
error: function(xhr, status, error) {
    console.error('An error occurred: ' + error);

    // Check if the error response has a message and details
    if (xhr.responseJSON && xhr.responseJSON.details) {
        try {
            var details = JSON.parse(xhr.responseJSON.details);
            showToast("error", details.message || "An error occurred while saving the data.");
        } catch (e) {
            showToast("error", "An error occurred while saving the data.");
        }
    } else {
        showToast("error", "An error occurred while saving the data.");
    }
}

        });
    }
});
var fields = [
    '#invoice_date', '#invoice_number', '#invoice_amount', '#entry_date',
    '#cash_invoice_date', '#cash_invoice_number', '#cash_amount', '#cash_entry_date',
    '#cash_vendor'
];

// Attach the event listener for each field
fields.forEach(function(field) {
    $(field).on('focus', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove(); // Remove error message
    });
});
    });
   
   

</script>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
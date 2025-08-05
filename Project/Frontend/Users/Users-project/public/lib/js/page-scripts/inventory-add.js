
    $(document).ready(function() {
        // Output the URL to check if it's correct
      
        $("#date, #manufacture_date, #calibrated_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today"
    });
        // CSRF Token
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#add-drugtype').click(function() {
    // Fetch the input values from the form
    var date = $('#date').val();
    var purchaseOrder = $('#purchase_order').val();
    var equipmentName = $('#equipment_name').val();
    var equipmentCode = $('#equipment_code').val();
    var equipmentLifetime = $('#equipment_lifetime').val();
    var manufacturerName = $('#manufacturers').val();
    var manufactureDate = $('#manufacture_date').val();
    var equipment_cost = $('#equipment_cost').val();
    var vendors = $('#vendors').val();
    var calibratedDate = $('#calibrated_date').val();
   
    var formIsValid = true;

    // Helper function to validate special characters
    function containsSpecialCharacters(value) {
        var regex = /[^a-zA-Z0-9 ]/; // Accepts only alphanumeric characters and spaces
        return regex.test(value);
    }

    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    // Validate all form fields
    if (date.trim() === '') {
        formIsValid = false;
        $('#date').addClass('is-invalid');
        $('#date').after('<div class="invalid-feedback">The Purchase Date is required.</div>');
    }

    if (purchaseOrder.trim() === '') {
        formIsValid = false;
        $('#purchase_order').addClass('is-invalid');
        $('#purchase_order').after('<div class="invalid-feedback">The Purchase Order is required.</div>');
    }

    if (equipmentName.trim() === '') {
        formIsValid = false;
        $('#equipment_name').addClass('is-invalid');
        $('#equipment_name').after('<div class="invalid-feedback">The Equipment Name is required.</div>');
    } else if (containsSpecialCharacters(equipmentName)) {
        formIsValid = false;
        $('#equipment_name').addClass('is-invalid');
        $('#equipment_name').after('<div class="invalid-feedback">The Equipment Name should not contain special characters.</div>');
    }

    if (equipmentCode.trim() === '') {
        formIsValid = false;
        $('#equipment_code').addClass('is-invalid');
        $('#equipment_code').after('<div class="invalid-feedback">The Equipment Code is required.</div>');
    } else if (containsSpecialCharacters(equipmentCode)) {
        formIsValid = false;
        $('#equipment_code').addClass('is-invalid');
        $('#equipment_code').after('<div class="invalid-feedback">The Equipment Code should not contain special characters.</div>');
    }

    if (equipmentLifetime.trim() === '') {
        formIsValid = false;
        $('#equipment_lifetime').addClass('is-invalid');
        $('#equipment_lifetime').after('<div class="invalid-feedback">The Equipment Lifetime is required.</div>');
    }

   

    if (manufacturerName.trim() === '') {
        formIsValid = false;
        $('#manufacturers').addClass('is-invalid');
        $('#manufacturers').after('<div class="invalid-feedback">The Manufacturer Name is required.</div>');
    } else if (containsSpecialCharacters(manufacturerName)) {
        formIsValid = false;
        $('#manufacturers').addClass('is-invalid');
        $('#manufacturers').after('<div class="invalid-feedback">The Manufacturer Name should not contain special characters.</div>');
    }

    if (manufactureDate.trim() === '') {
        formIsValid = false;
        $('#manufacture_date').addClass('is-invalid');
        $('#manufacture_date').after('<div class="invalid-feedback">The Manufacturer Date is required.</div>');
    }

    if (equipment_cost.trim() === '' || isNaN(equipment_cost) || equipment_cost <= 0) {
        formIsValid = false;
        $('#equipment_cost').addClass('is-invalid');
        $('#equipment_cost').after('<div class="invalid-feedback">The Equipment Cost is required and must be a positive number.</div>');
    }

    // Validation for vendor field: Should not contain special characters
    if (vendors.trim() === '') {
        formIsValid = false;
        $('#vendors').addClass('is-invalid');
        $('#vendors').after('<div class="invalid-feedback">The Vendor is required.</div>');
    } else if (containsSpecialCharacters(vendors)) {
        formIsValid = false;
        $('#vendors').addClass('is-invalid');
        $('#vendors').after('<div class="invalid-feedback">The Vendor should not contain special characters.</div>');
    }

    if (calibratedDate.trim() === '') {
        formIsValid = false;
        $('#calibrated_date').addClass('is-invalid');
        $('#calibrated_date').after('<div class="invalid-feedback">The Calibration Date is required.</div>');
    }

   

    // If all validations pass, send the form data via AJAX
    if (formIsValid) {
        var formData = {
            _token: csrfToken, // Assuming csrfToken is defined elsewhere in your script
            date: date,
            purchase_order: purchaseOrder,
            equipment_name: equipmentName,
            equipment_code: equipmentCode,
            equipment_lifetime: equipmentLifetime,
            manufacturers: manufacturerName,
            manufacture_date: manufactureDate,
            equipment_cost: equipment_cost,
            vendors: vendors,
            calibrated_date: calibratedDate
            
        };

        // AJAX request to save the data
        $.ajax({
            url: "{{ route('Others.store') }}", // Adjust the route if needed
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
});



        // Remove error messages on focus for various fields
$('#date').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#purchase_order').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#equipment_name').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#equipment_code').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#equipment_lifetime').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});


$('#manufacturers').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#manufacture_date').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#equipment_cost').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#vendors').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#calibrated_date').on('focus', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});


    });

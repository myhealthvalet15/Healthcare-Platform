$(document).ready(function () {
    $("#date, #manufacture_date, #calibrated_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today"
    });
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('#add-drugtype').click(function () {
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
        function containsSpecialCharacters(value) {
            var regex = /[^a-zA-Z0-9 ]/;
            return regex.test(value);
        }
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
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
        if (formIsValid) {
            var formData = {
                _token: csrfToken,
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
            apiRequest({
                url: "/others/store",
                method: "POST",
                data: formData,
                onSuccess: function (response) {
                    console.log("Full Response:", response);
                    if (response.success === true) {
                        showToast("success", "Data saved successfully!");
                        window.location.href = 'https://login-users.hygeiaes.com/others/inventory';
                    } else {
                        alert('An error occurred while saving the data.');
                    }
                },
                onError: function (error) {
                    console.error('An error occurred: ' + error);
                    alert('An error occurred while saving the data.');
                }
            });

        }
    });
    $('#date').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#purchase_order').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#equipment_name').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#equipment_code').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#equipment_lifetime').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#manufacturers').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#manufacture_date').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#equipment_cost').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#vendors').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#calibrated_date').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});

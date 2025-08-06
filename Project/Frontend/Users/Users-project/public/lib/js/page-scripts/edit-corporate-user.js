$(document).ready(function () {


    $('#first_name').val("{{ $corporateuser['first_name'] ?? '' }} ");
    $('#last_name').val("{{ $corporateuser['last_name'] ?? '' }} ");
    $('#email').val("{{ $corporateuser['email'] ?? '' }} ");
    $('#mobile_country_code').val("{{ $corporateuser['mobile_country_code'] ?? '' }} ");
    $('#mobile_num').val("{{ $corporateuser['mobile_num'] ?? '' }} ");

    $('#aadhar').val("{{ $corporateuser['aadhar'] ?? '' }} ");
    var gender = "{{ $corporateuser['gender'] ?? '' }}";
    var setting = "{{ $corporateuser['setting'] ?? '' }}";
    if (gender == 'Male') {
        $('#Male').attr('checked', 'checked');
    } else if (gender == 'Female') {
        $('#Female').attr('checked', 'checked');
    } else if (gender == 'Others') {
        $('#Others').attr('checked', 'checked');
    } else {
        $('input[name="gender"]').prop('checked', false);
    }

    if (setting == '1') {
        $('#setting_ohc').attr('checked', 'checked');
    } else if (setting == '2') {
        $('#setting_mhc').attr('checked', 'checked');
    } else if (setting == '1,2') {
        $('#setting_ohc').attr('checked', 'checked');
        $('#setting_mhc').attr('checked', 'checked');
    } else {
        $('input[name="setting"]').prop('checked', false);
    }

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: "{{ route('get-departments-hl1') }}", // The route you defined
        method: 'GET',
        success: function (response) {
            // console.log(response.data); // Log the response to inspect
            if (response.data) {

                // Populate the department select box
                var departmentSelect = $('#department');
                departmentSelect.empty();
                response.data.forEach(function (departmentname) {
                    departmentSelect.append(new Option(departmentname.hl1_name,
                        departmentname.hl1_id)); // Correct field name
                });
                var selectedDepartments = "{{ $corporateuser['department'] ?? '' }}";
                // select the options
                $('#department-select option').each(function () {
                    if (selectedDepartments.includes(parseInt($(this).val()))) {
                        $(this).prop('selected', true);
                    }
                });

                // check if all options are selected
                var allSelected = $('#department-select option').length === $(
                    '#department-select option:selected').length;
                if (allSelected) {
                    $('#department-select option[value="select-all"]').prop('selected', true);
                }
                // add event listener for select box change
                $('#department').on('change', function () {
                    var selectedValues = $(this).val();
                    if (selectedValues && selectedValues.includes('select-all')) {
                        // select all options
                        $(this).find('option').prop('selected', function () {
                            return $(this).val() !== 'select-all';
                        });
                    } else if (selectedValues && selectedValues.length === departments
                        .length) {
                        // add select-all option if all departments are selected
                        $(this).find('option[value="select-all"]').prop('selected',
                            true);
                    } else {
                        // remove select-all option if not all departments are selected
                        $(this).find('option[value="select-all"]').prop('selected',
                            false);
                    }
                    // trigger change event to update the select box
                    $(this).trigger('change');
                });

            } else {
                console.error('No department found');
            }
        },
        error: function (xhr, status, error) {
            console.error('An error occurred: ' + error);
        }
    });

});
$('#add-corporate-user').click(function () {
    // CSRF Token
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Fetch the input values from the form
    var first_name = $('#first_name').val();
    var last_name = $('#last_name').val();
    var email = $('#email').val();
    var mobile_country_code = $('#mobile_country_code').val();

    var mobile_num = $('#mobile_num').val();
    var department = $('#department').val();
    var aadhar = $('#aadhar').val();
    var gender = $('input[name="gender"]:checked').val();

    var setting = $('input[type="checkbox"]:checked').map(function (_, el) {
        return $(el).val();
    }).get();
    if (Array.isArray(department)) {
        // It’s already an array, no need to split.
        console.log('department is an array:', department);
    } else {
        // If it's not an array for any reason, fallback to an empty array.
        department = [];
    }

    var formIsValid = true;


    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    // Validate all form fields


    if (first_name.trim() === '') {
        formIsValid = false;
        $('#first_name').addClass('is-invalid');
        $('#first_name').after('<div class="invalid-feedback">The First Name is required.</div>');
    }

    if (last_name.trim() === '') {
        formIsValid = false;
        $('#last_name').addClass('is-invalid');
        $('#last_name').after('<div class="invalid-feedback">The Last Name is required.</div>');
    }

    if (email.trim() === '') {
        formIsValid = false;
        $('#email').addClass('is-invalid');
        $('#email').after('<div class="invalid-feedback">Email is required.</div>');
    }

    if (mobile_num.trim() === '') {
        formIsValid = false;
        $('#mobile_num').addClass('is-invalid');
        $('#mobile_num').after('<div class="invalid-feedback">The Mobile Number is required.</div>');
    }



    // If all validations pass, send the form data via AJAX
    if (formIsValid) {
        var formData = {
            _token: csrfToken, // Assuming csrfToken is defined elsewhere in your script
            first_name: first_name,
            last_name: last_name,
            email: email,
            mobile_country_code: mobile_country_code,
            mobile_num: mobile_num,
            department: department,
            aadhar: aadhar,
            gender: gender,
            setting: setting,

        };
        var currentUrl = window.location.pathname;
        var userId = currentUrl.split('/').pop();
        var requestUrl = '/corporate-users/updateUser/' + userId;
        // AJAX request to update the data
        $.ajax({
            url: requestUrl,
            method: 'POST',
            data: formData,
            success: function (response) {
                console.log("response:", response.result);
                if (response.result === true) {
                    showToast("success", "Data saved successfully!");
                    window.location.href =
                        'https://login-users.hygeiaes.com/corporate-users/users-list'; // Adjust the URL as needed
                } else {
                    alert('An error occurred while saving the data.');
                }
            },
            error: function (xhr, status, error) {
                console.error('An error occurred: ' + error);
                alert('An error occurred while saving the data.');
            }
        });
    }
});



// Remove error messages on focus for various fields
$('#first_name').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#last_name').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#email').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#mobile_country_code').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#mobile_num').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});


$('#department').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#aadhar').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('[name="gender"]').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

$('#setting').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove(); // Remove error message
});

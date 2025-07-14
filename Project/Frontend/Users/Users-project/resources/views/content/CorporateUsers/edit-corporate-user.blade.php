@extends('layouts/layoutMaster')

@section('title', 'Edit Corporate User - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss','resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js','resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@section('content')
<div class="col-12 mb-6">
    <div id="wizardb-validation" class="bs-stepper mt-2">

        <div class="bs-stepper-content">
            <form id="wizard-validation-form" method="post">
                <!-- Account Details -->
                <div id="account-details-validation" class="content"
                    style="display:block;">
                    <div
                        style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                        <button type="button" class="btn btn-primary"
                            id="back-to-list"
                            onclick="window.location.href='/corporate-users/users-list'"
                            style="margin-right: 20px;">Back to User
                            List</button>
                    </div>
                    <div class="row g-6">
                        <div class="col-md-6">
                            <label class="form-label" for="first_name">First
                                Name</label>
                            <input type="text" id="first_name" name="first_name"
                                class="form-control"
                                placeholder="John" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="last_name">Last
                                Name</label>
                            <input type="text" id="last_name" name="last_name"
                                class="form-control" placeholder="Doe" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group input-group-merge">
                                <input type="email" id="email" name="email"
                                    class="form-control" placeholder="john.doe"
                                    aria-label="john.doe"
                                    aria-describedby="email" />
                                <span class="input-group-text"
                                    id="email">@example.com</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"
                                for="mobile_num">Mobile</label>
                            <div class="d-flex">
                                <div class="me-2" style="flex: 0 0 100px;">
                                    <input type="text"
                                        name="mobile_country_code"
                                        id="mobile_country_code"
                                        class="form-control"
                                        placeholder="+91" />
                                </div>
                                <div style="flex: 1;">
                                    <input type="text" name="mobile_num"
                                        id="mobile_num" class="form-control"
                                        placeholder="Mobile no." />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 select2-primary">
                            <label class="form-label"
                                for="department">Department</label>
                                <select id="department" name="department[]" class="form-control select2" multiple>
                                <option value="select-all" >All Departments</option>
                                <!-- Options will be added dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="aadhar">Aadhar
                                No</label>

                            <input type="text" id="aadhar" name="aadhar"
                                class="form-control"
                                placeholder="XXXX XXXX XXXX"
                                aria-label="XXXX XXXX XXXX"
                                pattern="[0-9]{4} [0-9]{4} [0-9]{4}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-check-label">Gender</label>
                            <div class="col mt-2">
                                <div class="form-check form-check-inline">
                                    <input name="gender"
                                        class="form-check-input" type="radio"
                                        value="Male" id="Male"
                                        checked />
                                    <label class="form-check-label"
                                        for="Male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="gender"
                                        class="form-check-input" type="radio"
                                        value="Female"
                                        id="Female" />
                                    <label class="form-check-label"
                                        for="Female">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="gender"
                                        class="form-check-input" type="radio"
                                        value="Others"
                                        id="Others" />
                                    <label class="form-check-label"
                                        for="Others">Others</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label">Settings</label>
                            <div class="col mt-2">
                                <div class="form-check form-check-inline">
                                    <input name="setting"
                                        class="form-check-input" type="checkbox"
                                        value="1"
                                        id="setting_ohc" />
                                    <label class="form-check-label"
                                        for="setting_ohc">
                                        OHC
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="setting"
                                        class="form-check-input" type="checkbox"
                                        value="2"
                                        id="setting_mhc" />
                                    <label class="form-check-label"
                                        for="setting_mhc">
                                        MHC
                                    </label>
                                </div>

                            </div>

                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary"
                                    id="add-corporate-user">Save </button>
                                <button
                                    onclick="window.location.href='/corporate-users/users-list'"
                                    class="btn btn-label-danger waves-effect"
                                    data-bs-dismiss="offcanvas">Cancel</button>

                            </div>

                        </div>
                        <br /><br />
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
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
$('#department-select option').each(function() {
    if (selectedDepartments.includes(parseInt($(this).val()))) {
        $(this).prop('selected', true);
    }
});

// check if all options are selected
var allSelected = $('#department-select option').length === $('#department-select option:selected').length;
if (allSelected) {
    $('#department-select option[value="select-all"]').prop('selected', true);
}
                          // add event listener for select box change
$('#department').on('change', function() {
    var selectedValues = $(this).val();
    if (selectedValues && selectedValues.includes('select-all')) {
        // select all options
        $(this).find('option').prop('selected', function() {
            return $(this).val() !== 'select-all';
        });
    } else if (selectedValues && selectedValues.length === departments.length) {
        // add select-all option if all departments are selected
        $(this).find('option[value="select-all"]').prop('selected', true);
    } else {
        // remove select-all option if not all departments are selected
        $(this).find('option[value="select-all"]').prop('selected', false);
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
                        window.location.href = 'https://login-users.hygeiaes.com/corporate-users/users-list'; // Adjust the URL as needed
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





</script>

    @endsection
    <meta name="csrf-token" content="{{ csrf_token() }}">
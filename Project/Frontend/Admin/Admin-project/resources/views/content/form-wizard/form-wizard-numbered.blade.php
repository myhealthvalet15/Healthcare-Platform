@extends('layouts/layoutMaster')
@section('title', 'Wizard Numbered - Forms')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js',
'resources/assets/js/form-wizard-validation.js'
])
@endsection
@section('content')
<div class="row">
    <div class="col-12 mb-6">
        <!-- <small class="text-light fw-medium">Validation</small> -->
        <div id="wizard-validation" class="bs-stepper mt-2">
            <div class="bs-stepper-header">
                <div class="step" data-target="#account-details-validation">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">1</span>
                        <span class="bs-stepper-label mt-1">
                            <span class="bs-stepper-title">Corporate Details</span>
                            <span class="bs-stepper-subtitle">Setup Corporate Details</span>
                        </span>
                    </button>
                </div>
                <div class="line">
                    <i class="ti ti-chevron-right"></i>
                </div>
                <div class="step" data-target="#personal-info-validation">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">2</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Corporate Address</span>
                            <span class="bs-stepper-subtitle">Add Address info</span>
                        </span>
                    </button>
                </div>
                <div class="line">
                    <i class="ti ti-chevron-right"></i>
                </div>
                <div class="step" data-target="#social-links-validation">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">3</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Employee Type</span>
                            <span class="bs-stepper-subtitle">Add Employee Types</span>
                        </span>
                    </button>
                </div>
                <div class="line">
                    <i class="ti ti-chevron-right"></i>
                </div>
                <div class="step" data-target="#components-details-validation">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">4</span>
                        <span class="bs-stepper-label mt-1">
                            <span class="bs-stepper-title">Components</span>
                            <span class="bs-stepper-subtitle">Setup Components Details</span>
                        </span>
                    </button>
                </div>
                <div class="line">
                    <i class="ti ti-chevron-right"></i>
                </div>
                <div class="step" data-target="#adminuser-details-validation">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">5</span>
                        <span class="bs-stepper-label mt-1">
                            <span class="bs-stepper-title">Corporate Admin User</span>
                            <span class="bs-stepper-subtitle">Corporate Admin User Details</span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="bs-stepper-content">
                <form id="wizard-validation-form">
                    <div id="account-details-validation" class="content">
                        <div class="content-header mb-4">
                            <h6 class="mb-0">Corporate Details</h6>
                            <small>Enter Your Corporate Details</small>
                        </div>
                        <div class="row g-6">
                            <div class="col-sm-6">
                                <label for="corporate_name" class="form-label">Corporate Name</label>
                                <input type="text" id="corporate_name" name="corporate_name" class="form-control"
                                    placeholder="Enter the Corporate Name" required>
                            </div>
                            <input type="hidden" id="corporate_id" name="corporate_id"
                                class="form-control rounded-pill bg-light"
                                value="{{ htmlspecialchars($userIdsString ?? '') }}" readonly>
                            <input type="hidden" id="location_id" name="location_id"
                                class="form-control rounded-pill bg-light"
                                value="{{ htmlspecialchars($userIdsString ?? '') }}" readonly>
                            <div class="col-sm-6">
                                <label for="corporate_no" class="form-label">Corporate Number</label>
                                <input type="text" id="corporate_no" name="corporate_no" class="form-control"
                                    placeholder="Enter the Corporate Number">
                            </div>
                            <div class="col-sm-6">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" id="display_name" name="display_name" class="form-control"
                                    placeholder="Enter Display Name">
                            </div>
                            <div class="col-sm-6">
                                <label for="registration_no" class="form-label">Corporate Registration No</label>
                                <input type="text" id="registration_no" name="registration_no" class="form-control"
                                    placeholder="Enter Registration Number">
                            </div>
                            <div class="col-sm-6">
                                <label for="company_profile" class="form-label">Company Profile</label>
                                <input id="company_profile" name="company_profile" class="form-control"
                                    placeholder="Enter company profile details">
                            </div>
                            <div class="col-sm-6">
                                <label for="prof_image" class="form-label">Profile Image</label>
                                <input type="file" id="prof_image" name="prof_image" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="industry" class="form-label">Industry</label>
                                <input id="industry" name="industry" class="form-control" placeholder="Enter Industry">
                            </div>
                            <div class="col-sm-6">
                                <label for="industry_segment" class="form-label">Industry Segment</label>
                                <input type="text" id="industry_segment" name="industry_segment" class="form-control"
                                    placeholder="Enter Industry Segment">
                            </div>
                            <input type="hidden" id="created_by" name="created_by" class="form-control" value="mhvadmin"
                                placeholder="Enter creator's name">
                            <div class="col-md-6">
                                <label for="gstin" class="form-label fw-bold">Gstin</label>
                                <input type="text" id="gstin" name="gstin" class="form-control"
                                    placeholder="Enter Gstin">
                            </div>
                            <div class="col-md-6">
                                <label for="discount" class="form-label fw-bold">Discount</label>
                                <input type="text" id="discount" name="discount" class="form-control"
                                    placeholder="Enter discount">
                            </div>
                            <div class="col-md-6">
                                <label for="valid_from" class="form-label fw-bold">Valid From</label>
                                <input type="date" id="valid_from" name="valid_from" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="valid_upto" class="form-label fw-bold">Valid Upto</label>
                                <input type="date" id="valid_upto" name="valid_upto" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="corporate_color" class="form-label fw-bold">Corporate Color</label>
                                <input type="color" id="corporate_color" name="corporate_color" class="form-control">
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <label for="active_status" class="form-label fw-bold me-3">Active Status:</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="active" name="active_status" class="form-check-input"
                                        value="1" checked>
                                    <label class="form-check-label" for="active">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="inactive" name="active_status" class="form-check-input"
                                        value="0">
                                    <label class="form-check-label" for="inactive">Inactive</label>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-label-secondary btn-prev" disabled> <i
                                        class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-primary btn-next"> <span
                                        class="align-middle d-sm-inline-block d-none me-sm-2">Next</span> <i
                                        class="ti ti-arrow-right ti-xs"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Personal Info -->
                    <div id="personal-info-validation" class="content">
                        <div class="content-header mb-4">
                            <h6 class="mb-0">Corporate Address</h6>
                            <small>Enter Your Corporate Address.</small>
                        </div>
                        <div class="row g-6">
                            <div class="col-md-6">
                                <label for="nameid" class="form-label font-weight-bold">Pincode</label>
                                <select id="formValidationCountry" name="country_filter"
                                    class="select2 form-control border rounded" aria-label="Select a pincode">
                                </select>
                            </div>
                            <div class="col-sm-6" id="Area">
                                <label for="areaoptions" class="form-label font-weight-bold">Select Area</label>
                                <select id="areaoptions" class="select2">
                                </select>
                            </div>
                            <div class="col-sm-6" id="City" style="display: none;">
                                <label for="cityoptions" class="form-label font-weight-bold">Search by City</label>
                                <select id="cityoptions" class="select2">
                                </select>
                            </div>
                            <div class="col-sm-6" id="State" style="display: none;">
                                <label for="stateoptions" class="form-label font-weight-bold">Search by State</label>
                                <select id="stateoptions" class="select2">
                                </select>
                            </div>
                            <div class="col-sm-6" id="Country" style="display: none;">
                                <label for="countryoptions" class="form-label font-weight-bold">Country</label>
                                <select id="countryoptions" class="select2">
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="latitude" class="form-label font-weight-bold">Latitude</label>
                                <input type="text" id="latitude" name="latitude" class="form-control"
                                    placeholder="Latitude">
                            </div>
                            <div class="col-sm-6">
                                <label for="longitude" class="form-label font-weight-bold">Longitude</label>
                                <input type="text" id="longitude" name="longitude" class="form-control"
                                    placeholder="Longitude">
                            </div>
                            <div class="col-sm-6">
                                <label for="website_link" class="form-label font-weight-bold">website link</label>
                                <input type="text" id="website_link" name="website_link" class="form-control"
                                    placeholder="Longitude">
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-label-secondary btn-prev"> <i
                                        class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-primary btn-next"> <span
                                        class="align-middle d-sm-inline-block d-none me-sm-2">Next</span> <i
                                        class="ti ti-arrow-right ti-xs"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Social Links -->
                    <div id="social-links-validation" class="content">
                        <div class="content-header mb-4">
                            <h6 class="mb-0">Employee Type</h6>
                            <small class="text-muted">Enter Your Employee Type.</small>
                        </div>
                        <div class="row g-3" id="employee-type-container">
                            <!-- Default Field -->
                            <div class="col-md-7 employee-type-fields">
                                <label for="employee_type_name_1" class="form-label font-weight-bold">Employee Type
                                    Name</label>
                                <input type="text" id="employee_type_name_1" name="employee_type_name[]"
                                    class="form-control border rounded" placeholder="Employee Type Name 1">
                            </div>
                            <div class="col-md-5 d-flex align-items-center">
                                <button type="button" id="add-employee-type" class="btn btn-success btn-sm">
                                    <i class="ti ti-plus me-1"></i> Add
                                </button>
                            </div>
                            <!-- Dynamic Fields Will Be Added Here -->
                            <div class="col-md-12 mt-3" id="dynamic-fields-container"></div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-label-secondary btn-prev">
                                    <i class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-primary btn-next">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                                    <i class="ti ti-arrow-right ti-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="components-details-validation" class="content">
                        <div class="module-container">
                            <div class="module-list">
                                @foreach($modules as $module)
                                <div class="module mb-4" id="module-{{ $module['module_id'] }}">
                                    <!-- Module checkbox that will toggle submodules -->
                                    <div class="module-header p-2">
                                        <input type="checkbox" class="module-checkbox me-2" name="module_id[]"
                                            value="{{ $module['module_id'] }}" id="module-{{ $module['module_id'] }}">
                                        <label for="module-{{ $module['module_id'] }}">
                                            <strong>{{ $module['module_name'] }}</strong>
                                        </label>
                                    </div>
                                    <!-- Submodules Container (Initially Hidden) -->
                                    <div class="submodule-container" id="submodules-{{ $module['module_id'] }}"
                                        style="display: none; margin-left: 20px;">
                                        <div class="submodule-content">
                                            @foreach($module['sub_modules'] as $subModule)
                                            <label class="d-flex align-items-center mb-2">
                                                <input type="checkbox" class="submodule-checkbox me-2"
                                                    name="sub_module_id[{{ $module['module_id'] }}][]"
                                                    value="{{ $subModule['sub_module_id'] }}">
                                                {{ $subModule['sub_module_name'] }}
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between">
                            <button class="btn btn-label-secondary btn-prev"> <i
                                    class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                            </button>
                            <button class="btn btn-primary btn-next"> <span
                                    class="align-middle d-sm-inline-block d-none me-sm-2">Next</span> <i
                                    class="ti ti-arrow-right ti-xs"></i></button>
                        </div>
                    </div>
                    <div id="adminuser-details-validation" class="content">
                        <div class="content-header mb-4">
                            <h6 class="mb-0">Corporate Super A Details</h6>
                            <small>Enter Your Corporate Details</small>
                        </div>
                        <div class="row g-6">
                            <input type="hidden" id="corporate_admin_user_id" name="corporate_admin_user_id"
                                class="form-control" value="{{ htmlspecialchars($corporateIdsString) }}" readonly>
                            <div class="col-sm-6">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="dob">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob">
                            </div>
                            <div class="col-sm-6">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="mobile_country_code">Mobile Country Code</label>
                                <input type="text" class="form-control" id="mobile_country_code"
                                    name="mobile_country_code">
                            </div>
                            <div class="col-sm-6">
                                <label for="mobile_num">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile_num" name="mobile_num">
                            </div>
                            <div class="col-sm-6">
                                <label for="aadhar">Aadhar</label>
                                <input type="text" class="form-control" id="aadhar" name="aadhar">
                            </div>
                            <div class="col-sm-6">
                                <label for="age">Age</label>
                                <input type="number" class="form-control" id="age" name="age">
                            </div>
                            <div class="col-sm-6">
                                <label for="active_status">Active Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="active_status_active"
                                        name="corp_active_status" value="1" checked>
                                    <label class="form-check-label" for="active_status_active">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="active_status_inactive"
                                        name="active_status" value="0">
                                    <label class="form-check-label" for="active_status_inactive">Inactive</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="super_admin">Super Admin</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="super_admin_yes" name="super_admin"
                                        value="1" checked>
                                    <label class="form-check-label" for="super_admin_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="super_admin_no" name="super_admin"
                                        value="0">
                                    <label class="form-check-label" for="super_admin_no">No</label>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="signup_on" name="signup_on">
                            <input type="hidden" class="form-control" id="signup_by" name="signup_by" value="mhvadmin">
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-label-secondary btn-prev"> <i
                                        class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-success btn-submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<hr class="container-m-nx mb-12">
<!-- Modern -->
<div class="row">
    <!-- Modern Wizard -->
    <!-- /Modern Wizard -->
    <!-- Modern Vertical Wizard -->
    <!-- /Modern Vertical Wizard -->
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(function() {
        const select2 = $('.select2'),
            selectPicker = $('.selectpicker');
        if (selectPicker.length) {
            selectPicker.selectpicker();
        }
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                var placeholderText = $this.data('placeholder') ||
                    'Please select';
                $this.select2({
                    placeholder: placeholderText,
                    dropdownParent: $this.parent(),
                });
            });
        }
        $(document).ready(function() {
            $('#formValidationCountry').select2({
                placeholder: "Select the pincode",
                allowClear: true,
                minimumInputLength: 4,
                ajax: {
                    url: 'pincode',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            pincode: params.term
                        };
                    },
                    processResults: function(data) {
                        const uniqueNames = new Set();
                        return {
                            results: data
                                .filter(function(item) {
                                    if (!uniqueNames.has(item.address_name)) {
                                        uniqueNames.add(item.address_name);
                                        return true;
                                    }
                                    return false;
                                })
                                .map(function(item) {
                                    return {
                                        id: item.address_id,
                                        text: item.address_name
                                    };
                                })
                        };
                    },
                    cache: true
                }
            });
        });
        $('#formValidationCountry').change(function() {
            var address_id = $('#formValidationCountry').val();
            var address_name = $('#formValidationCountry option:selected')
                .text();
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: "{{ route('area_find') }}",
                data: {
                    address_name: address_name,
                    _token: token
                },
                success: function(response) {
                    $('#areaoptions').empty();
                    $('#areaoptions').append('<option label=" ">Please search area </option>');
                    $.each(response, function(index, area) {
                        $('#areaoptions').append('<option value="' + area.address_id +
                            '">' + area.address_name + '</option>');
                    });
                    $('#areaoptions').trigger('change');
                    initializeSelect2($('#areaoptions'));
                },
                error: function(xhr, status, error) {
                    console.error("There was an error:", error);
                }
            });
        });
        $('#areaoptions').change(function() {
            var area_id = $('#areaoptions').val();
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('findlocation') }}",
                type: "POST",
                data: {
                    address_id: area_id,
                    _token: token
                },
                success: function(response) {
                    if (response?.state?.length > 0) {
                        populateDropdown('#stateoptions', response.state);
                        initializeSelect2($(
                            '#stateoptions'));
                        if (response?.state?.length > 0) {
                            $('#stateoptions').val(response.state[0].address_id).trigger(
                                'change');
                        }
                    }
                    if (response?.country?.length > 0) {
                        populateDropdown('#countryoptions', response.country);
                        initializeSelect2($(
                            '#countryoptions'));
                        if (response?.country?.length > 0) {
                            $('#countryoptions').val(response.country[0].address_id).trigger(
                                'change');
                        }
                    }
                    if (response?.city?.length > 0) {
                        populateDropdown('#cityoptions', response.city);
                        initializeSelect2($(
                            '#cityoptions'));
                        if (response?.city?.length > 0) {
                            $('#cityoptions').val(response.city[0].address_id).trigger(
                                'change');
                        }
                        $("#Country, #City, #State").show();
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching addresses:', xhr.responseText || xhr
                        .statusText);
                    alert('Error fetching addresses. Please try again later.');
                }
            });
        });

        function populateDropdown(selector, items) {
            if ($(selector).length) {
                $(selector).empty();
                $(selector).append('<option value="">Please select</option>');
                $.each(items, function(key, value) {
                    $(selector).append(
                        `<option value="${value.address_id}">${value.address_name}</option>`);
                });
            }
        }

        function initializeSelect2($element) {
            if ($element.length && !$element.hasClass('select2-hidden-accessible')) {
                $element.select2({
                    placeholder: $element.data('placeholder') || 'Please select',
                    dropdownParent: $element.parent()
                });
            }
        }
    });
    $(document).ready(function() {
        $('#wizard-validation-form').on('submit', function(e) {
            e.preventDefault();
            var accountDetails = {
                corporate_name: $('#corporate_name').val(),
                corporate_no: $('#corporate_no').val(),
                location_id: $('#location_id').val(),
                corporate_id: $('#corporate_id').val(),
                display_name: $('#display_name').val(),
                registration_no: $('#registration_no').val(),
                company_profile: $('#company_profile').val(),
                industry: $('#industry').val(),
                industry_segment: $('#industry_segment').val(),
                gstin: $('#gstin').val(),
                discount: $('#discount').val(),
                valid_from: $('#valid_from').val(),
                valid_upto: $('#valid_upto').val(),
                corporate_color: $('#corporate_color').val(),
                active_status: $('input[name="active_status"]:checked').val()
            };
            var address = {
                corporate_id: $('#corporate_id').val(),
                pincode: $('#formValidationCountry').val(),
                area: $('#areaoptions').val(),
                city: $('#cityoptions').val(),
                state: $('#stateoptions').val(),
                country: $('#countryoptions').val(),
                latitude: $('#latitude').val(),
                longitude: $('#longitude').val(),
                website_link: $('#website_link').val()
            };
            var employeeTypes = [];
            $('input[name="employee_type_name[]"]').each(function() {
                employeeTypes.push($(this).val());
            });
            var corporateAdminUser = {
                corporate_admin_user_id: $('#corporate_admin_user_id').val(),
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                dob: $('#dob').val(),
                gender: $('#gender').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                mobile_country_code: $('#mobile_country_code').val(),
                mobile_num: $('#mobile_num').val(),
                aadhar: $('#aadhar').val(),
                age: $('#age').val(),
                active_status: $('input[name="corp_active_status"]:checked').val(),
                super_admin: $('input[name="super_admin"]:checked').val(),
                signup_on: $('#signup_on').val(),
                signup_by: $('#signup_by').val()
            };
            var modulesData = [];
            $('.module-checkbox:checked').each(function() {
                var moduleId = $(this).val();
                var subModuleIds = [];
                $('#submodules-' + moduleId)
                    .find('.submodule-checkbox:checked')
                    .each(function() {
                        subModuleIds.push($(this).val());
                    });
                var moduleData = {
                    module_id: moduleId
                };
                if (subModuleIds.length > 0) {
                    moduleData.sub_module_ids = subModuleIds;
                }
                modulesData.push(moduleData);
            });
            var formData = {
                accountDetails: accountDetails,
                address: address,
                employeeTypes: employeeTypes,
                corporateAdminUser: corporateAdminUser,
                modulesData: modulesData,
            };
            console.log('Compiled Form Data:', formData);
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('addcorporate.corp') }}",
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Login successful! Redirecting to dashboard...');
                        setTimeout(function() {
                            window.location.href = "/corporate/corporate-list";
                        }, 2000);
                    } else {
                        toastr.error('Login failed: ' + response.error, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    console.log(xhr.responseText);
                }
            });
        });
        let fieldCount = 1;

        function addEmployeeTypeField() {
            fieldCount++;
            const fieldHtml = `
        <div class="row g-3 employee-type-fields align-items-center" id="employee_type_field_${fieldCount}">
            <div class="col-md-7">
                <input type="text" id="employee_type_name_${fieldCount}" name="employee_type_name[]"
                    class="form-control border rounded" placeholder="Employee Type Name ${fieldCount}">
            </div>
            <div class="col-md-5 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-sm remove-employee-type" data-id="${fieldCount}">
                    <i class="ti ti-minus"></i> Remove
                </button>
            </div>
        </div>`;
            $('#dynamic-fields-container').append(fieldHtml).hide().fadeIn();
        }
        $(document).on('click', '#add-employee-type', function() {
            addEmployeeTypeField();
        });
        $(document).on('click', '.remove-employee-type', function() {
            const fieldId = $(this).data('id');
            $(`#employee_type_field_${fieldId}`).fadeOut(function() {
                $(this).remove();
            });
        });
        $('.toggle-icon').click(function() {
            const moduleId = $(this).data('module-id');
            const submodulesContainer = $('#submodules-' + moduleId);
            const icon = $(this);
            submodulesContainer.slideToggle();
            if (submodulesContainer.is(':visible')) {
                icon.text('-');
            } else {
                icon.text('+');
            }
        });
        $('.module-checkbox').on('change', function() {
            var moduleId = $(this).val();
            var submoduleContainer = $('#submodules-' + moduleId);
            if ($(this).prop('checked')) {
                submoduleContainer.slideDown();
            } else {
                submoduleContainer.slideUp();
            }
        });
    });
</script>
<style>
    /* Main container for layout */
    /* Add consistent spacing between dynamic fields */
    #dynamic-fields-container .employee-type-fields {
        margin-bottom: 15px;
        /* Space between fields */
        display: flex;
        align-items: center;
        /* Align input and button neatly */
    }

    /* Align input and buttons proportionally */
    .employee-type-fields input {
        flex: 1;
        /* Inputs take the available width */
        margin-right: 10px;
        /* Add space between input and button */
    }

    /* Add smooth animation for dynamic elements */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .employee-type-fields {
        animation: fadeIn 0.3s ease-in-out;
    }

    .module-container {
        padding: 20px;
        font-size: 16px;
    }

    /* Module list */
    .module-list {
        padding: 10px;
    }

    /* Each module container */
    .module {
        margin-bottom: 20px;
        position: relative;
    }

    /* Module header styling */
    .module-header {
        cursor: pointer;
        padding: 5px;
    }

    /* Submodule container styling */
    .submodule-container {
        display: none;
        /* Initially hidden */
        margin-top: 10px;
        padding-left: 20px;
        background-color: #f4f4f4;
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 10px;
        transition: all 0.3s ease-in-out;
    }

    /* Submodule checkboxes */
    .submodule-content label {
        display: block;
        margin-bottom: 10px;
        font-size: 14px;
    }

    /* Styling for submodule checkboxes */
    .submodule-content input {
        margin-right: 10px;
    }
</style>
@extends('layouts/layoutMaster')

@section('title', 'Wizard Numbered - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->


@section('content')
    <div class="row">
        <div class="col-12">
            <h5>Default</h5>
        </div>


        <div class="col-12 mb-6">
            <small class="text-light fw-medium">Validation</small>
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


                    <div class="step" data-target="#adminuser-details-validation">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">3</span>
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
                                    <label for="corporate_name" class="form-label fw-bold">Corporate Name</label>
                                    <input type="text" id="corporate_name" name="corporate_name"
                                        value="{{ $corporate['corporate_name'] }}" class="form-control"
                                        placeholder="Enter the Corporate Name" required>
                                </div>

                                {{-- Corporate ID (Read-Only) --}}
                                <div class="col-sm-6">
                                    <label for="corporate_id" class="form-label fw-bold">Corporate ID</label>
                                    <input type="text" id="corporate_id" name="corporate_id"
                                        class="form-control bg-light" value="{{ $corporate['corporate_id'] }}" readonly
                                        required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="corporate_id" class="form-label fw-bold">Location ID</label>
                                    <input type="text" id="location_id" name="location_id" class="form-control bg-light"
                                        value="{{ $locationId['data'] }}" readonly required>
                                </div>

                                {{-- Corporate Number --}}
                                <div class="col-sm-6">
                                    <label for="corporate_no" class="form-label fw-bold">Corporate Number</label>
                                    <input type="text" id="corporate_no" name="corporate_no"
                                        value="{{ $corporate['corporate_no'] }}" class="form-control"
                                        placeholder="Enter the Corporate Number" required>
                                </div>

                                {{-- Display Name --}}
                                <div class="col-sm-6">
                                    <label for="display_name" class="form-label fw-bold">Display Name</label>
                                    <input type="text" id="display_name" name="display_name" value=" "
                                        class="form-control" placeholder="Enter Display Name">
                                </div>

                                {{-- Corporate Registration Number --}}
                                <div class="col-sm-6">
                                    <label for="registration_no" class="form-label fw-bold">Corporate Registration
                                        No</label>
                                    <input type="text" id="registration_no" name="registration_no"
                                        value="{{ $corporate['registration_no'] }}" class="form-control"
                                        placeholder="Enter Registration Number" required>
                                </div>

                                {{-- Industry Segment --}}
                                <div class="col-sm-6">
                                    <label for="industry_segment" class="form-label fw-bold">Industry Segment</label>
                                    <input type="text" id="industry_segment" name="industry_segment"
                                        value="{{ $corporate['industry_segment'] }}" class="form-control"
                                        placeholder="Enter Industry Segment">
                                </div>

                                {{-- Profile Image --}}
                                <div class="col-sm-6">
                                    <label for="prof_image" class="form-label fw-bold">Profile Image</label>
                                    <input type="file" id="prof_image" name="prof_image" class="form-control">
                                </div>

                                {{-- Company Profile --}}
                                <div class="col-sm-6">
                                    <label for="company_profile" class="form-label fw-bold">Company Profile</label>
                                    <input id="company_profile" name="company_profile"
                                        value="{{ $corporate['company_profile'] }}" class="form-control"
                                        placeholder="Enter company profile details">
                                </div>

                                {{-- Industry --}}
                                <div class="col-sm-6">
                                    <label for="industry" class="form-label fw-bold">Industry</label>
                                    <input id="industry" name="industry" value="{{ $corporate['industry'] }}"
                                        class="form-control" placeholder="Enter Industry">
                                </div>

                                {{-- Created By --}}
                                <div class="col-sm-6">
                                    <label for="created_by" class="form-label fw-bold">Created By</label>
                                    <input type="text" id="created_by" name="created_by"
                                        value="{{ $corporate['created_by'] }}" class="form-control"
                                        placeholder="Enter creator's name" required>
                                </div>

                                {{-- GSTIN --}}
                                <div class="col-sm-6">
                                    <label for="gstin" class="form-label fw-bold">Gstin</label>
                                    <input type="text" id="gstin" name="gstin"
                                        value="{{ $corporate['gstin'] }}" class="form-control" placeholder="Enter Gstin"
                                        required>
                                </div>

                                {{-- Discount --}}
                                <div class="col-sm-6">
                                    <label for="discount" class="form-label fw-bold">Discount</label>
                                    <input type="text" id="discount" name="discount"
                                        value="{{ $corporate['discount'] }}" class="form-control"
                                        placeholder="Enter discount" required>
                                </div>

                                {{-- Created On --}}
                                <div class="col-sm-6">
                                    <label for="created_on" class="form-label fw-bold">Created On</label>
                                    <input type="date" id="created_on" name="created_on"
                                        value="{{ $corporate['created_on'] }}" class="form-control" required>
                                </div>

                                {{-- Valid From --}}
                                <div class="col-sm-6">
                                    <label for="valid_from" class="form-label fw-bold">Valid From</label>
                                    <input type="date" id="valid_from" name="valid_from" value=""
                                        class="form-control">
                                </div>

                                {{-- Valid Upto --}}
                                <div class="col-sm-6">
                                    <label for="valid_upto" class="form-label fw-bold">Valid Upto</label>
                                    <input type="date" id="valid_upto" name="valid_upto"
                                        value="{{ $corporate['valid_upto'] }}" class="form-control">
                                </div>

                                {{-- Corporate Color --}}
                                <div class="col-sm-6">
                                    <label for="corporate_color" class="form-label fw-bold">Corporate Color</label>
                                    <input type="color" id="corporate_color" name="corporate_color"
                                        value="{{ $corporate['corporate_color'] }}" class="form-control">
                                </div>

                                {{-- Active Status --}}
                                <div class="col-sm-6 d-flex align-items-center">
                                    <label for="active_status" class="form-label fw-bold me-3">Active Status:</label>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" id="active" name="active_status"
                                            class="form-check-input" value="1"
                                            {{ ($corporate['active_status'] ?? 1) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" id="inactive" name="active_status"
                                            class="form-check-input" value="0"
                                            {{ ($corporate['active_status'] ?? 1) == 0 ? 'checked' : '' }}>
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
                                    <label class="form-label" for="formValidationCountry">Pincode</label>
                                    <select class="select2 form-control border rounded" id="formValidationCountry"
                                        name="pincode_id">
                                        <option value="{{ $pincode['address_id'] }}">


                                            {{ $pincode['address_name'] }}
                                        </option>
                                    </select>
                                </div>


                                <!-- Area Dropdown -->
                                <div class="col-md-6">
                                    <label class="form-label" for="areaoptions">Area</label>
                                    <select class="select2 form-control border rounded" id="areaoptions" name="area_id">
                                        <option value="{{ $area['address_id'] }}">
                                            {{ $area['address_name'] }}
                                        </option>
                                    </select>
                                </div>

                                <!-- City Dropdown -->
                                <div class="col-md-6">
                                    <label class="form-label" for="cityoptions">City</label>
                                    <select class="select2 form-control border rounded" id="cityoptions" name="city_id">
                                        <option value="{{ $city['address_id'] }}">
                                            {{ $city['address_name'] }}
                                        </option>
                                    </select>
                                </div>

                                <!-- State Dropdown -->
                                <div class="col-md-6">
                                    <label class="form-label" for="stateoptions">State</label>
                                    <select class="select2 form-control border rounded" id="stateoptions"
                                        name="state_id">
                                        <option value="{{ $state['address_id'] }}">
                                            {{ $state['address_name'] }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Country Dropdown -->
                                <div class="col-md-6">
                                    <label class="form-label" for="countryoptions">Country</label>
                                    <select class="select2 form-control border rounded" id="countryoptions"
                                        name="country_id">
                                        <option value="{{ $country['address_id'] }}">
                                            {{ $country['address_name'] }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Latitude -->
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" id="latitude" value="{{ $corporateAddress['latitude'] }}"
                                        name="latitude" class="form-control border rounded shadow-sm"
                                        placeholder="Latitude">
                                </div>

                                <!-- Longitude -->
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" id="longitude" value="" name="longitude"
                                        class="form-control border rounded shadow-sm" placeholder="Longitude">
                                </div>

                                <!-- Website Link -->
                                <div class="col-md-6">
                                    <label for="website_link" class="form-label">Website Link</label>
                                    <input type="text" id="website_link" name="website_link"
                                        value="{{ $corporateAddress['website_link'] }}"
                                        class="form-control border rounded shadow-sm" placeholder="Website Link">
                                </div>
                                <div class="col-12 d-flex justify-content-between">
                                    <button class="btn btn-label-secondary btn-prev"> <i
                                            class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                        <span class="align-middle">Previous</span>
                                    </button>
                                    <button class="btn btn-primary btn-next"> <span class="align-middle ">Next</span> <i
                                            class="ti ti-arrow-right ti-xs"></i></button>
                                </div>
                            </div>

                        </div>
                        <!-- Social Links -->


                        <div id="adminuser-details-validation" class="content">
                            <div class="content-header mb-4">
                                <h6 class="mb-0">Corporate Super A Details</h6>
                                <small>Enter Your Corporate Details</small>
                            </div>
                            <div class="row g-6">
                                <input type="hidden" id="corporate_admin_user_id" name="corporate_admin_user_id"
                                    class="form-control" value="{{ $emptype['corporate_admin_user_id'] }}" readonly>
                                <div class="col-sm-6 mb-3">
                                    <label for="first_name" class="form-label">First Name:</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control"
                                        value="" required>
                                </div>

                                <!-- Last Name -->
                                <div class="col-sm-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name:</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control"
                                        value="{{ old('last_name', $emptype['last_name']) }}" required>
                                </div>

                                <!-- Date of Birth -->
                                <div class="col-sm-6 mb-3">
                                    <label for="dob" class="form-label">Date of Birth:</label>
                                    <input type="date" id="dob" name="dob" class="form-control"
                                        value="{{ old('dob', $emptype['dob']) }}" required>
                                </div>

                                <!-- Gender -->
                                <div class="col-sm-6 mb-3">
                                    <label for="gender" class="form-label">Gender:</label>
                                    <select id="gender" name="gender" class="form-control" required>
                                        <option value="male" {{ $emptype['gender'] == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female" {{ $emptype['gender'] == 'female' ? 'selected' : '' }}>
                                            Female
                                        </option>
                                        <option value="other" {{ $emptype['gender'] == 'other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>

                                <!-- Email -->
                                <div class="col-sm-6 mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>

                                <!-- Mobile Country Code -->
                                <div class="col-sm-6 mb-3">
                                    <label for="mobile_country_code" class="form-label">Mobile Country Code:</label>
                                    <input type="text" id="mobile_country_code" name="mobile_country_code"
                                        class="form-control"
                                        value="{{ old('mobile_country_code', $emptype['mobile_country_code']) }}"
                                        required>
                                </div>

                                <!-- Mobile Number -->
                                <div class="col-sm-6 mb-3">
                                    <label for="mobile_num" class="form-label">Mobile Number:</label>
                                    <input type="text" id="mobile_num" name="mobile_num" class="form-control"
                                        value="{{ old('mobile_num', $emptype['mobile_num']) }}" required>
                                </div>

                                <!-- Aadhar -->
                                <div class="col-sm-6 mb-3">
                                    <label for="aadhar" class="form-label">Aadhar Number:</label>
                                    <input type="text" id="aadhar" name="aadhar" class="form-control"
                                        value=" " required>
                                </div>

                                <!-- Age -->
                                <div class="col-sm-6 mb-3">
                                    <label for="age" class="form-label">Age:</label>
                                    <input type="text" id="age" name="age" class="form-control"
                                        value="{{ old('age', $emptype['age']) }}" required>
                                </div>

                                <!-- Signup By -->
                                <div class="col-sm-6 mb-3">
                                    <label for="signup_by" class="form-label">Signup By:</label>
                                    <input type="text" id="signup_by" name="signup_by" class="form-control"
                                        value="{{ old('signup_by', $emptype['signup_by']) }}" required>
                                </div>

                                <!-- Signup On -->
                                <div class="col-sm-6 mb-3">
                                    <label for="signup_on" class="form-label">Signup On:</label>
                                    <input type="text" id="signup_on" name="signup_on" class="form-control"
                                        value="{{ old('signup_on', $emptype['signup_on']) }}" disabled>
                                </div>

                                <!-- Active Status -->
                                <div class="col-sm-6">
                                    <label for="active_status">Active Status</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="active_status_active"
                                            name="corp_active_status" value="1" checked>
                                        <label class="form-check-label" for="active_status_active">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="active_status_inactive"
                                            name="corp_active_status" value="0">
                                        <label class="form-check-label" for="active_status_inactive">Inactive</label>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label for="super_admin">Super Admin</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="super_admin_yes"
                                            name="super_admin" value="1" checked>
                                        <label class="form-check-label" for="super_admin_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="super_admin_no"
                                            name="super_admin" value="0">
                                        <label class="form-check-label" for="super_admin_no">No</label>
                                    </div>
                                </div>
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



    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
    $(function() {
        const select2 = $('.select2'),
            selectPicker = $('.selectpicker');

        // Wizard Validation
        // --------------------------------------------------------------------
        const wizardValidation = document.querySelector('#wizard-validation');
        console.log(wizardValidation)

        if (wizardValidation) {
            // Wizard form
            const wizardValidationForm = wizardValidation.querySelector('#wizard-validation-form');

            // Wizard steps
            const wizardSteps = {
                step1: wizardValidationForm.querySelector('#account-details-validation'),
                step2: wizardValidationForm.querySelector('#personal-info-validation'),

                step5: wizardValidationForm.querySelector('#adminuser-details-validation'),
            };

            // Wizard navigation buttons
            const nextButtons = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
            const prevButtons = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));

            const validationStepper = new Stepper(wizardValidation, {
                linear: true
            });

            // Common Plugins Configuration
            const commonPlugins = {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5(),
                autoFocus: new FormValidation.plugins.AutoFocus(),
                submitButton: new FormValidation.plugins.SubmitButton()
            };

            // Step 1: Account Details Validation
            const FormValidation1 = FormValidation.formValidation(wizardSteps.step1, {
                fields: {
                    corporate_name: {
                        validators: {
                            notEmpty: {
                                message: 'The name is required'
                            },
                            stringLength: {
                                min: 6,
                                max: 30,
                                message: 'The name must be between 6 and 30 characters'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z0-9 ]+$/,
                                message: 'Only letters, numbers, and spaces are allowed'
                            }
                        }
                    },
                    display_name: {
                        validators: {
                            notEmpty: {
                                message: 'The display name is required'
                            },
                            stringLength: {
                                min: 6,
                                max: 30,
                                message: 'The name must be between 6 and 30 characters'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z0-9 ]+$/,
                                message: 'Only letters, numbers, and spaces are allowed'
                            }
                        }
                    },
                    valid_from: {
                        validators: {
                            notEmpty: {
                                message: 'The start date is required'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The start date is not valid'
                            }
                        }
                    },
                    valid_upto: {
                        validators: {
                            notEmpty: {
                                message: 'The end date is required'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The end date is not valid'
                            }
                        }
                    }
                },
                plugins: commonPlugins
            }).on('core.form.valid', () => validationStepper.next());

            // Step 2: Personal Info Validation
            const FormValidation2 = FormValidation.formValidation(wizardSteps.step2, {
                fields: {
                    formValidationCountry: {
                        validators: {
                            notEmpty: {
                                message: 'Pincode is required'
                            },
                            regexp: {
                                regexp: /^[1-9][0-9]{5}$/,
                                message: 'Pincode must be a valid 6-digit number'
                            }
                        }
                    }
                },
                plugins: commonPlugins
            }).on('core.form.valid', () => validationStepper.next());

            // Step 3: Social Links Validation

            // Step 4: Components Details Validation


            // Step 5: Final Validation
            const FormValidation5 = FormValidation.formValidation(wizardSteps.step5, {
                fields: {
                    first_name: {
                        validators: {
                            notEmpty: {
                                message: 'First Name is required.'
                            }
                        }
                    },
                    email: {
                        validators: {
                            notEmpty: {
                                message: 'Email is required.'
                            },
                            emailAddress: {
                                message: 'Please enter a valid email address.'
                            }
                        }
                    },
                    password: {
                        validators: {
                            notEmpty: {
                                message: 'Password is required.'
                            },
                            stringLength: {
                                min: 8,
                                message: 'Password must be at least 8 characters long.'
                            }
                        }
                    },
                    aadhar: {
                        validators: {
                            notEmpty: {
                                message: 'Aadhar is required.'
                            },
                            stringLength: {
                                min: 12,
                                max: 12,
                                message: 'Aadhar must be 12 digits.'
                            },
                            numeric: {
                                message: 'Only numeric values are allowed.'
                            }
                        }
                    }
                },
                plugins: commonPlugins
            }).on('core.form.valid', () => {
                alert('All steps are valid. You can now submit the form!');
                wizardValidationForm.submit();
            });

            // Handle Next Button Clicks
            nextButtons.forEach((button, index) => {
                button.addEventListener('click', () => {
                    switch (index) {
                        case 0:
                            FormValidation1.validate();
                            break;
                        case 1:
                            FormValidation2.validate();
                            break;
                        case 2:
                            FormValidation5.validate();
                            break;

                    }
                });
            });

            // Handle Previous Button Clicks
            prevButtons.forEach(button => {
                button.addEventListener('click', () => validationStepper.previous());
            });
        }

        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                var placeholderText = $this.data('placeholder') ||
                    'Please select'; // Default or custom placeholder
                $this.select2({
                    placeholder: placeholderText,
                    dropdownParent: $this.parent(),
                });
            });
        }
        var existingDisplayName = "{{ $corporate['display_name'] }}"; // Replace with server-side value

        $(document).ready(function() {
            $('#display_name').after(
                '<div id="displayNameError" style="color: red; display: none;">This Display Name is already used. Please choose another.</div>'
                );

            // Attach the blur event
            $('#display_name').on('blur', function() {
                var enteredName = $(this).val().trim(); // Get the value of the input field
                if (enteredName === existingDisplayName) {
                    $('#displayNameError').show(); // Show the error message
                    $(this).focus(); // Set focus back to the input field
                } else {
                    $('#displayNameError')
                .hide(); // Hide the error message if the name is valid
                }
            });

            // const stepper = new Stepper($('#wizard-validation')[0], {
            //     linear: true,
            //     animation: true
            // });

            // // Next button functionality
            // $('.btn-next').on('click', function() {
            //     stepper.next();
            // });

            // // Previous button functionality
            // $('.btn-prev').on('click', function() {
            //     stepper.previous();
            // });
            $('#formValidationCountry').select2({
                placeholder: "Select the pincode",
                allowClear: true,
                minimumInputLength: 4,
                ajax: {
                    url: '{{ route('findpincode') }}',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            pincode: params.term
                        };
                    },
                    processResults: function(data) {
                        //  console.log(data);

                        return {
                            results: data.map(function(item) {
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
            //alert(address_id);
            var address_name = $('#formValidationCountry option:selected')
                .text(); // This will give you the 'address_name'
            //alert(address_name);
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
                    $('#areaoptions').append(
                        '<option label=" ">Please search area </option>');

                    $.each(response, function(index, area) {
                        $('#areaoptions').append('<option value="' + area
                            .address_id +
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
                            '#stateoptions')); // Initialize select2 for state options

                        // Set default value for State dropdown (if any)
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
                            $('#countryoptions').val(response.country[0].address_id)
                                .trigger(
                                    'change');
                        }
                    }

                    if (response?.city?.length > 0) {
                        populateDropdown('#cityoptions', response.city);
                        initializeSelect2($(
                            '#cityoptions')); // Initialize select2 for city options

                        // Set default value for City dropdown (if any)
                        if (response?.city?.length > 0) {
                            $('#cityoptions').val(response.city[0].address_id).trigger(
                                'change');
                        }

                        $("#Country, #City, #State").show(); // Show the dropdowns
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
                $(selector).empty(); // Clear existing options
                $(selector).append('<option value="">Please select</option>'); // Add the default option

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
            e.preventDefault(); // Prevent default form submission

            // Gather data
            var accountDetails = {
                corporate_name: $('#corporate_name').val(),
                corporate_no: $('#corporate_no').val(),
                corporate_id: $('#corporate_id').val(),
                location_id: $('#location_id').val(),
                display_name: $('#display_name').val(),
                registration_no: $('#registration_no').val(),
                company_profile: $('#company_profile').val(),
                prof_image: $('#prof_image').val(),
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
            console.log(corporateAdminUser);

            var formData = {
                accountDetails: accountDetails,
                address: address,
                corporateAdminUser: corporateAdminUser
            };

            // Validate all fields
            var token = $('meta[name="csrf-token"]').attr('content');

            // AJAX call
            $.ajax({
                url: "{{ route('corporate.add_location') }}",
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(
                            'Corporate location added successfully! Redirecting to Corporate list...',
                            'Success', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: 'toast-top-right', // Position the toast in the top-right corner
                                timeOut: 3000, // Notification stays for 3 seconds
                                fadeOut: 1000, // Fade out after 1 second
                                extendedTimeOut: 1000 // Extension of time when user hovers over
                            }
                        );
                        setTimeout(function() {
                            window.location.href = "{{ route('corporate-list') }}";
                        }, 1000);
                    } else {
                        toastr.error(
                            'Submission failed: ' + response.error,
                            'Error', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: 'toast-top-right', // Position in the top-right corner
                                timeOut: 5000, // Notification stays for 5 seconds
                                fadeOut: 1000, // Fade out after 1 second
                                extendedTimeOut: 1000, // Extension of time when user hovers over
                                iconClass: 'toast-error' // Set custom error icon
                            }
                        );
                    }

                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    console.log(xhr.responseText);
                }
            });

        });

        // Validation Function








    });
</script>

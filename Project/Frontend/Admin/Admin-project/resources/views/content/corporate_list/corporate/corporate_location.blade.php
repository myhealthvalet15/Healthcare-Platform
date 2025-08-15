@extends('layouts/layoutMaster')
@section('title', 'Wizard Numbered - Forms')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection
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
                            <div class="col-md-6">
                                <label class="form-label" for="areaoptions">Area</label>
                                <select class="select2 form-control border rounded" id="areaoptions" name="area_id">
                                    <option value="{{ $area['address_id'] }}">
                                        {{ $area['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cityoptions">City</label>
                                <select class="select2 form-control border rounded" id="cityoptions" name="city_id">
                                    <option value="{{ $city['address_id'] }}">
                                        {{ $city['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="stateoptions">State</label>
                                <select class="select2 form-control border rounded" id="stateoptions"
                                    name="state_id">
                                    <option value="{{ $state['address_id'] }}">
                                        {{ $state['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="countryoptions">Country</label>
                                <select class="select2 form-control border rounded" id="countryoptions"
                                    name="country_id">
                                    <option value="{{ $country['address_id'] }}">
                                        {{ $country['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" id="latitude" value="{{ $corporateAddress['latitude'] }}"
                                    name="latitude" class="form-control border rounded shadow-sm"
                                    placeholder="Latitude">
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" id="longitude" value="" name="longitude"
                                    class="form-control border rounded shadow-sm" placeholder="Longitude">
                            </div>
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
                            <div class="col-sm-6 mb-3">
                                <label for="last_name" class="form-label">Last Name:</label>
                                <input type="text" id="last_name" name="last_name" class="form-control"
                                    value="{{ old('last_name', $emptype['last_name']) }}" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="dob" class="form-label">Date of Birth:</label>
                                <input type="date" id="dob" name="dob" class="form-control"
                                    value="{{ old('dob', $emptype['dob']) }}" required>
                            </div>
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
                            <div class="col-sm-6 mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="mobile_country_code" class="form-label">Mobile Country Code:</label>
                                <input type="text" id="mobile_country_code" name="mobile_country_code"
                                    class="form-control"
                                    value="{{ old('mobile_country_code', $emptype['mobile_country_code']) }}"
                                    required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="mobile_num" class="form-label">Mobile Number:</label>
                                <input type="text" id="mobile_num" name="mobile_num" class="form-control"
                                    value="{{ old('mobile_num', $emptype['mobile_num']) }}" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="aadhar" class="form-label">Aadhar Number:</label>
                                <input type="text" id="aadhar" name="aadhar" class="form-control"
                                    value=" " required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="age" class="form-label">Age:</label>
                                <input type="text" id="age" name="age" class="form-control"
                                    value="{{ old('age', $emptype['age']) }}" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="signup_by" class="form-label">Signup By:</label>
                                <input type="text" id="signup_by" name="signup_by" class="form-control"
                                    value="{{ old('signup_by', $emptype['signup_by']) }}" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="signup_on" class="form-label">Signup On:</label>
                                <input type="text" id="signup_on" name="signup_on" class="form-control"
                                    value="{{ old('signup_on', $emptype['signup_on']) }}" disabled>
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
<div class="row">
</div>
<script>
    var existingDisplayName = "{{ $corporate['display_name'] }}";
</script>
<script src="/lib/js/page-scripts/corporate_location.js"></script>
@endsection
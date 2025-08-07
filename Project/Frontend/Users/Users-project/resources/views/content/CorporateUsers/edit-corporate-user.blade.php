@extends('layouts/layoutMaster')

@section('title', 'Edit Corporate User - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->

@section('content')
<div class="col-12 mb-6">
    <div id="wizardb-validation" class="bs-stepper mt-2">

        <div class="bs-stepper-content">
            <form id="wizard-validation-form" method="post">
                <!-- Account Details -->
                <div id="account-details-validation" class="content" style="display:block;">
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                        <button type="button" class="btn btn-primary" id="back-to-list"
                            onclick="window.location.href='/corporate-users/users-list'"
                            style="margin-right: 20px;">Back to User
                            List</button>
                    </div>
                    <div class="row g-6">
                        <div class="col-md-6">
                            <label class="form-label" for="first_name">First
                                Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control"
                                placeholder="John" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="last_name">Last
                                Name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Doe" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group input-group-merge">
                                <input type="email" id="email" name="email" class="form-control" placeholder="john.doe"
                                    aria-label="john.doe" aria-describedby="email" />
                                <span class="input-group-text" id="email">@example.com</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mobile_num">Mobile</label>
                            <div class="d-flex">
                                <div class="me-2" style="flex: 0 0 100px;">
                                    <input type="text" name="mobile_country_code" id="mobile_country_code"
                                        class="form-control" placeholder="+91" />
                                </div>
                                <div style="flex: 1;">
                                    <input type="text" name="mobile_num" id="mobile_num" class="form-control"
                                        placeholder="Mobile no." />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 select2-primary">
                            <label class="form-label" for="department">Department</label>
                            <select id="department" name="department[]" class="form-control select2" multiple>
                                <option value="select-all">All Departments</option>
                                <!-- Options will be added dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="aadhar">Aadhar
                                No</label>

                            <input type="text" id="aadhar" name="aadhar" class="form-control"
                                placeholder="XXXX XXXX XXXX" aria-label="XXXX XXXX XXXX"
                                pattern="[0-9]{4} [0-9]{4} [0-9]{4}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-check-label">Gender</label>
                            <div class="col mt-2">
                                <div class="form-check form-check-inline">
                                    <input name="gender" class="form-check-input" type="radio" value="Male" id="Male"
                                        checked />
                                    <label class="form-check-label" for="Male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="gender" class="form-check-input" type="radio" value="Female"
                                        id="Female" />
                                    <label class="form-check-label" for="Female">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="gender" class="form-check-input" type="radio" value="Others"
                                        id="Others" />
                                    <label class="form-check-label" for="Others">Others</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-check-label">Settings</label>
                            <div class="col mt-2">
                                <div class="form-check form-check-inline">
                                    <input name="setting" class="form-check-input" type="checkbox" value="1"
                                        id="setting_ohc" />
                                    <label class="form-check-label" for="setting_ohc">
                                        OHC
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="setting" class="form-check-input" type="checkbox" value="2"
                                        id="setting_mhc" />
                                    <label class="form-check-label" for="setting_mhc">
                                        MHC
                                    </label>
                                </div>

                            </div>

                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" id="add-corporate-user">Save </button>
                                <button onclick="window.location.href='/corporate-users/users-list'"
                                    class="btn btn-label-danger waves-effect"
                                    data-bs-dismiss="offcanvas">Cancel</button>

                            </div>

                        </div>
                 
                    </div>

            </form>
        </div>
    </div>
</div>
<script>
    $('#first_name').val("{{ $corporateuser['first_name'] ?? '' }} ");
    $('#last_name').val("{{ $corporateuser['last_name'] ?? '' }} ");
    $('#email').val("{{ $corporateuser['email'] ?? '' }} ");
    $('#mobile_country_code').val("{{ $corporateuser['mobile_country_code'] ?? '' }} ");
    $('#mobile_num').val("{{ $corporateuser['mobile_num'] ?? '' }} ");
    $('#aadhar').val("{{ $corporateuser['aadhar'] ?? '' }} ");
    var gender = "{{ $corporateuser['gender'] ?? '' }}";
    var setting = "{{ $corporateuser['setting'] ?? '' }}";
    var selectedDepartment = "{{ $corporateuser['department'] ?? '' }}";
</script>
<script src="/lib/js/page-scripts/edit-corporate-user.js"></script>
@endsection
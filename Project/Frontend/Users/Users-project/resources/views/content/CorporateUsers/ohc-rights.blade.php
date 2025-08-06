@extends('layouts/layoutMaster')

@section('title', 'OHC Menu Rights')

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
<!-- Validation Wizard -->
<div class="col-12 mb-6">

    <div id="wizard-validation" class="bs-stepper mt-2">

        <div class="bs-stepper-content">

            <form id="wizard-validation-form" method="post">
                <!-- Account Details -->
                <div id="account-details-validation" class="content" style="display:block;">
                    <div style=" justify-content: flex-end; margin-bottom: 10px;">
                        <button type="button" class="btn btn-primary" id="back-to-list"
                            onclick="window.location.href='/corporate-users/users-list'"
                            style="margin-right: 20px;">Back to User
                            List</button>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <input type="hidden" name="corporate_user_id" id="corporate_user_id"
                                class="form-check-input" type="text" value="{{ $corporateuser['id'] }}">
                            <input type="hidden" name="corporate_ohc_rights_id" id="corporate_ohc_rights_id"
                                class="form-check-input" type="text" value="">
                            <p style="">{{ $corporateuser['first_name'] }} {{ $corporateuser['last_name'] }}</p>
                            <p style="">{{ $corporateuser['email'] }}</p>
                            <p style="">{{ $corporateuser['mobile_country_code'] }}
                                {{ $corporateuser['mobile_num'] }}</p>
                            <label>Department:</label><br>
                            {{ implode(', ', $corporateuser_dept) }}


                        </div>


                        <div class="col-md-8">
                            <div class="row g-12">
                                <div class="col-md-6">
                                    <h5>MENU</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>HIDE</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>VIEW</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>EDIT</h5>
                                </div>
                            </div>
                            <div class="row g-12">
                                <div class="col-md-6">
                                    <label>Employees</label>
                                </div>
                                <div class="col-md-2">
                                    <input name="employees" class="form-check-input" type="radio" value="0" />
                                </div>
                                <div class="col-md-2">
                                    <input name="employees" class="form-check-input" type="radio" value="1" />
                                </div>
                                <div class="col-md-2">
                                    <input name="employees" class="form-check-input" type="radio" value="2" />
                                </div>
                            </div>
                            <hr style="border-top: 2px dashed #ccc; margin-bottom: 20px; width: 100%;">

                            <div id="data-menu">

                            </div>
                            <div class="row g-12">
                                <div class="col-md-6">
                                    <label>LANDING PAGE</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-12">
                                        <div class="col-md-1">
                                            <input type="checkbox" name="landing_page[]" value="1"
                                                class="form-check-input">
                                        </div>
                                        <div class="col-md-8">
                                            <label>Corporate Profile</label>
                                        </div>
                                    </div>
                                    <div class="row g-12">
                                        <div class="col-md-1">
                                            <input type="checkbox" name="landing_page[]" value="2"
                                                class="form-check-input">
                                        </div>
                                        <div class="col-md-8">
                                            <label>Admin Dashboard</label>
                                        </div>
                                    </div>
                                    <div class="row g-12">
                                        <div class="col-md-1">
                                            <input type="checkbox" name="landing_page[]" value="3"
                                                class="form-check-input">
                                        </div>
                                        <div class="col-md-8">
                                            <label>Health Index Dashboard</label>
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" class="btn btn-primary" id="save_ohc_rights">Save
                                        </button>

                                        <button onclick="window.location.href='/corporate-users/users-list'"
                                            class="btn btn-label-danger waves-effect"
                                            data-bs-dismiss="offcanvas">Cancel</button>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    </div>
                                </div>
                            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Validation Wizard -->
</div>
   <script src="/lib/js/page-scripts/ohc-rights.js"></script>
@endsection
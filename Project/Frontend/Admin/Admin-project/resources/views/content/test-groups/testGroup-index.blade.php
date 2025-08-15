@extends('layouts/layoutMaster')
@section('title', 'Test Groups')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/forms-selects.js',
'resources/assets/js/forms-typeahead.js',
'resources/assets/js/form-wizard-numbered.js',
'resources/assets/js/form-wizard-validation.js'
])
@endsection
@section('content')
<style>
    .existing-group-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 200px;
        text-align: center;
        margin-top: 75px;
    }

    .spinner-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sk-bounce {
        display: flex;
        justify-content: space-between;
        width: 50px;
    }

    .sk-bounce-dot {
        width: 30px;
        height: 30px;
        margin: 0 5px;
        background-color: #007bff;
        border-radius: 50%;
        animation: sk-bounce 1.4s infinite ease-in-out both;
    }
</style>
<div class="row">
    <!-- <div class="col-12">
        <h5>You Can Add Groups, Sub Groups, Sub Sub Groups Here.</h5>
    </div> -->
    <div class="col-12 mb-6">
        <div class="bs-stepper wizard-numbered mt-2">
            <div class="bs-stepper-header">
                <div class="step" data-target="#group-table">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">1</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Group</span>
                            <span class="bs-stepper-subtitle">You can add group here.</span>
                        </span>
                    </button>
                </div>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <div class="step" data-target="#sub-group-table">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">2</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Sub Group</span>
                            <span class="bs-stepper-subtitle">You can add sub group here.</span>
                        </span>
                    </button>
                </div>
                <div class="line"><i class="ti ti-chevron-right"></i></div>
                <div class="step" data-target="#sub-sub-group-table">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">3</span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Sub Sub Group</span>
                            <span class="bs-stepper-subtitle">You can add sub sub group here.</span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="existing-group-spinner" id="existing-group-spinner" style="display: block;">
                <div class="spinner-container">
                    <div class="sk-bounce sk-primary">
                        <div class="sk-bounce-dot"></div>
                        <div class="sk-bounce-dot"></div>
                    </div>
                    <label id="spinnerLabeltext">retrieving datas ...</label>
                </div>
            </div>
            <div class="bs-stepper-content" id="existing-group-data" style="display: none;">
                <div id="group-table" class="content">
                    <form class="needs-validation-group" id="testGroupForm" name="testGroupForm" novalidate>
                        <div class="row align-items-center g-3">
                            <!-- Group Name Input -->
                            <div class="col-sm-4">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="bs-validation-name">Group name</label>
                                    <input name="grpName" type="text" id="grpName" class="form-control"
                                        placeholder="enter your group details here" required />
                                    <div class="valid-feedback"> Looks good! </div>
                                    <div class="invalid-feedback"> Please enter the group name. </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="status">Status</label>
                                    <div class="d-flex align-items-center gap-5">
                                        <div class="d-flex align-items-center me-5 me-lg-5 pe-5">
                                            <label class="switch switch-success mb-0">
                                                <input type="checkbox" class="switch-input" checked=""
                                                    id="groupActiveStatus">
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on">
                                                        <i class="ti ti-check"></i>
                                                    </span>
                                                    <span class="switch-off">
                                                        <i class="ti ti-x"></i>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <input type="hidden" id="editGroupItemId">
                                        <input type="hidden" id="formTypeGroup">
                                        <button type="submit" id="submitButtonGroup" class="btn btn-primary">
                                            <i class="fa-solid fa-plus"></i>&nbsp;Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="sub-group-table" class="content">
                    <form class="needs-validation" id="testSubGroupForm" name="testSubGroupForm" novalidate>
                        <div class="row align-items-center g-3">
                            <div class="col-sm-4">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="bs_validation_group_inSubGroup">Select Group</label>
                                    <select class="form-select select2" id="bs_validation_group_inSubGroup" required>
                                        <option value="0">Select Group</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a group.</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="subGrpName">Sub Group Name</label>
                                    <input name="subGrpName" type="text" id="subGrpName" class="form-control"
                                        placeholder="Sub Group List" required />
                                    <div class="invalid-feedback">Please enter the sub group name.</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="status">Status</label>
                                    <div class="d-flex align-items-center gap-5">
                                        <div class="d-flex align-items-center me-5 me-lg-5 pe-5">
                                            <label class="switch switch-success mb-0">
                                                <input type="checkbox" class="switch-input" checked=""
                                                    id="subGroupActiveStatus">
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on">
                                                        <i class="ti ti-check"></i>
                                                    </span>
                                                    <span class="switch-off">
                                                        <i class="ti ti-x"></i>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <input type="hidden" id="editSubGroupItemId">
                                        <input type="hidden" id="formTypeSubGroup">
                                        <button type="submit" id="submitButtonSubGroup" class="btn btn-primary">
                                            <i class="fa-solid fa-plus"></i>&nbsp;Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="sub-sub-group-table" class="content">
                    <form class="needs-validation" id="testSubSubGroupForm" name="testSubSubGroupForm" novalidate>
                        <div class="row align-items-center g-3">
                            <div class="col-sm-3">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="bs_validation_group_inSubSubGroup">Select
                                        Group</label>
                                    <select class="form-select select2" id="bs_validation_group_inSubSubGroup" required>
                                        <option value="0">Select Group</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a group.</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="bs_validation_subGroup_inSubSubGroup">Select Sub
                                        Group</label>
                                    <select class="form-select select2" id="bs_validation_subGroup_inSubSubGroup"
                                        required>
                                        <option value="0">Sub group List</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a sub group.</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="subSubGrpName">Sub Sub Group Name</label>
                                    <input name="subSubGrpName" type="text" id="subSubGrpName" class="form-control"
                                        placeholder="Enter sub sub-group name" required />
                                    <div class="invalid-feedback">Please enter the sub sub group name.</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="d-flex flex-column">
                                    <label class="form-label" for="status">Status</label>
                                    <div class="d-flex align-items-center gap-5">
                                        <div class="d-flex align-items-center me-5 me-lg-5 pe-5">
                                            <label class="switch switch-success mb-0">
                                                <input type="checkbox" class="switch-input" checked=""
                                                    id="subSubGroupActiveStatus">
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on">
                                                        <i class="ti ti-check"></i>
                                                    </span>
                                                    <span class="switch-off">
                                                        <i class="ti ti-x"></i>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <input type="hidden" id="editSubSubGroupItemId">
                                        <input type="hidden" id="formTypeSubSubGroup">
                                        <button type="submit" id="submitButtonSubSubGroup" class="btn btn-primary">
                                            <i class="fa-solid fa-plus"></i>&nbsp;Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <br>
                <!-- <h5 class="card-header" id="whichGroup">Group name</h5> -->
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Serial No.</th>
                                <th>Group Name</th>
                                <th>Sub Group Name</th>
                                <th>Sub Sub Group Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <!-- Dynamic Content Here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/testGroup-index.js"></script>
@endsection
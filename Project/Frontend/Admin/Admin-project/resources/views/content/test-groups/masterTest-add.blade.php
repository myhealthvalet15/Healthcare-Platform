@extends('layouts/layoutMaster')
@section('title', 'Tests - Test Group')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/form-layouts.js'])
@endsection
@section('content')
<div class="card mb-6">
    <h5 class="card-header">Add Test</h5>
    <form class="card-body needs-validation" novalidate id="master-test">
        <div class="row g-6">
            <!-- Test Name -->
            <div class="col-md-3 mb-3">
                <label class="form-label" for="multicol-test-name">Test
                    Name</label>
                <input type="text" id="test-name" class="form-control" placeholder="Test Name" required />
                <div class="invalid-feedback"> Enter the test name.
                </div>
            </div>
            <!-- Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-group" class="form-label">Group</label>
                <select id="select2Basic-group" class="select2 form-select form-select-lg" required>
                    <option value>Select Group</option>
                </select>
                <div class="invalid-feedback"> Select a group. </div>
            </div>
            <!-- Sub Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-sub-group" class="form-label">Sub
                    Group</label>
                <select id="select2Basic-sub-group" class="select2 form-select form-select-lg">
                    <option value>Select Sub Group</option>
                </select>
                <div class="invalid-feedback"> Select a sub-group. </div>
            </div>
            <!-- Sub Sub Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-sub-sub-group" class="form-label">Sub
                    Sub Group</label>
                <select id="select2Basic-sub-sub-group" class="select2 form-select form-select-lg">
                    <option value>Select Sub Sub Group</option>
                </select>
                <div class="invalid-feedback"> Select a sub-sub group.
                </div>
            </div>
            <!-- Description -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="basic-default-description">Description</label>
                <textarea id="basic-default-description" class="form-control"
                    placeholder="Write the description here"></textarea>
                <div class="invalid-feedback"> Provide a description.
                </div>
            </div>
            <!-- Remarks -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="basic-default-remarks">Remarks</label>
                <textarea id="basic-default-remarks" class="form-control"
                    placeholder="Write the remarks here"></textarea>
                <div class="invalid-feedback"> Provide remarks. </div>
            </div>
            <!-- Type Selection -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-type" class="form-label">Type</label>
                <select id="select2Basic-type" class="select2 form-select form-select-lg" required>
                    <option value="text">Text</option>
                    <option value="numeric">Numeric</option>
                </select>
                <div class="invalid-feedback"> Select a type. </div>
            </div>
            <!-- Unit -->
            <div class="col-md-3 mb-3">
                <label class="form-label" for="multicol-unit">Unit</label>
                <input type="text" id="multicol-unit" class="form-control" placeholder="Unit" />
                <div class="invalid-feedback"> Enter a unit. </div>
            </div>
            <!-- Dynamic Text Inputs -->
            <div class="col-md-6 mb-3" id="text-input-container" style="display: none;">
                <label class="form-label">Add Conditions</label>
                <div id="dynamic-text-fields">
                    <div class="d-flex mb-2 align-items-center">
                        <input type="text" class="form-control me-2 condition-input" placeholder="Enter condition"
                            name="basic-default-text-condition[]" />
                        <button type="button" class="btn btn-sm btn-success add-row">+</button>
                        <div class="invalid-feedback">Enter at least one
                            condition.</div>
                    </div>
                </div>
            </div>
            <!-- Numeric Dropdown (Initially Hidden) -->
            <div class="col-md-6 mb-3" id="numeric-dropdown-container" style="display: none;">
                <label class="form-label">Numeric</label>
                <select id="numeric-dropdown" class="select2 form-select form-select-lg" required>
                    <option value="no-age-range">No age range</option>
                    <option value="multiple-age-range">Multiple age
                        range</option>
                    <option value="multiple-text-value">Multiple text
                        value</option>
                    <option value="just-values">Just values</option>
                </select>
                <div class="invalid-feedback"> Select a numeric type</div>
            </div>
            <!-- Submit Button -->
            <div class="pt-6">
                <button type="submit" id="submitButton" class="btn btn-primary me-4"><i
                        class="fa-solid fa-plus"></i>&nbsp;Add</button>
            </div>
        </div>
    </form>
</div>
<script src="/lib/js/page-scripts/masterTest-add.js"></script>
@endsection
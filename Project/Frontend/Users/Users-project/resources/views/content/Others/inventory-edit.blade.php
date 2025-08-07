@extends('layouts/layoutMaster')

@section('title', 'Edit Inventory - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite(['resources/assets/js/form-wizard-numbered.js', 'resources/assets/js/form-wizard-validation.js'])
@endsection
<link rel="stylesheet" href="/lib/css/page-styles/inventory-edit.css">
<!-- Include jQuery from CDN (Content Delivery Network) -->
@section('content')
    <!-- Validation Wizard -->
    <div class="col-12 mb-6">
        <div id="wizard-validation" class="bs-stepper mt-2">
            <div class="bs-stepper-content" style="display:block;">
                <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                    <button type="button" class="btn btn-primary" id="back-to-list"
                        onclick="window.location.href='/others/inventory'" style="margin-right: 20px;">Back to
                        Inventory</button>
                </div>
                <div id="inventory-display" class="inventory-grid">
                    <div class="inventory-item">
                        <strong>Equipment Name:</strong> <span id="equipment_name_display"></span>
                    </div>
                    <div class="inventory-item">
                        <strong>Equipment Code:</strong> <span id="equipment_code_display"></span>
                    </div>
                    <div class="inventory-item">
                        <strong>Equipment Cost:</strong> <span id="equipment_cost_display"></span>
                    </div>
                    <div class="inventory-item">
                        <strong>Manufacturer Name:</strong> <span id="manufacturers_display"></span>
                    </div>
                    <div class="inventory-item">
                        <strong>Manufacture Date:</strong> <span id="manufacture_date_display"></span>
                    </div>
                    <div class="inventory-item">
                        <strong>Vendor:</strong> <span id="vendors_display"></span>
                    </div>
                    <div class="inventory-item">
                        <strong>Purchase Date:</strong> <span id="purchase_date_display"></span>
                    </div>
                    <div class="inventory-item">
                        <strong>Purchase Order:</strong> <span id="purchase_order_display"></span>
                    </div>

                </div>
                <form id="wizard-validation-form" method="post">
                    <!-- Account Details -->
                    <div id="account-details-validation" class="content" style="display:block;">
                        <div class="row g-6">
                            <!-- Calibration Date -->
                            <div class="col-sm-2" style="margin-left:10px;">
                                <label for="calibrated_date" class="form-label" required>Calibration Date</label>
                                <input type="date" id="calibrated_date" class="form-control">
                            </div>

                            <!-- Comment Section -->
                            <div class="col-sm-3" style="margin-left: 10px;">
                                <label for="comments" class="form-label">Comments</label>
                                <textarea id="comments" class="form-control" rows="4" style="height:10px;" placeholder="Enter your comments here"
                                    style="width: 100%;"></textarea>
                            </div>

                            <!-- Next Calibration Date -->
                            <div class="col-sm-2" style="margin-left: 10px;">
                                <label for="next_calibration_date" class="form-label" required>Next Calibration Date</label>
                                <input type="date" id="next_calibration_date" class="form-control">
                            </div>

                            <!-- Status Toggle -->
                            <div class="col-sm-3" style="margin-left: 20px; display: flex; align-items: center;">
                                <label for="status" class="form-label">Usage Status</label><br>
                                <label class="switch" style="margin-left: 10px;">
                                    <input type="checkbox" id="status-toggle">
                                    <span class="slider round"></span>
                                </label>
                                <span id="status-label" style="margin-left: 10px;">In Use</span>
                            </div>

                            <input type="hidden" id="corporate_inventory_id">

                            <!-- Save and Cancel Buttons -->
                            <div class="col-sm-12" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                                <button type="button" class="btn btn-primary" id="edit-inventory"
                                    style="margin-left: 10px;">Save</button>
                                <button type="reset" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas"
                                    style="margin-left: 10px;">Cancel</button>
                            </div>
                        </div>
                        <br /><br />
                    </div>
                </form>

                <table id="calibration-history-table" class="table table-bordered">
                    <thead>

                        <tr class="advance-search mt-3">
                            <th colspan="9" style="background-color:rgb(107, 27, 199);" rowspan="1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- Text on the left side -->
                                    <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">Calibration
                                        History</span>
                            </th>
                        </tr>

                    </thead>
                    <thead>
                        <tr>
                            <th>Calibration Date</th>
                            <th>Comments</th>
                            <th>Status</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody id="calibration-history-list">
                        <!-- Calibration History entries will be added here -->
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <!-- /Validation Wizard -->


    </div>
<script>
        $('#corporate_inventory_id').val("{{ $inventory['corporate_inventory_id'] ?? '' }}");
    $('#equipment_name_display').text("{{ $inventory['equipment_name'] ?? '' }} ");
    $('#equipment_code_display').text("{{ $inventory['equipment_code'] ?? '' }} ");

    $('#equipment_cost_display').text("Rs. " + ({{ $inventory['equipment_cost'] ?? '0' }}).toLocaleString());
$('#manufacturers_display').text("{{ $inventory['manufacturers'] ?? '' }}");
$('#vendors_display').text("{{ $inventory['vendors'] ?? '' }}");
$('#purchase_order_display').text("{{ $inventory['purchase_order'] ?? '' }}");
$('#manufacture_date_display').text(moment("{{ $inventory['manufacture_date'] ?? '' }}").format('DD-MM-YYYY'));
$('#purchase_date_display').text(moment("{{ $inventory['date'] ?? '' }}").format('D-M-Y'));
</script>

    <script src="/lib/js/page-scripts/inventory-edit.js"></script>
@endsection

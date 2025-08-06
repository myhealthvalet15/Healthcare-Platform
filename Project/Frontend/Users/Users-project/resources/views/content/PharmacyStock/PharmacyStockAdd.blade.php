@extends('layouts/layoutMaster')

@section('title', 'Add Drug Template - Forms')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection
<link rel="stylesheet" href="/lib/css/page-styles/PharmacyStockAdd.css">
@section('content')


    <div class="col-12 mb-6">
        <div id="wizard-validation" class="bs-stepper mt-2">

            <div class="bs-stepper-content">
                <form id="wizard-validation-form" method="post">
                    <!-- Select Drug Section -->
                    <div class="row g-6">
                        <div class="col-md-4">
                            <label for="drug_template" class="form-label">Select Drug</label>
                            <select id="drug_template" class="form-control select2" required>
                                <option value="">Select a Drug</option>
                                <!-- Add drug options dynamically -->
                            </select>
                        </div>
                    </div>

                    <!-- Drug Details Section (Initially hidden) -->
                    <div id="drug_details_section" style="display: none;">
                        <div class="row g-6">
                            <!-- First Column -->

                            <div class="col-md-4">
                                <label for="manufacturer" class="form-label">Manufacturer</label>
                                <input type="text" id="manufacturer" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="hsn_code" class="form-label">HSN Code</label>
                                <input type="text" id="hsn_code" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="restock_count" class="form-label">Re-stock Count</label>
                                <input type="text" id="restock_count" class="form-control" readonly>
                            </div>

                            <!-- Second Column -->
                            <div class="col-md-4">
                                <label for="id_no" class="form-label">ID No</label>
                                <input type="text" id="id_no" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="bill_status" class="form-label">Bill Status</label>
                                <input type="text" id="bill_status" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="schedule" class="form-label">Schedule</label>
                                <input type="text" id="schedule" class="form-control" readonly>
                            </div>

                            <!-- Third Column -->
                            <div class="col-md-4">
                                <label for="drug_ingredient" class="form-label">Drug Ingredient</label>
                                <input type="text" id="drug_ingredient" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="crd" class="form-label">CRD</label>
                                <input type="text" id="crd" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="mrp" class="form-label">MRP</label>
                                <input type="text" id="mrp" class="form-control" readonly>
                            </div>

                            <!-- First Column -->
                            <div class="col-md-4">
                                <label for="package_unit" class="form-label">Package Unit</label>
                                <input type="text" id="package_unit" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="mrp_per_unit" class="form-label">MRP Per Unit</label>
                                <input type="text" id="mrp_per_unit" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="unit_to_issue" class="form-label">Unit to Issue</label>
                                <input type="text" id="unit_to_issue" class="form-control" readonly>
                            </div>

                            <!-- Second Column -->
                            <div class="col-md-4">
                                <label for="sgst" class="form-label">SGST</label>
                                <input type="text" id="sgst" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="cgst" class="form-label">CGST</label>
                                <input type="text" id="cgst" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="igst" class="form-label">IGST</label>
                                <input type="text" id="igst" class="form-control" readonly>
                            </div>

                            <!-- Third Column -->
                            <div class="col-md-4">
                                <label for="discount" class="form-label">Discount</label>
                                <input type="text" id="discount" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="text" id="quantity" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="manufacture_date" class="form-label">Manufacture Date</label>
                                <input type="text" id="manufacture_date" class="form-control"
                                    placeholder="DD/MM/YYYY">

                            </div>
                            <div class="col-md-4">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="text" id="expiry_date" class="form-control" placeholder="DD/MM/YYYY">
                            </div>
                            <div class="col-md-4">
                                <label for="drug_batch" class="form-label">Drug Batch</label>
                                <input type="text" id="drug_batch" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons for submitting or cancelling -->
                    <div class="col-sm-12 mt-4">
                        <input type="hidden" id="drug_name" class="form-control">
                        <input type="hidden" id="drug_type" class="form-control">
                        <input type="hidden" id="drug_strength" class="form-control">
                        <input type="hidden" id="sold_quantity" class="form-control">
                        <input type="hidden" id="amount_per_tab" class="form-control">
                        <input type="hidden" id="ohc" class="form-control">
                        <input type="hidden" id="master_pharmacy_id" class="form-control">
                        <input type="hidden" id="tabletInStrip" class="form-control">
                        <input type="hidden" id="ohc_pharmacy_id" class="form-control">

                        <button type="button" class="btn btn-primary" id="add-pharmacystock">Save Changes</button>

                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="/lib/js/page-scripts/PharmacyStockAdd.js"></script>
@endsection

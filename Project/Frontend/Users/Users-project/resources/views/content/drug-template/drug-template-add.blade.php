@extends('layouts/layoutMaster')

@section('title', 'Add Drug Template - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
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

                        <div class="row g-6">
                            <div class="col-sm-6">
                                <label for="drug_type_name" class="form-label">Drug Type Name</label>
                                <input type="text" id="drug_type_name" name="drug_type_name" class="form-control"
                                    placeholder="Enter Drug Type Name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="drug_type_manufacturer" class="form-label">Drug Manufacturer</label>
                                <input type="text" id="drug_type_manufacturer" class="form-control"
                                    placeholder="Enter Drug Manufacturer" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationDrugType">Drug Type</label>
                                <select id="drug_type" class="form-control select2" required>
                                    <!-- Options will be added dynamically -->
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationDrugIngerdient" required>Drug
                                    Ingredients</label>
                                <select id="drug_ingredients" class="form-control select2" multiple>
                                    <!-- Options will be added dynamically -->
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationDrugStength" required>Drug Strength</label>
                                <input type="text" id="drug_strength" class="form-control"
                                    placeholder="Enter Drug Strength">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationRestockalert" required>Restock Alert
                                    Count</label>
                                <input type="text" id="restock_alert_count" class="form-control"
                                    placeholder="Enter Restock Alert Count">
                            </div>


                            <div class="col-sm-6">
                                <label for="schedule" class="form-label">Schedule</label>
                                <input type="text" id="schedule" class="form-control" placeholder="Enter Schedule">
                            </div>
                            <div class="col-sm-6">
                                <label for="id_no" class="form-label" required>ID Number</label>
                                <input type="text" id="id_no" class="form-control" placeholder="Enter ID Number">
                            </div>
                            <div class="col-sm-6">
                                <label for="hsn_code" class="form-label" required>HSN Code</label>
                                <input type="text" id="hsn_code" class="form-control" placeholder="Enter HSN Code">
                            </div>
                            <div class="col-sm-6">
                                <label for="unit_issue" class="form-label" required>Unit to Issue</label>
                                <input type="text" id="unit_issue" class="form-control" placeholder="Enter Unit Issue">
                            </div>
                            <div class="col-sm-6">
                                <label for="amount_per_strip" class="form-label" required>Amount Per Strip</label>
                                <input type="text" id="amount_per_strip" class="form-control"
                                    placeholder="Enter Per Strip">
                            </div>
                            <div class="col-sm-6">
                                <label for="tablet_in_strip" class="form-label" required>Tablet in Strip</label>
                                <input type="text" id="tablet_in_strip" class="form-control"
                                    placeholder="Enter Tablet in Strip">
                            </div>
                            <div class="col-sm-6">
                                <label for="amount_per_tab" class="form-label" required>Amount per Tab</label>
                                <input type="text" id="amount_per_tab" class="form-control"
                                    placeholder="Enter Amount per Tab">
                            </div>
                            <div class="col-sm-6">
                                <label for="discount" class="form-label" required>Discount</label>
                                <input type="text" id="discount" class="form-control" placeholder="Enter Discount">
                            </div>
                            <div class="col-sm-6">
                                <label for="sgst" class="form-label" required>SGST</label>
                                <input type="text" id="sgst" class="form-control" placeholder="Enter SGST"
                                    required>
                            </div>
                            <div class="col-sm-6">
                                <label for="cgst" class="form-label" required>CGST</label>
                                <input type="text" id="cgst" class="form-control" placeholder="Enter CGST"
                                    required>
                            </div>
                            <div class="col-sm-6">
                                <label for="igst" class="form-label">IGST</label>
                                <input type="text" id="igst" class="form-control" placeholder="Enter IGST"
                                    required>
                            </div>

                            <div class="col-sm-6">
                                <label for="bill_status" class="form-label">Bill Status</label>
                                <label class="switch">
                                    <input type="checkbox" class="switch-input" id="bill_status">
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label" id="bill-status-label">Inactive</span>
                                </label>
                            </div>
                            <div class="col-sm-6">

                                <div class="form-check">
                                    <input type="checkbox" id="otc" name="otc" class="form-check-input">
                                    <label class="form-check-label" for="otc">OTC</label>
                                    &nbsp; &nbsp;
                                    <input type="checkbox" id="crd" name="crd">
                                    <label class="form-check-label" for="crd">CRD</label>
                                </div>

                            </div>




                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" id="add-drugtype">Save Changes</button>
                                <button type="reset" class="btn btn-label-danger waves-effect"
                                    data-bs-dismiss="offcanvas">Cancel</button>

                            </div>

                        </div>
                        <br /><br />
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- /Validation Wizard -->
    </div>
    <script src="/lib/js/page-scripts/drug-template-add.js"></script>
@endsection

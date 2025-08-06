@extends('layouts/layoutMaster')

@section('title', 'Add New Inventory - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
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
                        <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                            <button type="button" class="btn btn-primary" id="back-to-list"
                                onclick="window.location.href='/others/inventory'" style="margin-right: 20px;">Back to
                                Inventory</button>
                        </div>
                        <div class="row g-6">
                            <div class="col-sm-6">
                                <label for="date" class="form-label">Purchase Date</label>
                                <input type="date" id="date" name="date" class="form-control"
                                    placeholder="Enter Purchase Date" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="purchase_order" class="form-label">Purchase Order</label>
                                <input type="text" id="purchase_order" class="form-control"
                                    placeholder="Enter Purchase Order" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="equipment_name" class="form-label">Equipment Name</label>
                                <input type="text" id="equipment_name" class="form-control"
                                    placeholder="Enter Equipment Name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="equipment_code" class="form-label">Equipment Code</label>
                                <input type="text" id="equipment_code" class="form-control"
                                    placeholder="Enter Equipment Code" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="equipment_lifetime" required> Life Time</label>
                                <input type="text" id="equipment_lifetime" class="form-control"
                                    placeholder="Enter Equipment Life time">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="manufacturers" required>Manufacturer Name
                                </label>
                                <input type="text" id="manufacturers" class="form-control"
                                    placeholder="Enter Manufacturer Name">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="manufacture_date" required>Manufacturer Date
                                </label>
                                <input type="date" id="manufacture_date" class="form-control"
                                    placeholder="Enter Manufacturer Date">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="equipment_cost" required>Equipment Cost </label>
                                <input type="text" id="equipment_cost" class="form-control"
                                    placeholder="Enter Equipment Cost">
                            </div>

                            <div class="col-sm-6">
                                <label for="vendors" class="form-label">Vendor</label>
                                <input type="text" id="vendors" class="form-control" placeholder="Enter Vendor">
                            </div>
                            <div class="col-sm-6">
                                <label for="calibrated_date" class="form-label" required>Callibration Date</label>
                                <input type="date" id="calibrated_date" class="form-control"
                                    placeholder="Enter Callibration Date">
                            </div>


                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" id="add-drugtype">Save </button>
                                <button onclick="window.location.href='/others/inventory'"
                                    class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancel</button>

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

    <script src="/lib/js/page-scripts/inventory-add.js"></script>
@endsection

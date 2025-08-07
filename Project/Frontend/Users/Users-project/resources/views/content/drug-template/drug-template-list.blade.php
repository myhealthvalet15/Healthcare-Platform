@extends('layouts/layoutMaster')
@section('title', 'Drug - Template')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')

<!-- Basic Bootstrap Table -->

<div class="card">

    <div class="d-flex justify-content-end align-items-center card-header">
        <a href="/drugs/drug-template-add" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New
                    Drug Template</span></span>
        </a>

        <!-- Add Modal -->

    </div>
    <div class="card-datatable table-responsive pt-0" style="margin-top:-30px;">
        <table class="datatables-basic table">
            <thead>
                <tr class="advance-search mt-3">
                    <th colspan="9" style="background-color:rgb(107, 27, 199);">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Text on the left side -->
                            <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Pharmacy Stock</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Drug Details</h5>
                <span>Drug template Id: #<span id="modaldrug_template_id"></span></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Container for image and text -->
                <div class="d-flex">
                    <!-- Left side (Text content) -->

                    <div>
                        <p><strong>Drug name :</strong> <span id="modalName"></span></p>
                        <p><strong>Drug Strength :</strong> <span id="modaldrug_strength"></span></p>
                        <p><strong>Drug Type :</strong> <span id="modalNdrug_type"></span></p>
                        <p><strong>Drug manufacturer :</strong> <span id="modaldrug_manufacturer"></span> <span
                                id="modalgender"></span></p>
                        <p><strong>Drug Ingredients :</strong> <span id="modaldrug_ingredient"></span></p>
                        <p><strong>Restock Count :</strong> <span id="modalrestock_alert_count"></span></p>
                        <p><strong>CRD :</strong> <span id="modalcrd"></span></p>
                        <p><strong>Schedule :</strong> <span id="modalschedule"></span></p>
                        <p><strong>HSN Code :</strong> <span id="modalhsn_code"></span></p>
                        <p><strong>Amount Per Strip :</strong> <span id="modalamount_per_strip"></span></p>
                        <p><strong>Unit to Issue :</strong> <span id="modalunit_issue"></span></p>
                        <p><strong>Amount Per Tab :</strong> <span id="modalamount_per_tab"></span></p>
                        <p><strong>Discount :</strong> <span id="modaldiscount"></span></p>
                        <p><strong>SGST :</strong> <span id="modalsgst"></span></p>
                        <p><strong>CGST :</strong> <span id="modalcgst"></span></p>
                        <p><strong>IGST :</strong> <span id="modaligst"></span></p>
                        <p><strong>Bill Status :</strong> <span id="modalbill_status"></span></p>
                    </div>

                    <!-- Right side (Image with padding) -->

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</div>
<hr class="my-12">

<script>
    var drugtemplates = @json($drugtemplates);
    var drugTypes = @json($drugTypes);
    var drugIngredients = @json($drugIngredients);
    var drugTypeName = @json($drugTypes)[rowData.drug_type] || 'Unknown';
    var drugIngredientsId = @json($drugIngredients)[id] || null;
</script>
  <script src="/lib/js/page-scripts/drug-template-list.js"></script>
@endsection
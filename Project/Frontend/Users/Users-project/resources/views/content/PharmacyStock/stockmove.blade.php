@extends('layouts/layoutMaster')
@section('title', 'Pharmacy - Stock List')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')
<style>
    td:nth-child(1),
    th:nth-child(1) {
        /* Assuming this is the first column */
        width: 300px;
        /* Adjust to your desired width */
    }
</style>

<!-- Basic Bootstrap Table -->

<div class="card">


    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table">
            <thead>
                <tr class="advance-search mt-3">
                    <th colspan="9" style="background-color:rgb(107, 27, 199);">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Text on the left side -->
                            <span style="color:#fff;" id="employeeTypeLabel">List of Stocks</span>
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
                <span>Pharmacy Stock Id: #<span id="modaldrug_template_id"></span></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Container for image and text -->
                <div class="d-flex">
                    <!-- Left side (Text content) -->

                    <div>
                        <p><strong>Drug name - Strength (Type) :</strong> <span id="modalName"></span>&nbsp;-&nbsp;
                            <span id="modaldrug_strength"></span>&nbsp;-&nbsp;(<span id="modalNdrug_type"></span>)</p>
                        <p><strong>Drug manufacturer :</strong> <span id="modaldrug_manufacturer"></span> <span
                                id="modalgender"></span></p>
                        <p><strong>Id No / HSN Code :</strong> <span id="modalhsn_code"></span> / <span
                                id="modalid_no"></span></p>
                        <p><strong>Restock Count :</strong> <span id="modalrestock_alert_count"></span></p>
                        <p><strong>Bill Status :</strong> <span id="modalbill_status"></span></p>
                        <p><strong>Schedule :</strong> <span id="modalschedule"></span></p>
                        <p><strong>CRD :</strong> <span id="modalcrd"></span></p>
                        <p><strong>MRP / MRP Per Unit</strong> <span id="modalamount_per_strip"></span> / <span
                                id="modalamount_per_tab"></p>
                        <p><strong>Package Unit :</strong> <span id="modaltablet_in_strip"></span></p>
                        <p><strong>Unit to Issue :</strong> <span id="modalunit_issue"></span></p>
                        <p><strong>SGST / CGST / IGST </strong> <span id="modalsgst"></span> / <span
                                id="modalcgst"></span> / <span id="modaligst"></span></p>
                        <p><strong>Discount :</strong> <span id="modaldiscount"></span></p>
                        <p><strong>Quantity :</strong> <span id="modalquantity"></span></p>
                        <p><strong>Current Availability :</strong> <span id="modalcurrent_availability"></span></p>
                        <p><strong>Change Stock:</strong> <input type="text" id="modalchange_quantity"
                                class="form-control" />
                        <div>
                            <label><input type="radio" name="qty_action" value="add"> + Plus</label>
                            <label><input type="radio" name="qty_action" value="remove"> - Minus</label>
                        </div>
                        </p>
                        <p><input type="hidden" id="modaldid" class="form-control" /></p>




                        <p><strong>Drug Batch:</strong> <input type="text" id="modaldrug_batch" class="form-control" />
                        </p>

                        <p><strong>Manufacturer Date:</strong>
                            <input type="date" id="modalmanufacter_date" class="form-control" />
                        </p>

                        <p><strong>Expiry Date:</strong>
                            <input type="date" id="modalexpiry_date" class="form-control" />
                        </p>




                    </div>

                    <!-- Right side (Image with padding) -->

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="submitQuantity" data-id="modaldid"
                    class="btn btn-primary mt-3">Submit</button>
            </div>
        </div>
    </div>
</div>

</div>
<hr class="my-12">

 <script src="/lib/js/page-scripts/stockmove.js?v=time()"></script>
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
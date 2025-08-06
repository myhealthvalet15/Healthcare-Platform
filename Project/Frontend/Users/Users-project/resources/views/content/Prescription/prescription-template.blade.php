@extends('layouts/layoutMaster')
@section('title', 'Prescription Template List')
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
<link rel="stylesheet" href="/lib/css/page-styles/prescription-template.css">

@section('content')



    <!-- Basic Bootstrap Table -->

    <div class="card">

        <div class="d-flex justify-content-end align-items-center card-header">

            <a href="https://login-users.hygeiaes.com/prescription/prescription-template-add"
                class="btn btn-secondary add-new btn-primary waves-effect waves-light">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New
                        Template</span></span>
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
                                <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of
                                    Prescription</span>
                            </div>
                        </th>
                    </tr>
                    <tr>
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
        <div class="modal-dialog modal-lg"> <!-- You can adjust modal size (e.g., 'modal-lg') -->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex justify-content-between w-100">
                        <!-- Prescription Template Details on the left -->
                        <span style="color: #000; font-weight: normal; font-size: 18px;" id="employeeModalLabel"> </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Table structure for displaying drug details -->
                    <table class="table table-striped">
                        <thead>

                            <tr>
                                <th>Drug Name - Strength (Type)</th>
                                <th>Days</th>
                                <th><img src="/assets/img/prescription-icons/morning.png">
                                </th>
                                <th><img src="/assets/img/prescription-icons/noon.png" align="absmiddle"> </th>
                                <th> <img src="/assets/img/prescription-icons/evening.png">
                                </th>
                                <th> <img src="/assets/img/prescription-icons/night.png">
                                </th>
                                <th>Remarks</th>
                                <th>AF/BF</th>
                            </tr>
                        </thead>
                        <tbody id="prescriptionTemplateTableBody">
                            <!-- Dynamic rows will be appended here -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <hr class="my-12">
    <script src="/lib/js/page-scripts/prescription-template.js"></script>
@endsection

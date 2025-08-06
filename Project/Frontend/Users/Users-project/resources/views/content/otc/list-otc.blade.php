@extends('layouts/layoutMaster')
@section('title', 'OTC LIST')
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
    <style>
        .highlight-row {
            background-color: #ffeb3b !important;
            /* Yellow background color */
            color: #000;
            /* Black text color */
        }
    </style>


    <!-- Basic Bootstrap Table -->

    <div class="card">

        <div class="d-flex justify-content-end align-items-center card-header">

            <a href="{{ route('otc-add-otc') }}" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New
                        OTC</span></span>
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
                                <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of OTC</span>
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

    <script src="/lib/js/page-scripts/list-otc.js"></script>

@endsection

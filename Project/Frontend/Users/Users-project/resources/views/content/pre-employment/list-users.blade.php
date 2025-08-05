@extends('layouts/layoutMaster')
@section('title', 'List Users')
<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite(['resources/assets/js/form-wizard-numbered.js'])
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('lib/js/page-scripts/pre-employment.js') }}?v=time()"></script>

@section('content')
    <!-- Default -->

    <div class="card">

        <div class="card-datatable table-responsive pt-0" style="margin-top:10px;">
            <table class="datatables-basic table">
                <thead>
                    <tr class="advance-search mt-3">
                        <th colspan="6" style="background-color:rgb(107, 27, 199);">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Text on the left side -->
                                <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Users</span>
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

                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

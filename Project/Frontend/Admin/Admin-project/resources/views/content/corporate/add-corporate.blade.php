@extends('layouts/layoutMaster')

@section('title', 'Add Corporate Users')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/dropzone/dropzone.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/dropzone/dropzone.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
    <div id="preloader" class="text-center py-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Datas are preparing for a view ...</p>
    </div>
    <div class="row">
        <!-- Data Section: 35% Width -->
        <div class="col-md-7">
            <div class="card mb-6">
                <h5 class="card-header">Uploaded Files<br>(recently uploaded 5 files
                    will be shown here.)</h5>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <div id="preloader_history" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Fetching Upload History ...</p>
                        </div>
                        <tbody id="fileTableBody">
                            <!-- Rows will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Upload Section: 65% Width -->
        <div class="col-md-5">
            <div class="card mb-6">
                <div class="card-widget-separator-wrapper">
                    <div class="card-body card-widget-separator">
                        <div class="row gy-4 gy-sm-1">
                            <!-- Dashboard Stats -->
                            <div class="col-12">
                                <div
                                    class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
                                    <div>
                                        <div id="preloader_mc" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p>Fetching Total master user
                                                details</p>
                                        </div>
                                        <div id="masterUserDetailsContent" style="display: none;">
                                            <p class="mb-1">Total records in Master
                                                Users</p>
                                            <h4 class="mb-1" id="masterUserCount"></h4>
                                            <p class="mb-0"><span class="me-2">5k
                                                    orders</span><span class="badge bg-label-success">+5.7%</span></p>
                                        </div>
                                    </div>
                                    <span class="avatar me-sm-6">
                                        <span class="avatar-initial rounded"><i
                                                class="ti-28px ti ti-smart-home text-heading"></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Add Users</h5>
                    <div>
                        <!-- First Dropdown -->
                        <select id="corporateDropdown" class="form-select form-select-sm d-inline-block me-2"
                            style="width: auto;" disabled>
                            <option value selected disabled>Select
                                Corporate</option>
                        </select>
                        <!-- Second Dropdown -->
                        <select id="locationDropdown" class="form-select form-select-sm d-inline-block" style="width: auto;"
                            disabled>
                            <option value selected disabled>Select a corporate
                                first</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Dropzone Form -->
                    <form action="/" class="dropzone needsclick" id="dropzone-basic" method="post">
                        <div class="dz-message needsclick">
                            Drop files here or click to upload
                            <span class="note needsclick">(Only .xls, .xlsx files
                                with 1000 rows are allowed.)</span>
                        </div>
                    </form>
                    <!-- Upload Button -->
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary" id="final-upload-btn">
                            <i class="ti ti-upload me-2"></i>Upload File
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/lib/js/page-scripts/add-excel.js"></script>
@endsection

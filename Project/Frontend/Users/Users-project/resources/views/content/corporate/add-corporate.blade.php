@extends('layouts/layoutMaster')

@section('title', 'Add Corporate Users')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/dropzone/dropzone.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/dropzone/dropzone.js'])
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
                                    <!-- <div id="preloader_mc" class="text-center py-4">
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
                                    </div> -->
                                    <div class="template-excel-download">
                                        <a
                                            href="/lib/excelTemplates/template.xlsx"
                                            download>
                                            Download Excel Template Here
                                            &nbsp;<i
                                                class="fa-solid fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-6">
            <div class="card-body">
                <!-- Display Current Corporate and Location -->
                <p class="text-danger">
                    You are currently going to upload to this <br>corporate:
                    <strong>{{ session('corporate_name') }}</strong> in this
                    location:
                    <strong>{{ session('location_name') }}</strong>.
                </p>
                <!-- Dropzone Form -->
                <form action="/" class="dropzone needsclick" id="dropzone-basic"
                    method="post">
                    <input type="hidden" id="corporate_id"
                        value="<?php echo session('corporate_id'); ?>">
                    <input type="hidden" id="location_id"
                        value="<?php echo session('location_id'); ?>">
                    <input type="hidden" id="corporate_name"
                        value="<?php echo session('corporate_name'); ?>">
                    <input type="hidden" id="location_name"
                        value="<?php echo session('location_name'); ?>">
                    <div class="dz-message needsclick">
                        Drop files here or click to upload
                        <span class="note needsclick">(Only .xls, .xlsx files
                            with 1000 rows are allowed.)</span>
                    </div>
                </form>
                <!-- Upload Button -->
                <div class="card-footer text-end">
                    <button type="button" class="btn btn-primary"
                        id="final-upload-btn">
                        <i class="ti ti-upload me-2"></i>Upload File
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/add-excel.js?v=time()"></script>
@endsection
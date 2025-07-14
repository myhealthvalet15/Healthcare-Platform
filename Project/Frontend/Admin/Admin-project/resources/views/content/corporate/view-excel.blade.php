@extends('layouts.layoutMaster')

@section('title', 'View Excel Data')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
    <div class="col-12">
        <div class="card mb-6">
            <!-- Card Header with Buttons -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0">Preview Excel Data</h5>
                <div>
                    <!-- Add Data Button Form -->
                    <form id="addDataForm" data-route="{{ route('add-corporate-excel') }}" method="POST"
                        style="display: inline;">
                        @csrf
                        <input type="hidden" name="file_name" value="{{ $fileName }}">
                        <button id="addDataBtn" type="submit" class="btn btn-primary btn d-none">Add
                            Data</button>
                    </form>

                    <!-- Revoke Button Form -->
                    <form id="revokeDataForm" data-route="{{ route('delete-corporate-excel') }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="file_name" value="{{ $fileName }}">
                        <button id="revokeBtn" type="submit" class="btn btn-danger btn d-none">Revoke</button>
                    </form>
                </div>
            </div>

            <!-- Scroll Alert -->
            <div class="alert alert-info" style="padding: 5px; font-size: 0.9rem; text-align: center;">
                👉 Scroll horizontally to view all columns 👈
            </div>

            <!-- Preloader -->
            <div id="preloader" class="text-center py-4" style="display: block;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Importing Datas ...</span>
                </div>
                <p id="preloader-text">Importing Datas ...</p>
            </div>

            <!-- Scrollable Table -->
            <div class="card-body" style="overflow-x: auto; position: relative; display: none;" id="excelTableContainer">
                <div style="min-width: max-content;">
                    {!! $htmlContent !!}
                </div>
            </div>
        </div>
    </div>
    <script src="/lib/js/page-scripts/view-excel.js"></script>
@endsection

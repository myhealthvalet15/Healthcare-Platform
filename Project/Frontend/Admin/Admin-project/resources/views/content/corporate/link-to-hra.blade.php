@extends('layouts/layoutMaster')
@section('title', ' Link to HRA - Corporate')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/forms-selects.js',
'resources/assets/js/form-layouts.js',
'resources/assets/js/forms-typeahead.js'
])
@endsection
@section('content')
<style>
    .link-to-hra {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 200px;
        text-align: center;
        margin-top: 75px;
    }

    .spinner-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sk-bounce {
        display: flex;
        justify-content: space-between;
        width: 50px;
    }

    .sk-bounce-dot {
        width: 30px;
        height: 30px;
        margin: 0 5px;
        background-color: #007bff;
        border-radius: 50%;
        animation: sk-bounce 1.4s infinite ease-in-out both;
    }
</style>
<div class="card mb-6">
    <h5 class="card-header">Link Corporates to HRA</h5>
    <div class="link-to-hra" id="link-to-hra" style="display: block;">
        <div class="spinner-container">
            <div class="sk-bounce sk-primary">
                <div class="sk-bounce-dot"></div>
                <div class="sk-bounce-dot"></div>
            </div>
            <label id="spinnerLabeltext">retrieving datas ...</label>
        </div>
    </div>
    <div id="linkForm" style="display: none;">
        <form class="card-body" id="addLink2HraForm">
            <div class="row g-6">
                <div class="col-md-6 mb-6">
                    <label for="select2Basic" class="form-label">Select Corporate</label>
                    <select id="select2Basic" class="select2 form-select form-select-lg" data-allow-clear="true">
                        <option value="">Select value</option>
                    </select>
                </div>
                <div class="col-md-6 mb-6">
                    <label for="select2Primary" class="form-label">Select Template</label>
                    <div class="select2-primary">
                        <select id="select2Primary" class="select2 form-select" multiple>
                            <option value="">Select value</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mt-4 text-start">
                    <button type="submit" id="submitButton" class="btn btn-primary">Add&nbsp;&nbsp;<i
                            class="fa-solid fa-plus"></i></button>
                </div>
            </div>
        </form>
        <h5 class="card-header">Linked Corporates</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Corporates</th>
                        <th>Templates</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <tr>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/link-to-hra.js"></script>
@endsection
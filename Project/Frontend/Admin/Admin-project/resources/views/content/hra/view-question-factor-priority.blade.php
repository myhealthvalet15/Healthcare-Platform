@extends('layouts/layoutMaster')
@section('title', 'HRA Question Priority')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/app-ecommerce-category-list.js',
'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/form-basic-inputs.js',
'resources/assets/js/add-question.js',
'resources/assets/js/forms-selects.js', 'resources/assets/js/forms-tagify.js',
'resources/assets/js/forms-typeahead.js'])
@endsection
@section('content')
<div class="row justify-content-center">
    <div id="preloader" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Fetching Datas...</p>
    </div>
    <div class="col-md-12" id="contents-container" style="display: none;">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title mb-0">HRA View Questions</h5>
                <div class="d-flex align-items-center">
                    <label for="adjustment-value" class="me-2" style="margin-top: 20px;">Adjustment Value</label>
                    <input type="text" id="adjustment-value" class="form-control me-2"
                        style="width: 100px;margin-top: 20px;" placeholder="Value">
                    <?php
                    if ($published) { ?>
                    <button id="publish-button" class="btn btn-success" style="margin-top: 20px;" disabled>
                        <i class="bx bx-save me-1"></i> Published
                    </button>
                    <?php } else { ?>
                    <button id="publish-button" class="btn btn-primary" style="margin-top: 20px;">
                        <i class="bx bx-save me-1"></i> Publish
                    </button>
                    <?php }
                    ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-0">{{ $template_name }}</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <span style="font-size: 15px;">(Maximum: 10, Minimum:
                            -10)</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div id="existing-questions-container" class="list-group">
                        <div id="no-factors-message" class="text-center text-muted py-4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const templateId = "{{ $template_id }}";
</script>
<script src="/lib/js/page-scripts/view-question-factor-priority.js"></script>
@endsection
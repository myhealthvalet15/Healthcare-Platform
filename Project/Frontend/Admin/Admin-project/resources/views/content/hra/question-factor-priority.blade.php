@extends('layouts/layoutMaster')
@section('title', 'HRA Question Priority')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sortablejs/sortable.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/app-ecommerce-category-list.js', 'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/form-basic-inputs.js', 'resources/assets/js/extended-ui-drag-and-drop.js',
'resources/assets/js/add-question.js', 'resources/assets/js/forms-selects.js', 'resources/assets/js/forms-tagify.js',
'resources/assets/js/forms-typeahead.js'])
@endsection
@section('content')
<style>
    .factor-row {
        transition: all 0.3s ease;
    }

    .factor-row:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .priority-badge {
        min-width: 30px;
        display: inline-block;
        text-align: center;
        margin-right: 10px;
    }

    #factors {
        height: calc(2.5rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    .factor-row {
        transition: all 0.3s ease;
    }

    .factor-row:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .priority-badge {
        min-width: 30px;
        display: inline-block;
        text-align: center;
        margin-right: 10px;
    }

    #factors {
        height: calc(2.5rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading .btn-text {
        opacity: 0;
    }

    .btn-loading .spinner-border {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 1rem;
        height: 1rem;
    }
</style>
<div class="row justify-content-center">
    <div id="preloader" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Fetching Datas...</p>
    </div>
    <div class="col-md-10" id="contents-container" style="display: none;">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">HRA Set Questions</h5>
                <button id="save-changes" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i> Save Changes
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <h5 class="mb-0">Template: {{ $template_id }}. {{ $template_name }}</h5>
                    </div>
                    <div class="col-md-6" id="factors-dropdown-container">
                        <select id="factors" class="form-select">
                            @php
                            $current_factor_id = request()->segment(5);
                            @endphp
                            @if (count($factors_with_priorities) > 0)
                            @foreach ($factors_with_priorities as $factor_id => $factor)
                            <option value="{{ $factor_id }}" {{ $factor_id==$current_factor_id ? 'selected' : '' }}>
                                {{ $factor['priority'] }}. {{ $factor['name'] }}
                            </option>
                            @endforeach
                            @else
                            <option value>No factors available</option>
                            @endif
                        </select>
                    </div>
                </div>
                <select id="questionsDropdown"
                    class="select2 form-select additional_options select2-hidden-accessible mb-4" tabindex="-1"
                    aria-hidden="true">
                    on value disabled selected>Select a question</option>
                </select>
                <div class="card border shadow-none mt-3">
                    <div class="card-header">
                        <class="card-title mb-0"="">Existing Questions
                        </class="card-title>
                    </div>
                    <div class="card-body p-0">
                        <div id="no-questions-container" class="list-group-item text-center text-muted py-3">
                        </div>
                        <div id="existing-questions-container" class="list-group list-group-flush">
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
<script src="/lib/js/page-scripts/question-factor-priority.js"></script>
@endsection
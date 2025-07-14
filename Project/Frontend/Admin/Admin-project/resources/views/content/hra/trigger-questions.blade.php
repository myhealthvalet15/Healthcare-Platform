@extends('layouts/layoutMaster')

@section('title', 'HRA Factor Priority')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/forms-selects.js', 'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/form-basic-inputs.js', 'resources/assets/js/forms-typeahead.js'])
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
</style>
<div class="row justify-content-center">
    <div id="preloader" class="text-center py-4" style="display: block;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Fetching Datas...</p>
    </div>
    <div class="col-md-10" id="contents-container" style="display: none;">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Add Trigger Questions</h5>
                <div class="ms-auto">
                    <button id="go-back" class="btn btn-secondary">
                        <i class="bx bx-save me-1"></i> Back
                    </button>
                    <button id="save-changes-button" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="card border shadow-none">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Template name:
                            <b>{{ $data['message']['template_name'] }}</b>
                        </h6>
                        <h6 class="card-title mb-0">Factor name:
                            <b>{{ $data['message']['factor_name'] }}</b>
                        </h6>
                        <h6 class="card-title mb-0">Add Trigger Question for
                            <b>{{ $data['message']['question_text'] }}</b>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div id="existing-factors-container" class="list-group list-group-flush">
                            @if (isset($question_data['data']['answer']) && isset($question_data['data']['points']))
                            @php
                            $answers = json_decode($question_data['data']['answer'], true);
                            $points = json_decode($question_data['data']['points'], true);
                            @endphp
                            @if (!empty($answers) && !empty($points))
                            @foreach ($answers as $key => $answer)
                            <div class="list-group-item d-flex align-items-center">
                                <!-- Answer -->
                                <div class="me-3" style="min-width: 150px;">
                                    {{ $answer === 'null' || $answer === null || empty($answer) ? 'N/A' : $answer }}
                                </div>

                                <!-- Points -->
                                <div class="me-3" style="min-width: 50px;">
                                    {{ ($points[$key] ?? null) === null || ($points[$key] ?? null) === 'null' ||
                                    empty($points[$key] ?? null) ? 'N/A' : ($points[$key] ?? 'N/A') }}
                                </div>

                                <!-- Dropdown -->
                                <div class="me-3" style="width: 100%;">
                                    <div class="mb-3">
                                        <label for="select2Multiple_{{ $key }}" class="form-label">Select Trigger
                                            Questions Here</label>
                                        <select id="select2Multiple_{{ $key }}" class="select2 form-select" multiple
                                            style="font-size: 16px;">
                                            <option value="option1" selected disabled>Select a value
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="list-group-item text-center text-muted py-3">
                                No data available.
                            </div>
                            @endif
                            @else
                            <div class="list-group-item text-center text-muted py-3">
                                Invalid data structure.
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Save Changes Button -->
                    <div class="card-footer text-center">
                        <button id="save-changes-button" class="btn btn-primary d-none">
                            <i class="bx bx-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/trigger-questions.js"></script>
@endsection
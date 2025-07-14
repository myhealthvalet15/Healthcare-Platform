@extends('layouts/layoutMaster')
@section('title', 'HRA Add Question')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/katex.scss',
'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/dropzone/dropzone.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/dropzone/dropzone.js',
'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/tagify/tagify.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/app-ecommerce-category-list.js', 'resources/assets/js/ui-toasts.js',
'resources/assets/js/extended-ui-sweetalert2.js', 'resources/assets/js/forms-file-upload.js'])
@endsection
@section('content')
<div class="app-ecommerce">
    <div class="bs-toast toast toast-placement-ex m-2 fade bottom-0 end-0" role="alert" aria-live="assertive"
        aria-atomic="true" data-bs-delay="2000" id="toast">
        <div class="toast-header">
            <i class="ti ti-bell ti-xs me-2 text-danger"></i>
            <div class="me-auto fw-medium">Hra Questions</div>
            <small class="text-muted">Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Text Data here !!
        </div>
    </div>
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
        <div class="d-flex flex-column justify-content-center">
            <h4 class="mb-1">Add new Questions</h4>
            <p class="mb-0">This questions can be linked to hra templates in
                templates section</p>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-4">
            <form action>
                <button type="submit" class="btn btn-primary">Save
                    Question</button>
            </form>
        </div>
    </div>
    <div class="row">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <div class="col-12 col-lg-8">
            <!-- Product Information -->
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Question</h5>
                </div>
                <div class="card-body">
                    <!-- Description -->
                    <div class="mb-4">
                        <textarea class="form-control hra_comments" id="hra-question" placeholder="Type Question Here"
                            name="hra_question" aria-label="hra_question" rows="4"></textarea>
                    </div>
                    <div class="row mb-6">
                        <div class="col">
                            <label class="form-label" for="formula">Formula</label>
                            <input type="text" class="form-control" id="hra-formula" placeholder="Type Formula Here"
                                name="hra_formula" aria-label="hra_formula">
                        </div>
                        <div class="col">
                            <label class="form-label" for="points">Dashboard
                                Text</label>
                            <input type="text" class="form-control" id="dashboard-text"
                                placeholder="Type Dashboard Text Here" name="hra_dashboard" aria-label="dashboard_text">
                        </div>
                    </div>
                    <style>
                    </style>
                    <div id="answer-points-container">
                        <div class="row mb-3 align-items-center">
                            <div class="col-5">
                                <label class="form-label" for="answer">Answer</label>
                                <input type="text" class="form-control" placeholder="Type Answers Here"
                                    name="hra_answers[]">
                            </div>
                            <div class="col-3">
                                <label class="form-label" for="points">Points</label>
                                <input type="text" class="form-control" placeholder="Type Points Here"
                                    name="hra_points[]">
                            </div>
                            <div class="col-2">
                                <label class="form-label" for="compareValue">Compare Value</label>
                                <input type="text" class="form-control" placeholder="Type Compare Values Here"
                                    name="compare_values[]">
                            </div>
                            <div class="col-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-success w-100 custom-btn" id="add-row">
                                    <i class="fas fa-plus fa-md"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title">Upload Image</h5>
                </div>
                <div class="card-body">
                    <form action="/upload" class="dropzone needsclick p-0" id="dropzone-basic">
                        <div class="dz-message needsclick">
                            <p class="h4 needsclick pt-3 mb-2">Drag and drop
                                your image here</p>
                            <p class="h6 text-muted d-block fw-normal mb-2">or</p>
                            <span class="note needsclick btn btn-sm btn-label-primary" id="btnBrowse">Browse
                                image</span>
                        </div>
                        <div class="fallback">
                            <input name="hra_question_file" type="file" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card mb-6">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="master_tests" class="form-label">Select Tests</label>
                            <div class="select2-primary">
                                <select id="select2Success" name="tests" class="select2 form-select master_tests"
                                    multiple>
                                    <option selected id="test-header" disabled>Select 1 or more tests</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="additional_options" class="form-label">Option Type</label>
                            <div class="select2-primary">
                                <select id="select2Additional" class="form-select additional_options" id="optionType">
                                    <option value disabled selected>Select</option>
                                    <option value="Select Box">Select Box</option>
                                    <option value="Check Box">Check Box</option>
                                    <option value="Input Box">Input Box</option>
                                    <option value="Radio Button">Radio Button</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Gender Selection -->
                    <div class="col-md mb-4">
                        <label for="hra_gender" class="form-label master_gender mb-2">Select Gender</label>
                        <div class="d-flex flex-wrap flex-sm-nowrap align-items-center">
                            <div class="form-check form-check-primary me-4">
                                <input name="hra_gender" class="form-check-input" type="checkbox" value="male"
                                    id="customCheckboxMale" />
                                <label class="form-check-label" for="customCheckboxMale"> Male </label>
                            </div>
                            <div class="form-check form-check-primary me-4">
                                <input name="hra_gender" class="form-check-input" type="checkbox" value="female"
                                    id="customCheckboxFemale" />
                                <label class="form-check-label" for="customCheckboxFemale"> Female </label>
                            </div>
                            <div class="form-check form-check-primary">
                                <input name="hra_gender" class="form-check-input" type="checkbox" value="third_gender"
                                    id="customCheckboxThirdGender" />
                                <label class="form-check-label" for="customCheckboxThirdGender"> Third Gender </label>
                            </div>
                        </div>
                    </div>
                    <!-- Checkboxes -->
                    <div class="row align-items-center mb-4">
                        <!-- <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input hra_input_box" type="checkbox" id="hra_input_box">
                                <label class="form-check-label" for="hra_input_box">Need Input Box?</label>
                            </div>
                        </div> -->
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input hra_compare_value" type="checkbox"
                                    id="hra_compare_value">
                                <label class="form-check-label" for="hra_compare_value">Compare
                                    Values</label>
                            </div>
                        </div>
                    </div>
                    <!-- Comments -->
                    <div class="mb-4">
                        <label class="form-label" for="hra_comments">Comments</label>
                        <textarea class="form-control hra_comments" id="hra_comments" placeholder="Type Comments Here"
                            name="hra_comments" aria-label="hra_comments" rows="4"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/add-questions.js"></script>
@endsection
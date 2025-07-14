@extends('layouts/layoutMaster')

@section('title', 'HRA Questions')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/quill/typography.scss',
'resources/assets/vendor/libs/quill/katex.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/quill/editor.scss'])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/app-ecommerce.scss')
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/quill/katex.js',
'resources/assets/vendor/libs/quill/quill.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/tagify/tagify.js'])
@endsection

@section('page-script')
@vite([
'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/ui-toasts.js', 'resources/assets/js/add-question.js'])
@endsection

@section('content')
<style>
    .preloader-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        background: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 10px;
    }

    .spinner-border {
        margin-bottom: 10px;
    }

    #offcanvasEcommerceCategoryList {
        width: 600px;
        /* Adjust this value as per your requirement */
    }

    @media (min-width: 768px) {
        #offcanvasEcommerceCategoryList {
            width: 700px;
            /* Adjust for larger screens */
        }
    }
</style>
<div class="app-ecommerce-category">

    <!-- Category List Table -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatables-category-list table border-top">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Gender</th>
                        <th>Answer / Points / Compare Values</th>
                        <th class="text-lg-center">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Offcanvas to add new customer -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCategoryList"
        enctype="multipart/form-data">
        <!-- Offcanvas Header -->
        <div class="offcanvas-header py-6">
            <h5 id="offcanvasEcommerceCategoryListLabel" class="offcanvas-title">Edit Question</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <!-- Offcanvas Body -->
        <div class="offcanvas-body border-top">
            <form class="pt-0" id="eCommerceCategoryListForm">
                <!-- Title -->
                <div class="mb-6">
                    <label class="form-label" for="Question_text">Question</label>
                    <input type="text" class="form-control" id="Question_text" placeholder="Enter category title"
                        name="categoryTitle" aria-label="category title">
                </div>
                <div class="mb-6">
                    <label class="form-label" for="Question_text">Question</label>
                    <input type="hidden" class="form-control" id="Question_text_old" placeholder="Enter category title"
                        name="categoryTitle" aria-label="category title">
                </div>
                <!-- Slug -->
                <div class="mb-6">
                    <label class="form-label" for="Question_formula">Formula</label>
                    <input type="text" id="Question_formula" class="form-control">
                </div>
                <div class="mb-6">
                    <label class="form-label" for="Dashboard_title">Dashboard
                        Text</label>
                    <input type="text" id="Dashboard_title" class="form-control">
                </div>
                <div id="answer-points-container">
                    <div class="row mb-3 align-items-center">
                        <div class="col-5">
                            <label class="form-label" for="answer">Answer</label>
                            <input type="text" class="form-control" placeholder="Type Answers Here" name="hra_answers[]"
                                id="hra_answer">
                        </div>
                        <div class="col-3">
                            <label class="form-label" for="points">Points</label>
                            <input type="text" class="form-control" placeholder="Type Points Here" name="hra_points[]"
                                id="hra_points">
                        </div>
                        <div class="col-2">
                            <label class="form-label" for="compareValue">Compare</label>
                            <input type="text" class="form-control" placeholder="Type Compare Values Here"
                                name="compare_values[]" id="hra_compare_values">
                        </div>
                        <div class="col-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-success w-100 custom-btn" id="add-row">
                                <i class="fas fa-plus fa-md"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Image Upload and Preview Section -->
                <div class="mb-6">
                    <label class="form-label" for="hra_question_image">Attachment</label>
                    <input class="form-control" type="file" id="hra_question_image" accept="image/*">
                </div>
                <div class="image-container mb-4" id="existing-image">
                    <p id="image-text" style="display: none; color: blue;">Existing image</p>
                    <p id="no-image-text" style="display: none; color: red;">No Existing Image Found</p>
                    <img id="imagePreview" src="" alt="Image Preview"
                        style="display: none; width: 100%; max-width: 150px; border-radius: 8px; margin-top: 10px;">
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-md-7">
                        <label for="master_tests" class="form-label">Select
                            Tests</label>
                        <div class="select2-primary">
                            <select id="masterTests" name="tests" class="select2 form-select master_tests" multiple>
                                <option selected id="test-header" disabled selected>Select 1 or more tests</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label for="additional_options" class="form-label">Option Type</label>
                        <div class="select2-primary">
                            <select id="option_type" class="form-select" name="option_type">
                                <option value="Select Box">Select Box</option>
                                <option value="Check Box">Check Box</option>
                                <option value="Input Box">Input Box</option>
                                <option value="Radio Button">Radio
                                    Button</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Gender Selection -->
                <div class="col-md mb-4">
                    <label for="hra_gender" class="form-label master_gender mb-2">Select
                        Gender</label>
                    <div class="d-flex flex-wrap flex-sm-nowrap align-items-center">
                        <div class="form-check form-check-primary me-4">
                            <input name="hra_gender" class="form-check-input" type="checkbox" value="male"
                                id="customRadioMale" checked />
                            <label class="form-check-label" for="customRadioMale"> Male </label>
                        </div>
                        <div class="form-check form-check-primary me-4">
                            <input name="hra_gender" class="form-check-input" type="checkbox" value="female"
                                id="customRadioFemale" />
                            <label class="form-check-label" for="customRadioFemale"> Female </label>
                        </div>
                        <div class="form-check form-check-primary">
                            <input name="hra_gender" class="form-check-input" type="checkbox" value="third_gender"
                                id="customRadioThirdGender" />
                            <label class="form-check-label" for="customRadioThirdGender"> Third Gender
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Checkboxes -->
                <div class="row align-items-center mb-4">
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input hra_input_box" type="checkbox" id="hra_input_box">
                            <label class="form-check-label" for="hra_input_box">Need Input Box?</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input hra_compare_value" type="checkbox" id="hra_compare_value">
                            <label class="form-check-label" for="hra_compare_value">Compare Values</label>
                        </div>
                    </div>
                </div>

                <!-- Comments -->
                <div class="mb-4">
                    <label class="form-label" for="hra_comments">Comments</label>
                    <textarea class="form-control hra_comments" id="hra_comments" placeholder="Type Comments Here"
                        name="hra_comments" aria-label="hra_comments" rows="4"></textarea>
                </div>
                <!-- Submit and reset -->
                <div class="mb-6">
                    <button class="btn btn-primary me-sm-3 me-1 data-submit" id="save-modifications">Modify</button>
                    <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/questions.js"></script>
@endsection
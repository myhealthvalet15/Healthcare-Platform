@extends('layouts/layoutMaster')
@section('title', 'HRA Assessment')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss','resources/assets/vendor/libs/@form-validation/form-validation.scss','resources/assets/vendor/libs/select2/select2.scss','resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss','resources/assets/vendor/libs/spinkit/spinkit.scss','resources/assets/vendor/libs/animate-css/animate.scss','resources/assets/vendor/libs/sweetalert2/sweetalert2.scss','resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js','resources/assets/vendor/libs/flatpickr/flatpickr.js','resources/assets/vendor/libs/@form-validation/popular.js','resources/assets/vendor/libs/@form-validation/bootstrap5.js','resources/assets/vendor/libs/@form-validation/auto-focus.js','resources/assets/vendor/libs/select2/select2.js','resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js','resources/assets/vendor/libs/typeahead-js/typeahead.js','resources/assets/vendor/libs/bloodhound/bloodhound.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js','resources/assets/js/forms-selects.js','resources/assets/js/forms-typeahead.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/hra_questionaire_template.css">
<div class="card hra-main-card">
    <div class="hra-header">
        <div class="hra-header-left">
            <h4 class="hra-title">
                <i class="fas fa-heartbeat me-2"></i>
                <?php echo htmlspecialchars($templateDetails['template_name']);
                ?>
            </h4>
            <div class="page-indicator">Page <span id="currentPage">1</span> of
                <span id="totalPages">1</span>
            </div>
        </div>
        <div class="hra-header-right">
            <div>
                <div class="progress-info">Question <span id="progressQuestionCount">0</span> of <span
                        id="totalQuestions">0</span> completed</div>
                <div class="progress">
                    <div class="progress-bar" id="progressBar" role="progressbar" style="width:0%" aria-valuenow="0"
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="hra-content">
        <div class="start-hra-container" id="startContainer">
            <h2 class="mb-4">Health Risk Assessment for
                {{ htmlspecialchars($templateDetails['template_name']) }}
            </h2>
            @if (!$templateDetails['is_questions_available'])
            <div class="no-questions-card p-4 text-center" style="background-color: #f8f9fa; border-radius: 12px;">
                <i class="fas fa-info-circle text-warning mb-3" style="font-size: 2rem;"></i>
                <h4 class="text-dark">No Questions Assigned</h4>
                <p class="text-muted mb-0">
                    This assessment template currently has no questions
                    assigned. <br>
                    Please contact your administrator to assign questions before
                    proceeding.
                </p>
            </div>
            @elseif ($templateDetails['is_hra_overall_results_exists'])
            <div class="completed-card p-4 text-center" style="background-color: #e2f0e9; border-radius: 12px;">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 2rem;"></i>
                <h4 class="text-success">Assessment Already Completed</h4>
                <p class="text-muted mb-0">
                    You have already completed all the questions for this
                    template. <br>
                    Thank you for participating in your health assessment.
                </p>
            </div>
            @else
            <p class="lead mb-4">
                Complete your comprehensive health assessment to understand your
                health risks and get personalized
                recommendations.
            </p>
            <button type="button" class="btn start-hra-btn" id="startHRABtn">
                <i class="fas fa-heartbeat me-2"></i>Start Health Assessment
            </button>
            @endif
        </div>
        <div class="hra-container position-relative d-none" id="hraContainer">
            <div class="preloader d-none" id="preloader">
                <div class="preloader-content">
                    <div class="spinner"></div>
                    <h4 class="text-primary">Loading your HRA Questions...</h4>
                    <p class="text-muted">Please wait while we prepare your
                        assessment</p>
                </div>
            </div>
            <div class="hra-body">
                <div id="questionsContainer"></div>
                <div id="errorContainer" class="d-none">
                    <div class="error-card">
                        <i class="fas fa-exclamation-triangle error-icon"></i>
                        <h4>Unable to Load Questions</h4>
                        <p id="errorMessage"></p>
                        <button type="button" class="btn btn-primary mt-3" onclick="location.reload()">Try
                            Again</button>
                    </div>
                </div>
                <div id="completionScreen" class="d-none">
                    <div class="completion-card">
                        <i class="fas fa-check-circle completion-icon"></i>
                        <h3 style="color: white;">Assessment Complete!</h3>
                        <p class="lead">Thank you for completing your Health
                            Risk Assessment.</p>
                        <p>Your responses have been saved successfully.</p>
                    </div>
                </div>
            </div>
            <div class="nav-buttons" id="navigationButtons">
                <button type="button" class="btn btn-outline-secondary" id="prevBtn" disabled><i
                        class="fas fa-chevron-left me-2"></i>Previous</button>
                <div class="text-muted">Page <span id="navCurrentPage">1</span>
                    of <span id="navTotalPages">1</span>
                </div>
                <div class="nav-right">
                    <button type="button" class="btn btn-primary" id="savePartiallyBtn"><i
                            class="fas fa-save me-2"></i>Save Partially</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next<i
                            class="fas fa-chevron-right ms-2"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="saveToast" class="toast" role="alert">
        <div class="toast-header">
            <i class="fas fa-save text-success me-2"></i>
            <strong class="me-auto">Saved</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">Your progress has been saved successfully!</div>
    </div>
</div>
<script src="/lib/js/page-scripts/hra_questionaire_template.js"></script>
@endsection
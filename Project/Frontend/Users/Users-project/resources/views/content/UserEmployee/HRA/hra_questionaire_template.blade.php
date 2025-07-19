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
<style>
    .hra-main-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .1);
        min-height: 80vh;
        position: relative;
        margin: 0 auto;
        max-width: 100%
    }

    .hra-header {
        background: linear-gradient(135deg, #667eea 0, #764ba2 100%);
        color: #fff;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 60px;
        position: sticky;
        top: 0;
        z-index: 999;
        width: 100%;
        box-sizing: border-box;
        border-radius: 15px 15px 0 0
    }

    .hra-header-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        min-width: 0
    }

    .hra-title {
        color: #fff !important;
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap
    }

    .page-indicator {
        background: rgba(255, 255, 255, .15);
        border-radius: 6px;
        padding: 4px 8px;
        font-size: .8rem;
        font-weight: 500;
        color: #fff;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .1);
        white-space: nowrap
    }

    .hra-header-right {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-shrink: 0;
        max-width: 250px
    }

    .progress-info {
        color: #fff;
        font-size: .85rem;
        font-weight: 500;
        text-align: right;
        margin-bottom: 6px;
        white-space: nowrap
    }

    .progress {
        height: 6px !important;
        border-radius: 3px;
        background: rgba(255, 255, 255, .2) !important;
        width: 180px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
        min-width: 120px
    }

    .progress-bar {
        background: linear-gradient(90deg, #4facfe 0, #00f2fe 100%) !important;
        border-radius: 3px;
        transition: width .5s ease
    }

    .hra-content {
        padding: 0;
        border-radius: 0 0 15px 15px;
        box-shadow: none;
        margin: 0;
        overflow: hidden
    }

    .hra-body {
        padding: 25px;
        min-height: 500px;
        max-height: calc(80vh - 140px);
        overflow-y: auto
    }

    .question-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .1);
        border-left: 4px solid #667eea;
        transition: all .2s ease;
        position: relative
    }

    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, .15)
    }

    .question-card.skipped {
        opacity: .6;
        transform: scale(.98);
        border-left-color: #ffc107
    }

    .question-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        font-size: 1.1rem
    }

    .form-check {
        margin: 0 0 10px 0;
        padding: 8px 12px;
        border-radius: 8px;
        transition: background-color .2s ease;
        cursor: pointer;
        display: flex;
        align-items: center
    }

    .form-check:hover {
        background-color: #f8f9fa
    }

    .form-check-input {
        margin: 0 10px 0 0 !important;
        position: static;
        flex-shrink: 0
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea
    }

    .form-check-input:checked~.form-check-label {
        font-weight: 600;
        color: #667eea
    }

    .form-check-label {
        width: 100%;
        cursor: pointer;
        padding-left: 0
    }

    .inline-options {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center
    }

    .inline-options .form-check {
        margin: 0;
        padding: 8px 12px;
        background: rgba(102, 126, 234, .05);
        border: 1px solid rgba(102, 126, 234, .2);
        border-radius: 8px;
        transition: all .2s ease
    }

    .inline-options .form-check:hover {
        background: rgba(102, 126, 234, .1);
        border-color: rgba(102, 126, 234, .3)
    }

    .inline-options .form-check-input:checked~.form-check-label {
        color: #667eea
    }

    .form-select,
    .form-control {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: border-color .3s ease
    }

    .form-select:focus,
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 .2rem rgba(102, 126, 234, .25)
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0, #764ba2 100%);
        border: 0;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all .3s ease
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, .4)
    }

    .btn-outline-secondary {
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all .3s ease
    }

    .skip-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, .9);
        color: #666;
        border: 0;
        border-radius: 20px;
        padding: 5px 15px;
        font-size: .9rem;
        transition: all .3s ease
    }

    .skip-btn:hover {
        background: #fff;
        color: #333;
        transform: scale(1.05)
    }

    .skip-btn.skipped {
        background: #ffc107;
        color: #333
    }

    .question-number {
        display: inline-block;
        background: #667eea;
        color: #fff;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        font-weight: bold;
        margin-right: 10px;
        font-size: .9rem;
        transition: all .3s ease
    }

    .question-number.skipped {
        background: #ffc107;
        color: #333
    }

    .nav-buttons {
        padding: 20px 25px;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        position: sticky;
        bottom: 0;
        z-index: 998
    }

    .completion-card {
        text-align: center;
        padding: 50px 30px;
        background: linear-gradient(135deg, #667eea 0, #764ba2 100%);
        color: #fff;
        border-radius: 15px
    }

    .completion-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #4facfe
    }

    .preloader {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, .95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
        backdrop-filter: blur(10px);
        border-radius: 15px
    }

    .preloader-content {
        text-align: center;
        animation: fadeInUp .8s ease-out
    }

    .spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #e3f2fd;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px
    }

    @keyframes spin {
        0% {
            transform: rotate(0)
        }

        100% {
            transform: rotate(360deg)
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .start-hra-container {
        text-align: center;
        padding: 50px 20px
    }

    .start-hra-btn {
        background: linear-gradient(135deg, #667eea 0, #764ba2 100%);
        border: 0;
        border-radius: 12px;
        padding: 20px 40px;
        font-weight: 600;
        font-size: 1.2rem;
        color: #fff;
        transition: all .3s ease
    }

    .start-hra-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, .4);
        color: #fff
    }

    .error-card {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        color: #856404;
        text-align: center
    }

    .error-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #f39c12
    }

    @media(max-width:768px) {
        .hra-header {
            padding: 12px 15px;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            min-height: auto
        }

        .hra-header-left {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px
        }

        .hra-header-right {
            width: 100%;
            justify-content: flex-start;
            max-width: 100%
        }

        .progress {
            width: 100%;
            max-width: 200px
        }

        .hra-body {
            padding: 15px
        }

        .nav-buttons {
            padding: 15px;
            flex-direction: column;
            gap: 10px
        }

        .hra-title {
            font-size: 1.1rem
        }

        .progress-info {
            font-size: .8rem
        }

        .inline-options {
            flex-direction: column;
            gap: 10px;
            align-items: stretch
        }

        .inline-options .form-check {
            justify-content: flex-start
        }
    }

    @media(max-width:480px) {
        .hra-header {
            padding: 10px 12px
        }

        .progress {
            width: 100%;
            min-width: 100px
        }

        .progress-info {
            text-align: left
        }

        .question-card {
            padding: 15px
        }

        .hra-body {
            padding: 12px
        }
    }

    .question-card.skipped input[disabled],
    .question-card.skipped select[disabled] {
        background-color: #f5f5f5 !important;
        border-color: #d3d3d3 !important;
        color: #999 !important;
        cursor: not-allowed !important;
        opacity: .5 !important
    }

    .question-card.skipped .form-check-input[disabled] {
        background-color: #f5f5f5 !important;
        border-color: #d3d3d3 !important;
        cursor: not-allowed !important;
        opacity: .5 !important
    }

    .question-card.skipped .form-check-label {
        color: #999 !important;
        cursor: not-allowed !important;
        opacity: .5 !important
    }

    .question-card.skipped .form-check:hover {
        background-color: transparent !important;
        border-color: #d3d3d3 !important
    }

    .question-card.skipped .inline-options .form-check:hover {
        background: rgba(102, 126, 234, 0.05) !important;
        border-color: #d3d3d3 !important
    }

    .trigger-question {
        border-left: 4px solid #ffc107 !important;
        margin-left: 30px;
        position: relative;
    }

    .trigger-question::before {
        content: '';
        position: absolute;
        left: -34px;
        top: 20px;
        width: 30px;
        height: 2px;
        background: #ffc107;
    }

    .trigger-badge {
        background: #ffc107 !important;
        color: #333 !important;
        font-weight: bold;
    }

    .trigger-badge.skipped {
        background: #6c757d !important;
        color: #fff !important;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
            max-height: 0;
        }

        to {
            opacity: 1;
            transform: translateY(0);
            max-height: 1000px;
        }
    }

    .trigger-question {
        animation: slideDown 0.5s ease-out;
    }

    @media(max-width:768px) {
        .trigger-question {
            margin-left: 15px;
        }

        .trigger-question::before {
            left: -19px;
            width: 15px;
        }
    }

    @media(max-width:480px) {
        .trigger-question {
            margin-left: 10px;
        }

        .trigger-question::before {
            left: -14px;
            width: 10px;
        }
    }

    .half-width-container {
        display: flex;
        justify-content: flex-start;
        width: 100%;
    }

    .form-control.half-width,
    .form-select.half-width {
        width: 50%;
        max-width: 400px;
        min-width: 200px;
    }

    @media (max-width: 768px) {

        .form-control.half-width,
        .form-select.half-width {
            width: 100%;
            min-width: 100%;
        }
    }

    @media (max-width: 480px) {

        .form-control.half-width,
        .form-select.half-width {
            width: 100%;
            min-width: 100%;
        }
    }

    .nav-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .nav-right {
        display: flex;
        gap: 0.5rem;
    }
</style>
<style>
    /* Your existing styles remain the same */
    /* Add these new styles for factor grouping */
    .factor-group {
        margin-bottom: 30px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .factor-header {
        background: linear-gradient(135deg, #667eea 0, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .factor-icon {
        font-size: 1.2rem;
    }

    .factor-questions {
        background: white;
        padding: 15px;
    }

    .factor-priority {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
</style>
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
                <div class="progress-info">Question <span
                        id="progressQuestionCount">0</span> of <span
                        id="totalQuestions">0</span> completed</div>
                <div class="progress">
                    <div class="progress-bar" id="progressBar"
                        role="progressbar" style="width:0%" aria-valuenow="0"
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
            <div class="no-questions-card p-4 text-center"
                style="background-color: #f8f9fa; border-radius: 12px;">
                <i class="fas fa-info-circle text-warning mb-3"
                    style="font-size: 2rem;"></i>
                <h4 class="text-dark">No Questions Assigned</h4>
                <p class="text-muted mb-0">
                    This assessment template currently has no questions
                    assigned. <br>
                    Please contact your administrator to assign questions before
                    proceeding.
                </p>
            </div>
            @elseif ($templateDetails['is_hra_overall_results_exists'])
            <div class="completed-card p-4 text-center"
                style="background-color: #e2f0e9; border-radius: 12px;">
                <i class="fas fa-check-circle text-success mb-3"
                    style="font-size: 2rem;"></i>
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
                        <button type="button" class="btn btn-primary mt-3"
                            onclick="location.reload()">Try
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
                <button type="button" class="btn btn-outline-secondary"
                    id="prevBtn" disabled><i
                        class="fas fa-chevron-left me-2"></i>Previous</button>
                <div class="text-muted">Page <span id="navCurrentPage">1</span>
                    of <span id="navTotalPages">1</span>
                </div>
                <div class="nav-right">
                    <button type="button" class="btn btn-primary"
                        id="savePartiallyBtn"><i
                            class="fas fa-save me-2"></i>Save Partially</button>
                    <button type="button" class="btn btn-primary"
                        id="nextBtn">Next<i
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
            <button type="button" class="btn-close"
                data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">Your progress has been saved successfully!</div>
    </div>
</div>
<script>
    class HRA {
        constructor() {
            this.q = [];
            this.p = 1;
            this.qpp = 20;
            this.tp = 1;
            this.r = {};
            this.s = new Set();
            this.a = new Set();
            this.tId = this.getTId();
            this.triggerQuestions = new Map();
            this.activeTriggers = new Map();
            this.bind();
            this.rawQuestions = [];
        }
        getTId() {
            const u = window.location.pathname,
                m = u.match(/\/template\/(\d+)/);
            return m ? parseInt(m[1]) : 1
        }
        bind() {
            document.getElementById('startHRABtn').addEventListener('click', () => this.init())
        }
        async init() {
            document.getElementById('startContainer').style.display = 'none';
            document.getElementById('hraContainer').classList.remove('d-none');
            this.show();
            try {
                await this.load();
                this.render();
                this.up();
                this.events()
            } catch (e) {
                this.showError(e.message)
            } finally {
                this.hide()
            }
        }
        show() {
            document.getElementById('preloader').classList.remove('d-none')
        }
        hide() {
            document.getElementById('preloader').classList.add('d-none')
        }
        showError(msg) {
            const errorEl = document.getElementById('errorMessage');
            errorEl.textContent = this.escapeHtml(msg);
            document.getElementById('errorContainer').classList.remove('d-none');
            document.getElementById('navigationButtons').style.display = 'none'
        }
        escapeHtml(t) {
            const div = document.createElement('div');
            div.textContent = t;
            return div.innerHTML
        }
        async load() {
            try {
                await new Promise((resolve, reject) => {
                    apiRequest({
                        url: `https://login-users.hygeiaes.com/UserEmployee/dashboard/templates/getTemplateQuestions/${encodeURIComponent(this.tId)}`,
                        method: 'GET',
                        onSuccess: (d) => {
                            if (!d.result) {
                                reject(new Error(d.data || 'Failed to load questions'));
                                return;
                            }
                            if (!d.data || !Array.isArray(d.data) || d.data.length === 0) {
                                reject(new Error('No questions found for this template'));
                                return;
                            }
                            this.rawQuestions = d.data;
                            this.q = d.data.map((q, i) => ({
                                id: q.question_id,
                                question: q.question,
                                type: this.getType(q.types),
                                options: this.getOptions(q.answer),
                                priority: q.question_priority,
                                factor_priority: q.factor_priority,
                                sequentialNumber: i + 1,
                                triggers: this.processTriggers(q),
                                rawData: q,
                                answered: q.answered || null,
                                isTriggerQuestion: !!q.trigger_question_of
                            })).sort((a, b) => a.factor_priority - b.factor_priority || a.priority - b.priority);
                            this.q.forEach((q, i) => q.sequentialNumber = i + 1);
                            this.tp = Math.ceil(this.q.length / this.qpp);
                            this.processPreviousAnswers();
                            resolve();
                        },
                        onError: (e) => {
                            reject(new Error(e.message.includes('fetch') ? 'Network error. Please check your connection.' : e.message));
                        }
                    });
                });
            } catch (e) {
                throw new Error(e.message);
            }
        }
        findTriggerKey(question, selectedValue) {
            if (!question.options) return null;
            if (question.rawData.comp_value) {
                try {
                    const compValues = JSON.parse(question.rawData.comp_value);
                    for (let i = 1; i <= Object.keys(compValues).length; i++) {
                        if (compValues[`key${i}`] === selectedValue) {
                            return `key${i}`;
                        }
                    }
                } catch (e) {
                    console.warn('Error parsing comp_value:', e);
                }
            }
            const optionIndex = question.options.findIndex(option => option === selectedValue);
            return optionIndex !== -1 ? `key${optionIndex + 1}` : null;
        }
        processPreviousAnswers() {
            this.rawQuestions.forEach(rawQuestion => {
                if (rawQuestion.answered && !rawQuestion.trigger_question_of) {
                    const question = this.q.find(q => q.id === rawQuestion.question_id);
                    if (question) {
                        this.r[question.id] = rawQuestion.answered;
                        this.a.add(question.id);
                        if (question.triggers) {
                            const triggerKey = this.findTriggerKey(question, rawQuestion.answered);
                            if (triggerKey && question.triggers[triggerKey]) {
                                if (!this.activeTriggers.has(question.id)) {
                                    this.activeTriggers.set(question.id, new Set());
                                }
                                this.activeTriggers.get(question.id).add(triggerKey);
                            }
                        }
                    }
                }
            });
            this.rawQuestions.forEach(rawQuestion => {
                if (rawQuestion.trigger_question_of && rawQuestion.answered) {
                    const parentQuestion = this.q.find(q => q.id === rawQuestion.trigger_question_of);
                    if (parentQuestion && this.activeTriggers.has(parentQuestion.id)) {
                        const activeTriggerKeys = this.activeTriggers.get(parentQuestion.id);
                        activeTriggerKeys.forEach(triggerKey => {
                            if (parentQuestion.triggers[triggerKey]) {
                                const triggerQuestion = parentQuestion.triggers[triggerKey].find(tq =>
                                    tq.originalId === rawQuestion.question_id
                                );
                                if (triggerQuestion) {
                                    this.r[triggerQuestion.id] = rawQuestion.answered;
                                    this.a.add(triggerQuestion.id);
                                }
                            }
                        });
                    }
                }
            });
        }
        restoreAnswer(q, container) {
            const sa = this.r[q.id];
            if (sa) {
                setTimeout(() => {
                    const ins = container.querySelectorAll('input, select');
                    if (q.type === 'checkbox' && Array.isArray(sa)) {
                        ins.forEach(i => {
                            if (i.type === 'checkbox' && sa.includes(i.value)) i.checked = true;
                        });
                    } else {
                        ins.forEach(i => {
                            if (i.type === 'radio' && i.value === sa) i.checked = true;
                            else if (i.type !== 'radio' && i.type !== 'checkbox') i.value = sa;
                        });
                    }
                }, 100);
            }
            else if (q.answered && !this.s.has(q.id)) {
                this.r[q.id] = q.answered;
                this.a.add(q.id);
                setTimeout(() => {
                    const ins = container.querySelectorAll('input, select');
                    if (q.type === 'checkbox') {
                        try {
                            const answers = JSON.parse(q.answered);
                            if (Array.isArray(answers)) {
                                ins.forEach(i => {
                                    if (i.type === 'checkbox' && answers.includes(i.value)) i.checked = true;
                                });
                            }
                        } catch (e) {
                            ins.forEach(i => {
                                if (i.type === 'checkbox' && i.value === q.answered) i.checked = true;
                            });
                        }
                    } else if (q.type === 'radio') {
                        ins.forEach(i => {
                            if (i.type === 'radio' && i.value === q.answered) i.checked = true;
                        });
                    } else {
                        ins.forEach(i => {
                            if (i.type !== 'radio' && i.type !== 'checkbox') i.value = q.answered;
                        });
                    }
                }, 100);
            }
        }
        saveToServer() {
            const data = {
                template_id: this.tId,
                answers: []
            };
            this.q.forEach(q => {
                let answerValue;
                if (this.s.has(q.id)) {
                    answerValue = "SKIP"
                } else if (this.r[q.id] !== undefined) {
                    answerValue = this.r[q.id]
                } else {
                    return
                }
                const response = {
                    question_id: q.id,
                    answer: answerValue
                };
                if (!this.s.has(q.id) && this.activeTriggers.has(q.id)) {
                    response.triggers = [];
                    const activeTriggerKeys = this.activeTriggers.get(q.id);
                    activeTriggerKeys.forEach(triggerKey => {
                        if (q.triggers[triggerKey]) {
                            q.triggers[triggerKey].forEach(triggerQuestion => {
                                let triggerAnswer;
                                if (this.s.has(triggerQuestion.id)) {
                                    triggerAnswer = "SKIP"
                                } else if (this.r[triggerQuestion.id] !== undefined) {
                                    triggerAnswer = this.r[triggerQuestion.id]
                                } else {
                                    return
                                }
                                const triggerResponse = {
                                    question_id: triggerQuestion.originalId,
                                    answer: triggerAnswer
                                };
                                response.triggers.push(triggerResponse)
                            })
                        }
                    })
                }
                data.answers.push(response)
            });
            this.showSaveLoader();
            apiRequest({
                url: `https://login-users.hygeiaes.com/UserEmployee/dashboard/templates/saveHraTemplateQuestionnaireAnswers/${encodeURIComponent(this.tId)}`,
                method: 'POST',
                data: data,
                onSuccess: (result) => {
                    this.hideSaveLoader();
                    if (result.result) {
                        this.complete();
                        const toast = new bootstrap.Toast(document.getElementById('saveToast'));
                        toast.show();
                        showToast('success', 'Success', 'Assessment saved successfully!')
                    } else {
                        this.showSaveError(result.data || 'Failed to save responses')
                    }
                },
                onError: (error) => {
                    this.hideSaveLoader();
                    console.error('Error:', error);
                    this.showSaveError('Failed to save responses. Please try again.')
                }
            })
        }
        savePartially() {
            this.save();
            const data = {
                template_id: this.tId,
                answers: [],
                is_partial: true
            };
            this.q.forEach(q => {
                let answerValue;
                if (this.s.has(q.id)) {
                    answerValue = "SKIP";
                } else if (this.r[q.id] !== undefined) {
                    answerValue = this.r[q.id];
                } else {
                    return;
                }
                const response = {
                    question_id: q.id,
                    answer: answerValue
                };
                if (!this.s.has(q.id) && this.activeTriggers.has(q.id)) {
                    response.triggers = [];
                    const activeTriggerKeys = this.activeTriggers.get(q.id);
                    activeTriggerKeys.forEach(triggerKey => {
                        if (q.triggers[triggerKey]) {
                            q.triggers[triggerKey].forEach(triggerQuestion => {
                                let triggerAnswer;
                                if (this.s.has(triggerQuestion.id)) {
                                    triggerAnswer = "SKIP";
                                } else if (this.r[triggerQuestion.id] !== undefined) {
                                    triggerAnswer = this.r[triggerQuestion.id];
                                } else {
                                    return;
                                }
                                const triggerResponse = {
                                    question_id: triggerQuestion.originalId,
                                    answer: triggerAnswer
                                };
                                response.triggers.push(triggerResponse);
                            });
                        }
                    });
                }
                data.answers.push(response);
            });
            this.showPartialSaveLoader();
            apiRequest({
                url: `https://login-users.hygeiaes.com/UserEmployee/dashboard/templates/saveHraTemplateQuestionnaireAnswers/${encodeURIComponent(this.tId)}`,
                method: 'POST',
                data: data,
                onSuccess: (result) => {
                    this.hidePartialSaveLoader();
                    if (result.result) {
                        const toast = new bootstrap.Toast(document.getElementById('saveToast'));
                        toast.show();
                        showToast('success', 'Success', 'Partial assessment saved successfully! You can continue later.');
                    } else {
                        this.showPartialSaveError(result.data || 'Failed to save partial responses');
                    }
                },
                onError: (error) => {
                    this.hidePartialSaveLoader();
                    console.error('Partial Save Error:', error);
                    this.showPartialSaveError('Failed to save partial responses. Please try again.');
                }
            });
        }
        showPartialSaveLoader() {
            const saveBtn = document.getElementById('savePartiallyBtn');
            const originalContent = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            saveBtn.setAttribute('data-original-content', originalContent);
        }
        hidePartialSaveLoader() {
            const saveBtn = document.getElementById('savePartiallyBtn');
            const originalContent = saveBtn.getAttribute('data-original-content');
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalContent || '<i class="fas fa-save me-2"></i>Save Partially';
            saveBtn.removeAttribute('data-original-content');
        }
        showPartialSaveError(message) {
            this.hidePartialSaveLoader();
            showToast('error', 'Error', message);
            console.error('Partial Save Error:', message);
        }
        showSaveLoader() {
            const navButtons = document.getElementById('navigationButtons');
            navButtons.style.display = 'none';
            const questionsContainer = document.getElementById('questionsContainer');
            const saveLoader = document.createElement('div');
            saveLoader.id = 'saveLoader';
            saveLoader.className = 'preloader';
            const preloaderContent = document.createElement('div');
            preloaderContent.className = 'preloader-content';
            const spinner = document.createElement('div');
            spinner.className = 'spinner';
            const heading = document.createElement('h4');
            heading.className = 'text-primary';
            heading.textContent = 'Saving your assessment...';
            const paragraph = document.createElement('p');
            paragraph.className = 'text-muted';
            paragraph.textContent = 'Please wait while we save your responses';
            preloaderContent.appendChild(spinner);
            preloaderContent.appendChild(heading);
            preloaderContent.appendChild(paragraph);
            saveLoader.appendChild(preloaderContent);
            questionsContainer.appendChild(saveLoader);
        }
        hideSaveLoader() {
            const saveLoader = document.getElementById('saveLoader');
            if (saveLoader) {
                saveLoader.remove();
            }
        }
        showSaveError(message) {
            this.hideSaveLoader();
            const navButtons = document.getElementById('navigationButtons');
            navButtons.style.display = 'flex';
            const questionsContainer = document.getElementById('questionsContainer');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-card';
            const errorIcon = document.createElement('i');
            errorIcon.className = 'fas fa-exclamation-triangle error-icon';
            const errorHeading = document.createElement('h4');
            errorHeading.textContent = 'Failed to Save Assessment';
            const errorMessage = document.createElement('p');
            errorMessage.textContent = this.escapeHtml(message);
            const retryButton = document.createElement('button');
            retryButton.type = 'button';
            retryButton.className = 'btn btn-primary mt-3';
            retryButton.textContent = 'Try Again';
            retryButton.onclick = () => hra.retrySave();
            errorDiv.appendChild(errorIcon);
            errorDiv.appendChild(errorHeading);
            errorDiv.appendChild(errorMessage);
            errorDiv.appendChild(retryButton);
            questionsContainer.appendChild(errorDiv);
            showToast('error', 'Error', message);
        }
        retrySave() {
            const errorCard = document.querySelector('.error-card');
            if (errorCard) {
                errorCard.remove()
            }
            this.saveToServer()
        }
        processTriggers(rawQuestion) {
            const triggers = {};
            for (let i = 1; i <= 8; i++) {
                const triggerKey = `trigger_${i}`;
                if (rawQuestion[triggerKey] && rawQuestion[triggerKey] !== null) {
                    const triggerData = rawQuestion[triggerKey];
                    const triggerQuestions = [];
                    Object.keys(triggerData).forEach(questionKey => {
                        const triggerQuestion = triggerData[questionKey];
                        const uniqueTriggerId = `trigger_${rawQuestion.question_id}_${i}_${triggerQuestion.question_id}`;
                        triggerQuestions.push({
                            id: uniqueTriggerId,
                            originalId: triggerQuestion.question_id,
                            question: triggerQuestion.question,
                            type: this.getType(triggerQuestion.types),
                            options: this.getOptions(JSON.stringify(triggerQuestion.answer)),
                            triggerNumber: i,
                            parentId: rawQuestion.question_id,
                            isTrigger: true,
                            triggerKey: triggerKey,
                            questionKey: questionKey,
                            sequenceInTrigger: triggerQuestions.length + 1,
                            answered: triggerQuestion.answered || null
                        })
                    });
                    triggers[`key${i}`] = triggerQuestions
                }
            }
            return triggers
        }
        getType(t) {
            switch (t.toLowerCase()) {
                case 'input box':
                    return 'text';
                case 'select box':
                    return 'select';
                case 'radio button':
                    return 'radio';
                case 'checkbox':
                case 'check box':
                    return 'checkbox';
                default:
                    return 'text'
            }
        }
        getOptions(a) {
            try {
                const p = JSON.parse(a);
                return Object.values(p).filter(v => v !== 'null' && v !== null)
            } catch {
                return []
            }
        }
        render() {
            const s = (this.p - 1) * this.qpp,
                e = Math.min(s + this.qpp, this.q.length),
                cq = this.q.slice(s, e),
                c = document.getElementById('questionsContainer');
            this.clearElement(c);
            const groupedQuestions = this.groupQuestionsByFactor(cq);
            groupedQuestions.forEach(group => {
                const factorGroup = document.createElement('div');
                factorGroup.className = 'factor-group';
                const factorHeader = document.createElement('div');
                factorHeader.className = 'factor-header';
                const priorityBadge = document.createElement('div');
                priorityBadge.className = 'factor-priority';
                priorityBadge.textContent = group.factor_priority;
                const factorIcon = document.createElement('i');
                factorIcon.className = 'fas fa-layer-group factor-icon';
                const factorTitle = document.createElement('span');
                factorTitle.textContent = group.factor_name;
                factorHeader.appendChild(priorityBadge);
                factorHeader.appendChild(factorIcon);
                factorHeader.appendChild(factorTitle);
                const questionsContainer = document.createElement('div');
                questionsContainer.className = 'factor-questions';
                group.questions.forEach(q => {
                    const mainCard = this.create(q);
                    questionsContainer.appendChild(mainCard);
                    if (this.activeTriggers.has(q.id)) {
                        const activeTriggerKeys = this.activeTriggers.get(q.id);
                        activeTriggerKeys.forEach(triggerKey => {
                            if (q.triggers[triggerKey]) {
                                const triggerQuestions = q.triggers[triggerKey];
                                triggerQuestions.forEach(triggerQuestion => {
                                    const triggerCard = this.createTriggerQuestion(triggerQuestion);
                                    questionsContainer.appendChild(triggerCard);
                                });
                            }
                        });
                    }
                });
                factorGroup.appendChild(factorHeader);
                factorGroup.appendChild(questionsContainer);
                c.appendChild(factorGroup);
            });
            this.upInfo();
            this.upNav();
        }
        groupQuestionsByFactor(questions) {
            const factorMap = new Map();
            questions.forEach(q => {
                if (!factorMap.has(q.rawData.factor_name)) {
                    factorMap.set(q.rawData.factor_name, {
                        factor_name: q.rawData.factor_name,
                        factor_priority: q.rawData.factor_priority,
                        questions: []
                    });
                }
                factorMap.get(q.rawData.factor_name).questions.push(q);
            });
            return Array.from(factorMap.values()).sort((a, b) => a.factor_priority - b.factor_priority);
        }
        clearElement(el) {
            while (el.firstChild) el.removeChild(el.firstChild)
        }
        create(q) {
            const d = document.createElement('div');
            d.className = 'question-card position-relative';
            d.id = `question-card-${q.id}`;
            const skipBtn = document.createElement('button');
            skipBtn.className = `skip-btn ${this.s.has(q.id) ? 'skipped' : ''}`;
            skipBtn.onclick = () => this.skip(q.id);
            const skipIcon = document.createElement('i');
            skipIcon.className = `fas fa-${this.s.has(q.id) ? 'undo' : 'forward'} me-1`;
            skipBtn.appendChild(skipIcon);
            skipBtn.appendChild(document.createTextNode(this.s.has(q.id) ? 'Unskip' : 'Skip'));
            const titleDiv = document.createElement('div');
            titleDiv.className = 'question-title';
            const qNum = document.createElement('span');
            qNum.className = `question-number ${this.s.has(q.id) ? 'skipped' : ''}`;
            qNum.id = `question-number-${q.id}`;
            qNum.textContent = q.sequentialNumber;
            titleDiv.appendChild(qNum);
            titleDiv.appendChild(document.createTextNode(q.question));
            const inputDiv = document.createElement('div');
            inputDiv.className = 'question-input';
            inputDiv.appendChild(this.createInput(q));
            d.appendChild(skipBtn);
            d.appendChild(titleDiv);
            d.appendChild(inputDiv);
            if (this.s.has(q.id)) d.classList.add('skipped');
            this.restoreAnswer(q, inputDiv);
            return d
        }
        createTriggerQuestion(triggerQ) {
            const d = document.createElement('div');
            d.className = 'question-card position-relative trigger-question';
            d.id = `trigger-question-card-${triggerQ.id}`;
            const skipBtn = document.createElement('button');
            skipBtn.className = `skip-btn ${this.s.has(triggerQ.id) ? 'skipped' : ''}`;
            skipBtn.onclick = () => this.skip(triggerQ.id);
            const skipIcon = document.createElement('i');
            skipIcon.className = `fas fa-${this.s.has(triggerQ.id) ? 'undo' : 'forward'} me-1`;
            skipBtn.appendChild(skipIcon);
            skipBtn.appendChild(document.createTextNode(this.s.has(triggerQ.id) ? 'Unskip' : 'Skip'));
            const titleDiv = document.createElement('div');
            titleDiv.className = 'question-title';
            const qNum = document.createElement('span');
            qNum.className = `question-number trigger-badge ${this.s.has(triggerQ.id) ? 'skipped' : ''}`;
            qNum.id = `question-number-${triggerQ.id}`;
            qNum.textContent = `T${triggerQ.sequenceInTrigger}`;
            titleDiv.appendChild(qNum);
            titleDiv.appendChild(document.createTextNode(triggerQ.question));
            const inputDiv = document.createElement('div');
            inputDiv.className = 'question-input';
            inputDiv.appendChild(this.createInput(triggerQ));
            d.appendChild(skipBtn);
            d.appendChild(titleDiv);
            d.appendChild(inputDiv);
            if (this.s.has(triggerQ.id)) d.classList.add('skipped');
            this.restoreAnswer(triggerQ, inputDiv);
            return d
        }
        createInput(q) {
            const qId = q.isTrigger ? `trigger_question_${q.id}` : `question_${q.id}`;
            switch (q.type) {
                case 'radio':
                    return this.createRadioOptions(q, qId);
                case 'checkbox':
                    return this.createCheckboxOptions(q, qId);
                case 'select':
                    return this.createSelectOption(q, qId);
                case 'text':
                case 'number':
                    return this.createTextInput(q, qId);
                default:
                    return this.createTextInput(q, qId)
            }
        }
        createRadioOptions(q, qId) {
            const container = document.createElement('div');
            container.className = 'inline-options';
            q.options.forEach(o => {
                const checkDiv = document.createElement('div');
                checkDiv.className = 'form-check';
                const input = document.createElement('input');
                input.className = 'form-check-input';
                input.type = 'radio';
                input.name = qId;
                input.id = `${qId}_${o.replace(/\s+/g, '_')}`;
                input.value = o;
                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.setAttribute('for', input.id);
                label.textContent = o;
                checkDiv.appendChild(input);
                checkDiv.appendChild(label);
                container.appendChild(checkDiv)
            });
            return container
        }
        createCheckboxOptions(q, qId) {
            const container = document.createElement('div');
            container.className = 'inline-options';
            q.options.forEach(o => {
                const checkDiv = document.createElement('div');
                checkDiv.className = 'form-check';
                const input = document.createElement('input');
                input.className = 'form-check-input';
                input.type = 'checkbox';
                input.name = qId;
                input.id = `${qId}_${o.replace(/\s+/g, '_')}`;
                input.value = o;
                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.setAttribute('for', input.id);
                label.textContent = o;
                checkDiv.appendChild(input);
                checkDiv.appendChild(label);
                container.appendChild(checkDiv)
            });
            return container
        }
        createSelectOption(q, qId) {
            const container = document.createElement('div');
            container.className = 'half-width-container';
            const select = document.createElement('select');
            select.className = 'form-select half-width';
            select.name = qId;
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Select an option';
            select.appendChild(defaultOption);
            q.options.forEach(o => {
                const option = document.createElement('option');
                option.value = o;
                option.textContent = o;
                select.appendChild(option)
            });
            container.appendChild(select);
            return container
        }
        createTextInput(q, qId) {
            const container = document.createElement('div');
            container.className = 'half-width-container';
            const input = document.createElement('input');
            input.type = q.type === 'number' ? 'number' : 'text';
            input.className = 'form-control half-width';
            input.name = qId;
            input.placeholder = q.type === 'number' ? 'Enter number' : 'Enter your answer';
            container.appendChild(input);
            return container
        }
        handleTriggerLogic(questionId, selectedValue) {
            const question = this.q.find(q => q.id === questionId);
            if (!question || !question.triggers) return;
            this.removeTriggers(questionId);
            const optionIndex = question.options.findIndex(option => option === selectedValue);
            if (optionIndex === -1) return;
            const triggerNumber = optionIndex + 1;
            const triggerKey = `key${triggerNumber}`;
            if (question.triggers[triggerKey]) {
                if (!this.activeTriggers.has(questionId)) {
                    this.activeTriggers.set(questionId, new Set())
                }
                this.activeTriggers.get(questionId).add(triggerKey);
                this.render();
                this.up()
            }
        }
        findTriggerKey(question, selectedValue) {
            const optionIndex = question.options.findIndex(option => option === selectedValue);
            if (optionIndex !== -1) {
                return `key${optionIndex + 1}`
            }
            return null
        }
        removeTriggers(questionId) {
            const question = this.q.find(q => q.id === questionId);
            if (!question || !question.triggers) return;
            if (this.activeTriggers.has(questionId)) {
                const activeTriggerKeys = this.activeTriggers.get(questionId);
                activeTriggerKeys.forEach(triggerKey => {
                    const triggerQuestions = question.triggers[triggerKey];
                    if (triggerQuestions && Array.isArray(triggerQuestions)) {
                        triggerQuestions.forEach(triggerQuestion => {
                            delete this.r[triggerQuestion.id];
                            this.a.delete(triggerQuestion.id);
                            this.s.delete(triggerQuestion.id);
                            const triggerCard = document.getElementById(`trigger-question-card-${triggerQuestion.id}`);
                            if (triggerCard) {
                                triggerCard.remove()
                            }
                        })
                    }
                });
                this.activeTriggers.delete(questionId);
                this.up()
            }
        }
        restoreAnswer(q, container) {
            const sa = this.r[q.id] || q.answered;
            if (sa) {
                setTimeout(() => {
                    const ins = container.querySelectorAll('input, select');
                    if (q.type === 'checkbox') {
                        let answers = sa;
                        if (typeof sa === 'string') {
                            try {
                                answers = JSON.parse(sa);
                            } catch (e) {
                                answers = [sa];
                            }
                        }
                        if (Array.isArray(answers)) {
                            ins.forEach(i => {
                                if (i.type === 'checkbox' && answers.includes(i.value)) {
                                    i.checked = true;
                                }
                            });
                        }
                    } else if (q.type === 'radio') {
                        ins.forEach(i => {
                            if (i.type === 'radio' && i.value === sa) {
                                i.checked = true;
                            }
                        });
                    } else {
                        ins.forEach(i => {
                            if (i.type !== 'radio' && i.type !== 'checkbox') {
                                i.value = sa;
                            }
                        });
                    }
                }, 100);
            }
        }
        upInfo() {
            ['currentPage', 'navCurrentPage'].forEach(id => document.getElementById(id).textContent = this.p);
            ['totalPages', 'navTotalPages'].forEach(id => document.getElementById(id).textContent = this.tp)
        }
        getTotalQuestions() {
            let total = this.q.length;
            this.activeTriggers.forEach((triggerKeys, questionId) => {
                const question = this.q.find(q => q.id === questionId);
                if (question && question.triggers) {
                    triggerKeys.forEach(triggerKey => {
                        if (question.triggers[triggerKey]) {
                            total += question.triggers[triggerKey].length
                        }
                    })
                }
            });
            return total
        }
        getCompletedQuestions() {
            let completed = 0;
            this.q.forEach(q => {
                if (this.a.has(q.id) || this.s.has(q.id)) {
                    completed++
                }
            });
            this.activeTriggers.forEach((triggerKeys, questionId) => {
                const question = this.q.find(q => q.id === questionId);
                if (question && question.triggers) {
                    triggerKeys.forEach(triggerKey => {
                        if (question.triggers[triggerKey]) {
                            question.triggers[triggerKey].forEach(triggerQuestion => {
                                if (this.a.has(triggerQuestion.id) || this.s.has(triggerQuestion.id)) {
                                    completed++;
                                }
                            })
                        }
                    })
                }
            });
            return completed
        }
        up() {
            const totalQuestions = this.getTotalQuestions();
            const completedQuestions = this.getCompletedQuestions();
            const pc = totalQuestions > 0 ? Math.round((completedQuestions / totalQuestions) * 100) : 0;
            const b = document.getElementById('progressBar');
            b.style.width = `${pc}%`;
            b.setAttribute('aria-valuenow', pc);
            document.getElementById('progressQuestionCount').textContent = completedQuestions;
            document.getElementById('totalQuestions').textContent = totalQuestions
        }
        upNav() {
            document.getElementById('prevBtn').disabled = this.p === 1;
            const nb = document.getElementById('nextBtn');
            this.clearElement(nb);
            if (this.p === this.tp) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-save me-2';
                nb.appendChild(icon);
                nb.appendChild(document.createTextNode('Save Assessment'))
            } else {
                nb.appendChild(document.createTextNode('Next'));
                const icon = document.createElement('i');
                icon.className = 'fas fa-chevron-right ms-2';
                nb.appendChild(icon)
            }
        }
        canNext() {
            const s = (this.p - 1) * this.qpp,
                e = Math.min(s + this.qpp, this.q.length);
            return this.q.slice(s, e).every(q => this.a.has(q.id) || this.s.has(q.id))
        }
        getNext() {
            const s = (this.p - 1) * this.qpp,
                e = Math.min(s + this.qpp, this.q.length),
                cq = this.q.slice(s, e);
            for (let q of cq)
                if (!this.a.has(q.id) && !this.s.has(q.id)) return q.id;
            return null
        }
        isSeq(qId) {
            const s = (this.p - 1) * this.qpp,
                e = Math.min(s + this.qpp, this.q.length),
                cq = this.q.slice(s, e);
            for (let i = 0; i < cq.length; i++) {
                const q = cq[i];
                if (q.id === qId) {
                    for (let j = 0; j < i; j++) {
                        const pq = cq[j];
                        if (!this.a.has(pq.id) && !this.s.has(pq.id)) return false
                    }
                    return true
                }
            }
            return false
        }
        scroll(cqId) {
            if (this.isSeq(cqId)) {
                const nqId = this.getNext();
                if (nqId) {
                    const nc = document.getElementById(`question-card-${nqId}`);
                    if (nc) setTimeout(() => nc.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    }), 300)
                }
            }
        }
        findTriggerQuestion(qId) {
            for (let question of this.q) {
                if (question.triggers) {
                    for (let triggerKey in question.triggers) {
                        const triggerQuestions = question.triggers[triggerKey];
                        if (Array.isArray(triggerQuestions)) {
                            for (let triggerQuestion of triggerQuestions) {
                                if (triggerQuestion.id === qId) {
                                    return {
                                        ...triggerQuestion,
                                        isTrigger: true
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return null
        }
        events() {
            document.addEventListener('change', (e) => {
                if (e.target.matches('input, select')) {
                    if (e.target.disabled) {
                        e.preventDefault();
                        return
                    }
                    let qId, isTriggerQuestion = false;
                    if (e.target.name.startsWith('trigger_question_')) {
                        qId = e.target.name.replace('trigger_question_', '');
                        isTriggerQuestion = true
                    } else {
                        qId = parseInt(e.target.name.replace('question_', ''))
                    }
                    let q = isTriggerQuestion ? this.findTriggerQuestion(qId) : this.q.find(q => q.id === qId);
                    if (q && q.type === 'checkbox') {
                        const nameAttr = e.target.name;
                        const cb = document.querySelectorAll(`input[name="${nameAttr}"]:checked:not([disabled])`);
                        const vs = Array.from(cb).map(c => c.value);
                        if (vs.length > 0) {
                            this.r[qId] = vs;
                            this.a.add(qId);
                            this.s.delete(qId)
                        } else {
                            delete this.r[qId];
                            this.a.delete(qId)
                        }
                    } else if (e.target.value.trim()) {
                        this.r[qId] = e.target.value;
                        this.a.add(qId);
                        this.s.delete(qId);
                        if (!isTriggerQuestion && q && q.triggers && Object.keys(q.triggers).length > 0) {
                            this.handleTriggerLogic(qId, e.target.value)
                        }
                    } else {
                        delete this.r[qId];
                        this.a.delete(qId);
                        if (!isTriggerQuestion && q && q.triggers && Object.keys(q.triggers).length > 0) {
                            this.removeTriggers(qId)
                        }
                    }
                    const qn = document.getElementById(`question-number-${qId}`);
                    if (qn && this.a.has(qId)) qn.classList.remove('skipped');
                    if (!isTriggerQuestion) {
                        this.scroll(qId)
                    }
                    this.up()
                }
            });
            document.addEventListener('click', (e) => {
                if (e.target.closest('.form-check')) {
                    const formCheck = e.target.closest('.form-check');
                    const input = formCheck.querySelector('input');
                    if (input && input.disabled) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false
                    }
                }
            });
            document.getElementById('prevBtn').addEventListener('click', () => {
                if (this.p > 1) {
                    this.save();
                    this.p--;
                    this.render();
                    this.up()
                }
            });
            document.getElementById('nextBtn').addEventListener('click', () => {
                if (!this.canNext()) {
                    showToast('error', 'Please answer or skip all questions on this page before proceeding.');
                    return
                }
                this.save();
                if (this.p < this.tp) {
                    this.p++;
                    this.render();
                    this.up()
                } else {
                    this.saveToServer()
                }
            });
            document.getElementById('savePartiallyBtn').addEventListener('click', () => {
                this.savePartially();
            });
        }
        save() {
            const processedQuestions = new Set();
            document.querySelectorAll('#questionsContainer input, #questionsContainer select').forEach(i => {
                let qId;
                if (i.name.startsWith('trigger_question_')) {
                    qId = i.name.replace('trigger_question_', '')
                } else {
                    qId = parseInt(i.name.replace('question_', ''))
                }
                if (processedQuestions.has(qId) || this.s.has(qId)) {
                    return
                }
                const q = this.q.find(q => q.id === qId) || this.findTriggerQuestion(qId);
                if (q && q.type === 'checkbox') {
                    const nameAttr = i.name;
                    const cb = document.querySelectorAll(`input[name="${nameAttr}"]:checked`);
                    const vs = Array.from(cb).map(c => c.value);
                    if (vs.length > 0) {
                        this.r[qId] = vs;
                        this.a.add(qId)
                    }
                    processedQuestions.add(qId);
                } else if (q && q.type === 'radio') {
                    const nameAttr = i.name;
                    const checkedRadio = document.querySelector(`input[name="${nameAttr}"]:checked`);
                    if (checkedRadio && checkedRadio.value && checkedRadio.value.trim()) {
                        this.r[qId] = checkedRadio.value;
                        this.a.add(qId)
                    }
                    processedQuestions.add(qId);
                } else if (q && (q.type === 'select' || q.type === 'text' || q.type === 'number')) {
                    if (i.value && i.value.trim()) {
                        this.r[qId] = i.value;
                        this.a.add(qId)
                    }
                    processedQuestions.add(qId);
                }
            })
        }
        skip(qId) {
            const c = document.getElementById(`question-card-${qId}`) || document.getElementById(`trigger-question-card-${qId}`);
            const b = c.querySelector('.skip-btn');
            const ins = c.querySelectorAll('input, select');
            const qn = document.getElementById(`question-number-${qId}`);
            this.clearElement(b);
            if (this.s.has(qId)) {
                this.s.delete(qId);
                this.a.delete(qId);
                delete this.r[qId];
                c.classList.remove('skipped');
                b.classList.remove('skipped');
                if (qn) qn.classList.remove('skipped');
                ins.forEach(i => {
                    i.disabled = false;
                    i.style.opacity = '1';
                    i.style.cursor = 'pointer';
                    const label = c.querySelector(`label[for="${i.id}"]`);
                    if (label) {
                        label.style.opacity = '1';
                        label.style.cursor = 'pointer'
                    }
                });
                const formChecks = c.querySelectorAll('.form-check');
                formChecks.forEach(fc => {
                    fc.style.opacity = '1';
                    fc.style.cursor = 'pointer'
                });
                const icon = document.createElement('i');
                icon.className = 'fas fa-forward me-1';
                b.appendChild(icon);
                b.appendChild(document.createTextNode('Skip'))
            } else {
                this.s.add(qId);
                this.a.delete(qId);
                delete this.r[qId];
                c.classList.add('skipped');
                b.classList.add('skipped');
                if (qn) qn.classList.add('skipped');
                ins.forEach(i => {
                    if (i.type === 'radio' || i.type === 'checkbox') {
                        i.checked = false
                    } else if (i.type === 'select-one') {
                        i.selectedIndex = 0
                    } else {
                        i.value = ''
                    }
                });
                ins.forEach(i => {
                    i.disabled = true;
                    i.style.opacity = '0.5';
                    i.style.cursor = 'not-allowed';
                    const label = c.querySelector(`label[for="${i.id}"]`);
                    if (label) {
                        label.style.opacity = '0.5';
                        label.style.cursor = 'not-allowed'
                    }
                });
                const formChecks = c.querySelectorAll('.form-check');
                formChecks.forEach(fc => {
                    fc.style.opacity = '0.5';
                    fc.style.cursor = 'not-allowed'
                });
                const icon = document.createElement('i');
                icon.className = 'fas fa-undo me-1';
                b.appendChild(icon);
                b.appendChild(document.createTextNode('Unskip'));
                const mainQuestion = this.q.find(q => q.id === qId);
                if (mainQuestion) {
                    this.removeTriggers(qId)
                }
                this.scroll(qId)
            }
            this.up()
        }
        complete() {
            ['questionsContainer', 'navigationButtons'].forEach(id => document.getElementById(id).classList.add('d-none'));
            document.getElementById('completionScreen').classList.remove('d-none')
        }
    }
    let hra;
    document.addEventListener('DOMContentLoaded', () => hra = new HRA());
</script>
@endsection
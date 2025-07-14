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

    /* Reduced size for select dropdown */
    #factors {
        height: calc(2.5rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }
</style>
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

    /* Reduced size for select dropdown */
    #factors {
        height: calc(2.5rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    /* Loading button styles */
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"
    integrity="sha512-csIng5zcB+XpulRUa+ev1zKo7zRNGpEaVfNB9On1no9KYTEY/rLGAEEpvgdw6nim1WdTuihZY1eqZ31K7/fZjw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    const templateId = "{{ $template_id }}";
    const additionalCSS = `
            <style>
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
            `;
    document.addEventListener('DOMContentLoaded', function () {
        const factorDropdown = document.querySelector('#factors');
        const existingFactorsContainer = document.querySelector('#existing-questions-container');
        const questionsDropdown = document.querySelector('#questionsDropdown');
        const noQuestions = document.getElementById('no-questions-container');
        const saveChangesButton = document.getElementById('save-changes');
        function showButtonLoading() {
            saveChangesButton.classList.add('btn-loading');
            saveChangesButton.disabled = true;
            const originalContent = saveChangesButton.innerHTML;
            saveChangesButton.setAttribute('data-original-content', originalContent);
            saveChangesButton.innerHTML = `
            <span class="btn-text">${originalContent}</span>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        `;
        }
        function hideButtonLoading() {
            saveChangesButton.classList.remove('btn-loading');
            saveChangesButton.disabled = false;
            const originalContent = saveChangesButton.getAttribute('data-original-content');
            if (originalContent) {
                saveChangesButton.innerHTML = originalContent;
            }
        }
        saveChangesButton.addEventListener('click', async function () {
            showButtonLoading();
            try {
                const factorId = factorDropdown.value;
                const priority = getQuestionPriority();
                const data = {
                    template_id: templateId,
                    factor_id: factorId,
                    priority: priority
                };
                await apiRequest({
                    url: '/hra/templates/set-question-factor-priority/',
                    method: 'PUT',
                    data: data,
                    onSuccess: responseData => {
                        if (responseData.result === 'success') {
                            showToast('success', 'Changes saved successfully!');
                        } else {
                            showToast('error', 'Failed to save changes.');
                        }
                    },
                    onError: error => {
                        showToast('error', `Error: ${error}`);
                    }
                });
            } catch (error) {
                showToast('error', `Unexpected error: ${error}`);
            } finally {
                hideButtonLoading();
            }
        });
        function getQuestionPriority() {
            const listItems = document.querySelectorAll('#existing-questions-container .list-group-item');
            return Array.from(listItems).map(item => item.getAttribute('data-id'));
        }
        async function fetchQuestions() {
            await apiRequest({
                url: 'https://mhv-admin.hygeiaes.com/hra/get-all-questions',
                method: 'GET',
                onSuccess: data => {
                    document.getElementById('preloader').style.display = 'none';
                    document.getElementById('contents-container').style.display = 'block';
                    if (data.data && Array.isArray(data.data)) {
                        showToast('success', 'Questions fetched successfully');
                        while (questionsDropdown.firstChild) {
                            questionsDropdown.removeChild(questionsDropdown.firstChild);
                        }
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.disabled = true;
                        defaultOption.selected = true;
                        defaultOption.textContent = 'Select a question';
                        questionsDropdown.appendChild(defaultOption);
                        const filteredQuestions = data.data.filter(question => {
                            const currentTemplateId = parseInt(templateId);
                            const questionTemplateId = question.template_id_linked_with;
                            return questionTemplateId !== currentTemplateId;
                        });
                        filteredQuestions.forEach(question => {
                            const option = document.createElement('option');
                            option.value = question.question;
                            let genderText = '';
                            try {
                                const genderArray = JSON.parse(question.gender);
                                genderText = genderArray.join(', ');
                            } catch (e) {
                                genderText = question.gender;
                            }
                            option.textContent = `${question.question} (${genderText})`;
                            option.setAttribute('data-id', question.question_id);
                            questionsDropdown.appendChild(option);
                        });
                        hideExistingQuestionsInDropdown();
                    } else {
                        showToast('error', 'Unexpected response format: ' + data);
                    }
                },
                onError: error => {
                    showToast('error', 'error: ' + error);
                    document.getElementById('preloader').style.display = 'none';
                    document.getElementById('contents-container').style.display = 'block';
                }
            });
        }
        let existingQuestionsSortable;
        function initializeSortable() {
            const existingQuestionsContainer = document.getElementById('existing-questions-container');
            if (existingQuestionsSortable) {
                existingQuestionsSortable.destroy();
            }
            existingQuestionsSortable = new Sortable(existingQuestionsContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    updatePriorities();
                }
            });
        }
        function createQuestionListItem(question, priority) {
            const listItem = document.createElement('li');
            listItem.classList.add(
                'list-group-item',
                'drag-item',
                'cursor-move',
                'd-flex',
                'justify-content-between',
                'align-items-center'
            );
            listItem.setAttribute('data-id', question.question_id);
            const priorityBadge = document.createElement('span');
            priorityBadge.textContent = priority;
            priorityBadge.classList.add('badge', 'bg-primary', 'priority-badge');
            const questionName = document.createElement('span');
            questionName.textContent = question.question_name;
            questionName.classList.add('flex-grow-1');
            const removeButton = document.createElement('button');
            removeButton.classList.add('btn', 'btn-sm', 'btn-danger', 'ms-3');
            removeButton.textContent = 'Remove';
            removeButton.addEventListener('click', () => {
                listItem.remove();
                updatePriorities();
                showToast('success', `Question "${question.question_name}" removed`);
            });
            listItem.appendChild(priorityBadge);
            listItem.appendChild(questionName);
            listItem.appendChild(removeButton);
            return listItem;
        }
        async function fetchQuestionPriorities(templateId, factorId) {
            await apiRequest({
                url: `https://mhv-admin.hygeiaes.com/hra/templates/${templateId}/factor-priority/${factorId}/get-question-factor-priority`,
                method: 'GET',
                onSuccess: data => {
                    existingFactorsContainer.innerHTML = '';
                    showToast('success', 'Question priority fetched successfully');
                    if (data.result === 'success' && data.message && data.message.questions && data.message.questions.length > 0) {
                        const fragment = document.createDocumentFragment();
                        data.message.questions.forEach((question, index) => {
                            const listItem = createQuestionListItem(question, index + 1);
                            fragment.appendChild(listItem);
                        });
                        existingFactorsContainer.appendChild(fragment);
                        initializeSortable();
                        hideExistingQuestionsInDropdown();
                    } else {
                        noQuestions.textContent = 'No questions available for this factor.';
                    }
                },
                onError: error => {
                    showToast('error', error);
                    noQuestions.textContent = 'Error fetching question priorities';
                }
            });
        }
        function hideExistingQuestionsInDropdown() {
            const existingQuestions = existingFactorsContainer.querySelectorAll('.list-group-item');
            existingQuestions.forEach(function (item) {
                const questionId = item.getAttribute('data-id');
                $(`#questionsDropdown option[data-id='${questionId}']`).remove();
            });
        }
        function createTextElement(tag, text, classes = []) {
            const element = document.createElement(tag);
            element.textContent = text;
            classes.forEach(className => element.classList.add(className));
            return element;
        }
        function updatePriorities() {
            const listItems = document.querySelectorAll('#existing-questions-container .list-group-item');
            listItems.forEach((item, index) => {
                const priorityBadge = item.querySelector('.priority-badge');
                if (priorityBadge) {
                    priorityBadge.textContent = index + 1;
                }
            });
        }
        $('#questionsDropdown').on('select2:select', function (e) {
            const selectedQuestion = e.params.data;
            const questionName = selectedQuestion.text;
            const questionId = selectedQuestion.element.dataset.id;
            const existingItems = existingFactorsContainer.querySelectorAll('.list-group-item');
            const nextPriority = existingItems.length + 1;
            const question = {
                question_id: questionId,
                question_name: questionName
            };
            const listItem = createQuestionListItem(question, nextPriority);
            document.getElementById('no-questions-container').style.display = 'none';
            existingFactorsContainer.appendChild(listItem);
            const optionToRemove = $(`#questionsDropdown option[data-id='${questionId}']`);
            optionToRemove.remove();
            $('#questionsDropdown').val(null).trigger('change');
            $('#questionsDropdown').trigger('change');
            const remainingOptions = $('#questionsDropdown option').length;
            if (remainingOptions === 0) {
                $('#questionsDropdown').select2('placeholder', 'All questions added');
            } else {
                $('#questionsDropdown').select2('placeholder', 'Select a question');
            }
            updatePriorities();
            showToast('success', `Question "${questionName}" added to the list`);
        });
        $('#questionsDropdown').select2();
        factorDropdown.addEventListener('change', function () {
            const factorId = this.value;
            if (templateId && factorId) {
                const newUrl = `https://mhv-admin.hygeiaes.com/hra/templates/${templateId}/factor-priority/${factorId}/question-priority`;
                window.location.href = newUrl;
            }
        });
        if (factorDropdown.value) {
            fetchQuestionPriorities(templateId, factorDropdown.value);
        }
        fetchQuestions();
        initializeSortable();
    });
</script>
<!-- <script src="/lib/js/page-scripts/question-factor-priority.js"></script> -->
@endsection
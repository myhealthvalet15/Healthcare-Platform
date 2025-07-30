document.addEventListener('DOMContentLoaded', async function () {
    const templatesDropdown = document.getElementById('templates');
    const factorsDropdown = document.getElementById('factors');
    const factorsDropdownContainer = document.getElementById('factors-dropdown-container');
    const existingFactorsContainer = document.getElementById('existing-factors-container');
    const saveChangesButton = document.getElementById('save-changes-button');
    const noFactorsMessage = document.getElementById('no-factors-message');
    let templatesData = [];
    function initializeSortable() {
        if (window.Sortable) {
            Sortable.create(existingFactorsContainer, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function (evt) {
                    updatePrioritiesAfterDrag();
                    updateSaveButtonVisibility();
                }
            });
        } else {
            // console.error('Sortable.js is not loaded');
        }
    }
    function updatePrioritiesAfterDrag() {
        const factorRows = Array.from(existingFactorsContainer.querySelectorAll('.factor-row'));
        factorRows.forEach((row, index) => {
            const priorityBadge = row.querySelector('.priority-badge');
            if (priorityBadge) {
                priorityBadge.textContent = index + 1;
            }
        });
    }
    function updateSaveButtonVisibility() {
        const hasFactors = existingFactorsContainer.children.length > 1;
        // saveChangesButton.classList.toggle('d-none', !hasFactors);
        noFactorsMessage.style.display = hasFactors ? 'none' : 'block';
    }
    function createFactorRow(factorId, factorName, priority, isNewFactor = false) {
        const factorRow = document.createElement('div');
        factorRow.classList.add('list-group-item', 'list-group-item-action', 'factor-row', 'd-flex', 'align-items-center', 'drag-item', 'cursor-move');
        factorRow.dataset.factorId = factorId;
        const dragHandle = document.createElement('span');
        dragHandle.innerHTML = '&#9776;';
        dragHandle.classList.add('me-2', 'text-muted', 'drag-handle');
        factorRow.appendChild(dragHandle);
        const priorityBadge = document.createElement('span');
        priorityBadge.classList.add('badge', 'bg-primary', 'priority-badge');
        priorityBadge.textContent = parseInt(priority || 0);
        factorRow.appendChild(priorityBadge);
        const factorNameSpan = document.createElement('span');
        factorNameSpan.textContent = factorName;
        factorNameSpan.classList.add('flex-grow-1', 'ms-2');
        factorRow.appendChild(factorNameSpan);
        const actionButtonsContainer = document.createElement('div');
        actionButtonsContainer.classList.add('d-flex', 'align-items-center');
        if (!isNewFactor) {
            // Set Question Button
            const setQuestionButton = document.createElement('button');
            setQuestionButton.textContent = "Set Question";
            setQuestionButton.classList.add('btn', 'btn-sm', 'btn-success', 'me-2');
            setQuestionButton.addEventListener('click', () => {
                const templateId = templatesDropdown.value;
                if (!templateId || !factorId) {
                    showToast('error', 'Template or Factor is missing');
                    return;
                }
                const redirectUrl = `/hra/templates/${templateId}/factor-priority/${factorId}/question-priority/`;
                window.open(redirectUrl, '_blank');
            });
            actionButtonsContainer.appendChild(setQuestionButton);
            // Total Questions Badge
            const additionalBadge = document.createElement('span');
            additionalBadge.classList.add('badge', 'bg-primary', 'me-2');
            additionalBadge.textContent = "Total available questions: 0";
            actionButtonsContainer.appendChild(additionalBadge);
            // Fetch total question count
            fetchQuestionCount(factorId, additionalBadge);
        }
        // Remove Button
        const removeButton = document.createElement('button');
        removeButton.textContent = "Remove";
        removeButton.classList.add('btn', 'btn-sm', 'btn-danger');
        removeButton.addEventListener('click', () => {
            factorRow.remove();
            updateDropdown(factorId, factorName);
            updateSaveButtonVisibility();
        });
        actionButtonsContainer.appendChild(removeButton);
        factorRow.appendChild(actionButtonsContainer);
        return factorRow;
    }
    async function fetchQuestionCount(factorId, totalQuestionsBadge) {
        const templateId = templatesDropdown.value;
        document.getElementById('preloader').style.display = 'none';
        document.getElementById('contents-container').style.display = 'block';
        if (templateId && factorId) {
            const apiUrl = `/hra/templates/${templateId}/factor-priority/${factorId}/get-question-factor-priority/`;
            try {
                const response = await apiRequest({
                    url: apiUrl,
                    method: 'GET',
                    onSuccess: (responseData) => {
                        if (responseData.result === "success") {
                            if (responseData.message.questions && Array.isArray(responseData.message.questions)) {
                                const questionCount = responseData.message.questions.length;
                                totalQuestionsBadge.textContent = `Total available questions: ${questionCount}`;
                            } else {
                                totalQuestionsBadge.textContent = "Total available questions: 0";
                            }
                        } else {
                            totalQuestionsBadge.textContent = "Total available questions: 0";
                        }
                    },
                    onError: (errorMessage) => {
                        totalQuestionsBadge.textContent = "Total available questions: 0";
                        // console.error('Error fetching question count:', errorMessage);
                    },
                });
            } catch (error) {
                // console.error('Error fetching question count:', error);
                totalQuestionsBadge.textContent = "Total available questions: 0";
            }
        }
    }
    async function populateDropdown() {
        const apiUrl = "/hra/fetch-templates";
        await apiRequest({
            url: apiUrl,
            method: 'GET',
            onSuccess: (response) => {
                templatesData = response;
                templatesDropdown.innerHTML = '<option value="">Select a Template</option>';
                response.forEach((template) => {
                    const option = document.createElement('option');
                    option.value = template.template_id;
                    option.text = template.template_name;
                    templatesDropdown.appendChild(option);
                });
                document.getElementById("preloader").style.display = 'none';
                document.getElementById("contents-container").style.display = 'block';
            },
            onError: (errorMessage) => {
                showToast('error', 'Error fetching templates');
                // console.error('Error fetching templates:', errorMessage);
            },
        });
    }
    function updateDropdown(factorId, factorName) {
        const option = document.createElement('option');
        option.value = factorId;
        option.text = factorName;
        factorsDropdown.appendChild(option);
        factorsDropdownContainer.style.display = 'block';
    }
    function populateFactorsAndExisting(templateId) {
        const selectedTemplate = templatesData.find(
            (template) => template.template_id == templateId
        );
        if (!selectedTemplate) {
            showToast('error', 'Template not found');
            return;
        }
        const baseUrl = window.location.pathname.split('/').slice(0, -1).join('/');
        const newUrl = `${baseUrl}/${templateId}`;
        history.pushState({ templateId }, '', newUrl);
        factorsDropdownContainer.style.display = 'none';
        existingFactorsContainer.innerHTML = '';
        existingFactorsContainer.appendChild(noFactorsMessage);
        factorsDropdown.innerHTML = '<option value="">Select a Factor</option>';
        const { factors, priorities } = selectedTemplate;
        const factorPriorityList = Object.entries(factors).map(([factorId, factorName]) => ({
            factorId,
            factorName,
            priority: priorities[factorId] || 0
        })).filter(f => f.priority > 0)
            .sort((a, b) => a.priority - b.priority);
        factorPriorityList.forEach(({ factorId, factorName, priority }) => {
            const factorRow = createFactorRow(factorId, factorName, priority);
            existingFactorsContainer.appendChild(factorRow);
        });
        Object.entries(factors).forEach(([factorId, factorName]) => {
            if (!factorPriorityList.some(f => f.factorId === factorId)) {
                factorsDropdownContainer.style.display = 'block';
                const option = document.createElement('option');
                option.value = factorId;
                option.text = factorName;
                factorsDropdown.appendChild(option);
            }
        });
        initializeSortable();
        updateSaveButtonVisibility();
    }
    templatesDropdown.addEventListener('change', (event) => {
        const selectedTemplateId = event.target.value;
        if (selectedTemplateId) {
            populateFactorsAndExisting(selectedTemplateId);
        } else {
            factorsDropdownContainer.style.display = 'none';
            existingFactorsContainer.innerHTML = '';
            existingFactorsContainer.appendChild(noFactorsMessage);
            updateSaveButtonVisibility();
        }
    });
    factorsDropdown.addEventListener('change', function () {
        const selectedFactorId = factorsDropdown.value;
        if (selectedFactorId) {
            const selectedTemplate = templatesData.find(
                t => t.template_id == templatesDropdown.value
            );
            if (selectedTemplate) {
                const factorRows = Array.from(existingFactorsContainer.querySelectorAll('.factor-row'));
                const newPriority = factorRows.length + 1;
                const factorRow = createFactorRow(
                    selectedFactorId,
                    selectedTemplate.factors[selectedFactorId],
                    newPriority,
                    true
                );
                existingFactorsContainer.appendChild(factorRow);
                this.querySelector(`option[value="${selectedFactorId}"]`).remove();
                if (this.options.length <= 1) {
                    factorsDropdownContainer.style.display = 'none';
                }
                updateSaveButtonVisibility();
                initializeSortable();
            }
        }
    });
    saveChangesButton.addEventListener('click', async () => {
        const factorRows = Array.from(existingFactorsContainer.querySelectorAll('.factor-row'));
        const priorityData = factorRows.map((row, index) => ({
            factorId: row.dataset.factorId,
            priority: index + 1
        }));
        try {
            const response = await apiRequest({
                url: "/hra/templates/set-factor-priority/",
                method: 'PUT',
                data: {
                    templateId: templatesDropdown.value,
                    priorities: priorityData
                },
                onSuccess: (response) => {
                    showToast('success', 'Priorities updated successfully');
                    location.reload();
                },
                onError: (errorMessage) => {
                    showToast('error', errorMessage);
                }
            });
        } catch (error) {
            // console.error('Save error:', error);
            showToast('error', 'An error occurred while saving');
        }
    });
    document.getElementById('view-questions-button').addEventListener('click', () => {
        const templateId = templatesDropdown.value;
        if (!templateId) {
            showToast('error', 'Template or Factor is missing');
            return;
        }
        const redirectUrl = `/hra/templates/${templateId}/factor-priority/view-question-priority/`;
        window.open(redirectUrl, '_blank');
    });
    await populateDropdown();
    const templateIdFromUrl = window.location.pathname.split('/').pop();
    if (templateIdFromUrl) {
        templatesDropdown.value = templateIdFromUrl;
        populateFactorsAndExisting(templateIdFromUrl);
    }
    const style = document.createElement('style');
    style.textContent = `
        .drag-item {
            cursor: move;
            user-select: none;
        }
        .drag-item.sortable-ghost {
            opacity: 0.4;
        }
        .drag-handle {
            cursor: grab;
            margin-right: 10px;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
    `;
    document.head.appendChild(style);
});

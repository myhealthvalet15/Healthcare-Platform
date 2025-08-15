document.addEventListener('DOMContentLoaded', function () {
    const existingFactorsContainer = document.querySelector('#existing-questions-container');
    const noFactorsMessage = document.getElementById('no-factors-message');
    const publishButton = document.getElementById('publish-button');
    publishButton.addEventListener('click', async function () {
        const factors = [];
        document.querySelectorAll('[data-factor-id]').forEach(factorCard => {
            const factorId = factorCard.getAttribute('data-factor-id');
            const maxValue = document.getElementById(`maxValue-${factorId}`).value;
            const adjValue = document.getElementById(`adjValue-${factorId}`).value;
            factors.push({
                factor_id: factorId,
                max_value: maxValue,
                factor_adjustment_value: adjValue
            });
        });
        const totalAdjustmentValue = document.getElementById('adjustment-value').value;
        const payload = {
            template_id: templateId,
            total_adjustment_value: totalAdjustmentValue,
            factors: factors
        };
        try {
            const response = await apiRequest({
                url: `/hra/templates/publishTemplate`,
                method: 'POST',
                data: payload,
                onSuccess: (data) => {
                    showToast("success", "Question factor priorities updated successfully!");
                },
                onError: (error) => {
                    showToast("error", error);
                }
            });
        } catch (error) {
            showToast("error", "An unexpected error occurred.");
        }
    });
    async function fetchQuestionPriorities(templateId) {
        await apiRequest({
            url: `/hra/templates/${templateId}/getAllQuestionFactorPriority`,
            method: 'GET',
            onSuccess: (data) => {
                existingFactorsContainer.innerHTML = '';
                showToast("success", 'Question priority fetched successfully');
                document.getElementById('preloader').style.display = 'none';
                document.getElementById('contents-container').style.display = 'block';
                if (data.result === 'success') {
                    if (data.message && Object.keys(data.message).length > 0) {
                        noFactorsMessage.classList.add('d-none');
                        const templateGroups = {};
                        Object.values(data.message).forEach(factorData => {
                            if (!templateGroups[factorData.Template_name]) {
                                templateGroups[factorData.Template_name] = [];
                            }
                            templateGroups[factorData.Template_name].push(factorData);
                        });
                        Object.entries(templateGroups).forEach(([templateName, factors]) => {
                            factors.sort((a, b) => a.Factor_priority - b.Factor_priority);
                            var totalfactorAdjustValue = 0;
                            factors.forEach(factorData => {
                                const questions = Array.isArray(factorData.questions)
                                    ? factorData.questions
                                    : Object.values(factorData.questions);
                                (function (templateId, factorData) {
                                    const { maxValue, factorAdjustValue } = calculateFactorValues(questions);
                                    const factorHeaderContainer = document.createElement('div');
                                    factorHeaderContainer.classList.add('card', 'mb-3');
                                    factorHeaderContainer.setAttribute('data-factor-id', factorData.Factor_id);
                                    const factorHeader = document.createElement('div');
                                    factorHeader.classList.add('card-header', 'd-flex', 'justify-content-between', 'align-items-center');
                                    const factorTitleContainer = document.createElement('div');
                                    factorTitleContainer.classList.add('d-flex', 'align-items-center');
                                    const priorityBadge = createTextElement('span', `Priority: ${factorData.Factor_priority}`, ['badge', 'bg-danger']);
                                    priorityBadge.style.marginRight = '10px';
                                    const factorTitle = createTextElement('h5', `${factorData.Factor_name}`, ['mb-0']);
                                    factorTitleContainer.appendChild(priorityBadge);
                                    factorTitleContainer.appendChild(factorTitle);
                                    const inputContainer = document.createElement('div');
                                    inputContainer.classList.add('d-flex', 'gap-2', 'align-items-center');
                                    const maxValueLabel = createTextElement('label', 'Maximum Value:', ['form-label', 'mb-0']);
                                    const maxValueInput = document.createElement('input');
                                    maxValueInput.type = 'number';
                                    maxValueInput.classList.add('form-control', 'form-control-sm');
                                    maxValueInput.id = `maxValue-${factorData.Factor_id}`;
                                    maxValueInput.value = maxValue;
                                    maxValueInput.style.width = '100px';
                                    const adjValueLabel = createTextElement('label', 'Factor Adjustment Value:', ['form-label', 'mb-0']);
                                    const adjValueInput = document.createElement('input');
                                    adjValueInput.type = 'number';
                                    adjValueInput.id = `adjValue-${factorData.Factor_id}`;
                                    totalfactorAdjustValue += factorAdjustValue;
                                    adjValueInput.value = factorAdjustValue;
                                    adjValueInput.classList.add('form-control', 'form-control-sm');
                                    adjValueInput.style.width = '100px';
                                    inputContainer.appendChild(maxValueLabel);
                                    inputContainer.appendChild(maxValueInput);
                                    inputContainer.appendChild(adjValueLabel);
                                    inputContainer.appendChild(adjValueInput);
                                    factorHeader.appendChild(factorTitleContainer);
                                    factorHeader.appendChild(inputContainer);
                                    factorHeaderContainer.appendChild(factorHeader);
                                    const questionsList = document.createElement('div');
                                    questionsList.classList.add('list-group', 'list-group-flush');
                                    const sortedQuestions = questions.sort((a, b) => a.question_priority - b.question_priority);
                                    sortedQuestions.forEach((question, index) => {
                                        const listItem = createQuestionListItem(question, index + 1, templateId, factorData);
                                        questionsList.appendChild(listItem);
                                    });
                                    factorHeaderContainer.appendChild(questionsList);
                                    existingFactorsContainer.appendChild(factorHeaderContainer);
                                })(templateId, factorData);
                            });
                            document.getElementById('adjustment-value').value = totalfactorAdjustValue;
                        });
                    } else {
                        noFactorsMessage.classList.remove('d-none');
                        noFactorsMessage.textContent = 'No questions available.';
                    }
                } else {
                    noFactorsMessage.classList.remove('d-none');
                    noFactorsMessage.textContent = 'No Question Priorities Found';
                }
            },
            onError: (error) => {
                console.log("Error: " + error)
                showToast("error", "No Question Priorities Found");
                noFactorsMessage.classList.remove('d-none');
                noFactorsMessage.textContent = 'No Data Found';
                document.getElementById('preloader').style.display = 'none';
                document.getElementById('contents-container').style.display = 'block';
            }
        });
    }
    function calculateFactorValues(questions) {
        const genderQuestionGroups = {};
        questions.forEach(question => {
            const key = `${question.question_name}-${question.gender}`;
            if (!genderQuestionGroups[key]) {
                genderQuestionGroups[key] = [];
            }
            genderQuestionGroups[key].push(question);
        });
        const combinedQuestions = Object.entries(genderQuestionGroups).map(([key, questionGroup]) => {
            return questionGroup.reduce((highest, current) => {
                const currentPoints = current.points ? JSON.parse(current.points.replace(/\\/g, '')) : {};
                const highestPoints = highest && highest.points ? JSON.parse(highest.points.replace(/\\/g, '')) : {};
                const currentPointsArray = Object.values(currentPoints).map(p => Number(p));
                const highestPointsArray = Object.values(highestPoints).map(p => Number(p));
                const isCurrentBetter = currentPointsArray.at(-1) > (highestPointsArray.at(-1) || -Infinity);
                return isCurrentBetter ? current : highest;
            });
        });
        const maxValue = combinedQuestions.reduce((total, question) => {
            if (!question.points) return total;
            const points = JSON.parse(question.points.replace(/\\/g, ''));
            const pointValues = Object.values(points).map(p => Number(p));
            const maxPositiveValue = Math.max(...pointValues.filter(value => value > 0), 0);
            return total + maxPositiveValue;
        }, 0);
        const factorAdjustValue = combinedQuestions.reduce((total, question) => {
            if (!question.points) return total;
            const points = JSON.parse(question.points.replace(/\\/g, ''));
            const pointValues = Object.values(points).map(p => Number(p));
            const minValue = Math.min(...pointValues);
            return total + minValue;
        }, 0);
        return { maxValue, factorAdjustValue: Math.abs(factorAdjustValue) };
    }
    function createQuestionListItem(question, priority, templateId, factorData) {
        const listItem = document.createElement('div');
        listItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center', 'py-3', 'question-link');
        listItem.setAttribute('data-id', question.question_id);
        listItem.style.cursor = 'pointer';
        const priorityBadge = createTextElement('span', priority, ['badge', 'bg-primary', 'priority-badge']);
        priorityBadge.style.marginRight = '0.5rem';
        const questionContainer = document.createElement('div');
        questionContainer.classList.add('d-flex', 'flex-column', 'flex-grow-1', 'align-items-start');
        const questionRow = document.createElement('div');
        questionRow.classList.add('d-flex', 'align-items-center', 'gap-2', 'flex-wrap');
        const questionName = document.createElement('a');
        questionName.href = `/hra/templates/trigger-questions/${templateId}/${factorData.Factor_id}/${question.question_id}`;
        questionName.classList.add('question-name', 'text-decoration-none', 'text-body', 'me-2');
        const questionText = document.createTextNode(question.question_name);
        questionName.appendChild(questionText);
        let genderList = [];
        try {
            genderList = JSON.parse(question.gender);
        } catch (error) {
            console.error("Error parsing gender:", error);
        }
        const genderContainer = document.createElement('div');
        genderContainer.classList.add('d-flex', 'gap-1', 'flex-nowrap');
        genderList.forEach(gender => {
            let genderClass = 'bg-secondary';
            if (gender.toLowerCase() === 'male') {
                genderClass = 'bg-label-primary';
            } else if (gender.toLowerCase() === 'female') {
                genderClass = 'bg-label-danger';
            } else if (gender.toLowerCase() === 'third_gender') {
                genderClass = 'bg-label-secondary';
                gender = 'Third Gender';
            }
            const genderBadge = createTextElement('span', gender, ['badge', genderClass]);
            genderContainer.appendChild(genderBadge);
        });
        questionRow.appendChild(questionName);
        questionRow.appendChild(genderContainer);
        questionContainer.appendChild(questionRow);
        let pointsDisplay = '';
        try {
            if (question.points) {
                const pointsObj = JSON.parse(question.points.replace(/\\/g, ''));
                pointsDisplay = Object.values(pointsObj).join(' / ');
            } else {
                pointsDisplay = 'N/A';
            }
        } catch (error) {
            pointsDisplay = 'N/A';
        }
        const pointsBadge = createTextElement('span', pointsDisplay, ['badge', 'bg-label-success']);
        listItem.appendChild(priorityBadge);
        listItem.appendChild(questionContainer);
        listItem.appendChild(pointsBadge);
        listItem.addEventListener('click', (e) => {
            window.location.href = `/hra/templates/trigger-questions/${templateId}/${factorData.Factor_id}/${question.question_id}`;
        });
        return listItem;
    }
    function createTextElement(tag, text, classes = []) {
        const element = document.createElement(tag);
        element.textContent = text;
        classes.forEach(className => element.classList.add(className));
        return element;
    }
    fetchQuestionPriorities(templateId);
});

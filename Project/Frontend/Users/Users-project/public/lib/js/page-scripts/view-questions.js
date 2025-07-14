document.addEventListener('DOMContentLoaded', function () {
    const existingFactorsContainer = document.querySelector('#existing-questions-container');
    const noFactorsMessage = document.getElementById('no-factors-message');
    const saveChangesButton = document.getElementById('save-changes-button');
    saveChangesButton.addEventListener('click', function () {
        showToast("error", "nothing will happen, this feature is on development phase..")
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
                                // Normalize questions as an array
                                const questions = Array.isArray(factorData.questions)
                                    ? factorData.questions
                                    : Object.values(factorData.questions);

                                // Pass templateId and factorData to a closure
                                (function (templateId, factorData) {
                                    const { maxValue, factorAdjustValue } = calculateFactorValues(questions);

                                    const factorHeaderContainer = document.createElement('div');
                                    factorHeaderContainer.classList.add('card', 'mb-3');
                                    factorHeaderContainer.setAttribute('data-factor-id', factorData.Factor_id);

                                    const factorHeader = document.createElement('div');
                                    factorHeader.classList.add('card-header', 'd-flex', 'justify-content-between', 'align-items-center');

                                    // Create a container for the title and badge, starting with the priority badge
                                    const factorTitleContainer = document.createElement('div');
                                    factorTitleContainer.classList.add('d-flex', 'align-items-center');

                                    // Create a badge for the priority
                                    const priorityBadge = createTextElement('span', `Priority: ${factorData.Factor_priority}`, ['badge', 'bg-danger']);
                                    priorityBadge.style.marginRight = '10px';

                                    // Create the factor name
                                    const factorTitle = createTextElement('h5', `${factorData.Factor_name}`, ['mb-0']);

                                    // Append the badge and factor name in the desired order
                                    factorTitleContainer.appendChild(priorityBadge);
                                    factorTitleContainer.appendChild(factorTitle);

                                    // Create a container for the input fields
                                    const inputContainer = document.createElement('div');
                                    inputContainer.classList.add('d-flex', 'gap-2', 'align-items-center');

                                    // Create the Maximum Value label and input
                                    const maxValueLabel = createTextElement('label', 'Maximum Value:', ['form-label', 'mb-0']);
                                    const maxValueInput = document.createElement('input');
                                    maxValueInput.type = 'number';
                                    maxValueInput.classList.add('form-control', 'form-control-sm');
                                    maxValueInput.id = `maxValue-${factorData.Factor_id}`;
                                    maxValueInput.value = maxValue;
                                    maxValueInput.style.width = '100px';

                                    // Create the Factor Adjustment Value label and input
                                    const adjValueLabel = createTextElement('label', 'Factor Adjustment Value:', ['form-label', 'mb-0']);
                                    const adjValueInput = document.createElement('input');
                                    adjValueInput.type = 'number';
                                    adjValueInput.id = `adjValue-${factorData.Factor_id}`;
                                    totalfactorAdjustValue += factorAdjustValue;
                                    adjValueInput.value = factorAdjustValue;
                                    adjValueInput.classList.add('form-control', 'form-control-sm');
                                    adjValueInput.style.width = '100px';

                                    // Append labels and inputs to the input container
                                    inputContainer.appendChild(maxValueLabel);
                                    inputContainer.appendChild(maxValueInput);
                                    inputContainer.appendChild(adjValueLabel);
                                    inputContainer.appendChild(adjValueInput);

                                    // Append the title container and input container to the header
                                    factorHeader.appendChild(factorTitleContainer);
                                    factorHeader.appendChild(inputContainer);
                                    factorHeaderContainer.appendChild(factorHeader);

                                    const questionsList = document.createElement('div');
                                    questionsList.classList.add('list-group', 'list-group-flush');

                                    const sortedQuestions = questions.sort((a, b) => a.question_priority - b.question_priority);
                                    sortedQuestions.forEach((question, index) => {
                                        // Create a closure to capture the current context
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
        // Group questions by their name and gender
        questions.forEach(question => {
            const key = `${question.question_name}-${question.gender}`;
            if (!genderQuestionGroups[key]) {
                genderQuestionGroups[key] = [];
            }
            genderQuestionGroups[key].push(question);
        });
        const combinedQuestions = Object.entries(genderQuestionGroups).map(([key, questionGroup]) => {
            // Find the question with the highest points values
            return questionGroup.reduce((highest, current) => {
                const currentPoints = JSON.parse(current.points.replace(/\\/g, ''));
                const highestPoints = highest ? JSON.parse(highest.points.replace(/\\/g, '')) : {};
                // Convert points to an array and parse as numbers
                const currentPointsArray = Object.values(currentPoints).map(p => Number(p));
                const highestPointsArray = Object.values(highestPoints).map(p => Number(p));
                // Compare the last point value for max calculation
                const isCurrentBetter = currentPointsArray.at(-1) > (highestPointsArray.at(-1) || -Infinity);
                return isCurrentBetter ? current : highest;
            });
        });
        const maxValue = combinedQuestions.reduce((total, question) => {
            const points = JSON.parse(question.points.replace(/\\/g, ''));
            const lastValue = Number(Object.values(points).at(-1) || 0);
            return total + lastValue;
        }, 0);
        let highestNegative = 0;
        const factorAdjustValue = combinedQuestions.reduce((total, question) => {
            const points = JSON.parse(question.points.replace(/\\/g, ''));
            const firstValue = Number(Object.values(points)[0] || 0);
            if (firstValue < 0 && Math.abs(firstValue) > Math.abs(highestNegative)) {
                highestNegative = firstValue;
            }
            return firstValue > 0 ? total + firstValue : total;
        }, 0) + Math.abs(highestNegative);
        return { maxValue, factorAdjustValue };
    }

    function createTextElement(tag, text, classes = []) {
        const element = document.createElement(tag);
        element.textContent = text;
        classes.forEach(className => element.classList.add(className));
        return element;
    }

    function createQuestionListItem(question, priority, templateId, factorData) {
        const listItem = document.createElement('div');
        listItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center', 'py-3', 'question-link');
        listItem.setAttribute('data-id', question.question_id);
        listItem.style.cursor = 'pointer'; // Add cursor pointer to indicate it's clickable

        const priorityBadge = createTextElement('span', priority, ['badge', 'bg-primary', 'priority-badge']);
        priorityBadge.style.marginRight = '0.5rem';

        const questionContainer = document.createElement('div');
        questionContainer.classList.add('d-flex', 'flex-column', 'flex-grow-1', 'align-items-start');

        const questionName = document.createElement('a');
        questionName.href = `/hra/templates/trigger-questions/${templateId}/${factorData.Factor_id}/${question.question_id}`;
        questionName.classList.add('question-name', 'flex-grow-1', 'text-decoration-none', 'text-body');
        const questionText = document.createTextNode(question.question_name);

        // Parse and display points
        let pointsDisplay = '';
        try {
            const pointsObj = JSON.parse(question.points.replace(/\\/g, ''));
            pointsDisplay = Object.values(pointsObj).join(' / ');
        } catch (error) {
            // console.error('Error parsing points:', error);
            pointsDisplay = 'N/A';
        }

        const pointsBadge = createTextElement('span', pointsDisplay, ['badge', 'bg-label-success']);

        // Gender badge
        let genderClass = 'bg-secondary';
        let genderText = question.gender;
        if (question.gender.toLowerCase() === 'male') {
            genderClass = 'bg-label-primary';
        } else if (question.gender.toLowerCase() === 'female') {
            genderClass = 'bg-label-danger';
        } else if (question.gender.toLowerCase() === 'third_gender') {
            genderClass = 'bg-label-secondary';
            genderText = 'Third Gender';
        }
        const genderBadge = createTextElement('span', genderText, ['badge', genderClass, 'ms-2']);
        genderBadge.style.textAlign = 'center';

        questionName.appendChild(questionText);
        questionName.appendChild(genderBadge);

        questionContainer.appendChild(questionName);

        listItem.appendChild(priorityBadge);
        listItem.appendChild(questionContainer);
        listItem.appendChild(pointsBadge);

        // Add click event listener for navigation
        listItem.addEventListener('click', (e) => {
            window.location.href = `/hra/templates/trigger-questions/${templateId}/${factorData.Factor_id}/${question.question_id}`;
        });

        return listItem;
    }

    fetchQuestionPriorities(templateId);
});


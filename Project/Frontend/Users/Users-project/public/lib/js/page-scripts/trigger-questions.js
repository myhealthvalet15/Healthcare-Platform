document.addEventListener('DOMContentLoaded', function () {
    const url = window.location.href;
    const regex = /(\d+)\/(\d+)\/(\d+)$/;
    const matches = url.match(regex);
    const template_id = matches[1];
    const factor_id = matches[2];
    const question_id = matches[3];
    async function fetchAndPopulateQuestions() {
        try {
            const response = await apiRequest({
                url: 'https://mhv-admin.hygeiaes.com/hra/get-all-questions',
                method: 'GET',
            });

            if (response && Array.isArray(response.data)) {
                showToast('success', 'Questions fetched successfully');

                const triggeredQuestionsData = await fetchExistingTriggeredQuestions();
                document.getElementById('preloader').style.display = 'none';
                document.getElementById('contents-container').style.display = 'block';
                const triggers = Object.keys(triggeredQuestionsData.message.triggers);
                const maxTriggers = triggers.reduce((max, triggerName) => {
                    const match = triggerName.match(/(\d+)$/);
                    if (match) {
                        return Math.max(max, parseInt(match[1], 10));
                    }
                    return max;
                }, -Infinity);


                const triggerQuestionDropdowns = document.querySelectorAll('select[id^="select2Multiple_key"]');
                const maxDropdowns = Array.from(triggerQuestionDropdowns).reduce((max, select) => {
                    const match = select.id.match(/select2Multiple_key(\d+)/);
                    if (match) {
                        return Math.max(max, parseInt(match[1], 10));
                    }
                    return max;
                }, -Infinity);




                const populateDropdowns = (triggers = null) => {
                    if (triggers === null) {
                        triggerQuestionDropdowns.forEach(dropdown => {
                            dropdown.innerHTML = '';
                            response.data.forEach(question => {
                                const option = document.createElement('option');
                                option.value = question.question_id;
                                option.setAttribute('data-id', question.question_id);
                                option.textContent = question.question;
                                dropdown.appendChild(option);
                            });
                            $(dropdown).select2('destroy').select2();
                        });
                    } else {
                        triggerQuestionDropdowns.forEach(dropdown => {
                            dropdown.innerHTML = '';
                            response.data.forEach(question => {
                                const option = document.createElement('option');
                                option.value = question.question_id;
                                option.setAttribute('data-id', question.question_id);
                                option.textContent = question.question;
                                dropdown.appendChild(option);
                            });
                            $(dropdown).select2('destroy').select2();
                        });

                        setTimeout(() => {
                            let i = 0;
                            Object.values(triggers).forEach(trigger => {
                                Object.values(trigger).forEach((questionValue, questionIndex) => {
                                    let id = questionValue;
                                    const dropdown = triggerQuestionDropdowns[i];
                                    const optionToSelect = dropdown.querySelector(`option[data-id="${id}"]`);
                                    if (optionToSelect) {
                                        optionToSelect.selected = true;
                                    }
                                    $(dropdown).select2('destroy').select2();
                                });
                                i++;
                            });
                        }, 100);


                    }
                };
                if (maxTriggers <= maxDropdowns) {
                    populateDropdowns(triggeredQuestionsData.message.triggers);
                } else {
                    populateDropdowns();
                }
            } else {
                // console.error('Error: No data found in response', response);
                showToast('error', 'Failed to load questions: No data found');
            }
            return response;
        } catch (error) {
            // console.error('Error fetching questions:', error);
            showToast('error', 'An unexpected error occurred');
            return null;
        }
    }
    async function fetchExistingTriggeredQuestions() {
        try {
            const response = await apiRequest({
                url: `/hra/templates/get-trigger-questions/${template_id}/${factor_id}/${question_id}`,
                method: 'GET',
            });
            return response;
        } catch (error) {
            // console.error('Error fetching triggered questions:', error);
            showToast('error', 'An unexpected error occurred');
            return null;
        }
    }
    fetchAndPopulateQuestions();

    document.getElementById('save-changes-button').addEventListener('click', function () {
        const triggerQuestions = [];
        document.querySelectorAll('[id^="select2Multiple_"]').forEach(dropdown => {
            const selectedOptions = $(dropdown).select2('data');
            selectedOptions.forEach(option => {
                triggerQuestions.push({
                    answerId: dropdown.id.replace('select2Multiple_', ''),
                    questionId: option.id,
                });
            });
        });
        apiRequest({
            url: `/hra/templates/set-trigger-questions/${template_id}/${factor_id}/${question_id}`,
            method: 'PUT',
            data: { data: triggerQuestions },
            onSuccess: function (responseData) {
                showToast('success', `${triggerQuestions.length} trigger questions selected`);
                location.reload();
            },
            onError: function (errorMessage) {
                showToast('error', `Error: ${errorMessage}`);
            }
        });
    });
    document.getElementById('go-back').addEventListener('click', function () {
        window.location.href = `https://mhv-admin.hygeiaes.com/hra/templates/${template_id}/factor-priority/view-question-priority`;
    });
});


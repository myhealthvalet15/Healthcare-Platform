const maxRows = 8;
const addRowButton = document.getElementById('add-row');
const container = document.getElementById('answer-points-container');
addRowButton.addEventListener('click', function () {
    const existingRows = container.querySelectorAll('.row').length;
    if (existingRows < maxRows) {
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 align-items-center';
        const answerCol = document.createElement('div');
        answerCol.className = 'col-5';
        const answerInput = document.createElement('input');
        answerInput.type = 'text';
        answerInput.className = 'form-control';
        answerInput.placeholder = 'Type Answers Here';
        answerInput.name = 'hra_answers[]';
        answerCol.appendChild(answerInput);
        const pointsCol = document.createElement('div');
        pointsCol.className = 'col-3';
        const pointsInput = document.createElement('input');
        pointsInput.type = 'text';
        pointsInput.className = 'form-control';
        pointsInput.placeholder = 'Type Points Here';
        pointsInput.name = 'hra_points[]';
        pointsCol.appendChild(pointsInput);
        const compareCol = document.createElement('div');
        compareCol.className = 'col-2';
        const compareInput = document.createElement('input');
        compareInput.type = 'text';
        compareInput.className = 'form-control';
        compareInput.placeholder = 'Type Compare Values Here';
        compareInput.name = 'compare_values[]';
        compareCol.appendChild(compareInput);
        const buttonCol = document.createElement('div');
        buttonCol.className = 'col-1';
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger w-100 remove-row';
        const removeIcon = document.createElement('i');
        removeIcon.className = 'fas fa-minus fa-md';
        removeButton.appendChild(removeIcon);
        buttonCol.appendChild(removeButton);
        newRow.appendChild(answerCol);
        newRow.appendChild(pointsCol);
        newRow.appendChild(compareCol);
        newRow.appendChild(buttonCol);
        container.appendChild(newRow);
        if (!$('#hra_compare_value').is(':checked')) {
            newRow.querySelector('input[name="compare_values[]"]').disabled = true;
        }
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Maximum Rows Reached!',
            text: 'You can only add up to 8 rows.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-warning'
            }
        });
    }
});
container.addEventListener('click', function (e) {
    if (e.target.closest('.remove-row')) {
        const row = e.target.closest('.row');
        if (row) {
            row.remove();
        }
    }
});
$('#hra_compare_value').change(function () {
    const compareValueInputs = $('input[name="compare_values[]"]');
    if ($(this).is(':checked')) {
        compareValueInputs.prop('disabled', false);
    } else {
        compareValueInputs.prop('disabled', true);
        compareValueInputs.val('');
    }
});
$('input[name="compare_values[]"]').prop('disabled', true);
$(document).ready(function () {
    $('#select2Success').change(function () {
        if ($('#select2Success').val()) {
            $('#test-header').prop('selected', false);
        } else {
            $('#test-header').prop('selected', true);
        }
    });
    fetchTestNames();
    async function fetchTestNames() {
        try {
            const data = await apiRequest({
                url: '/hra/master-test-names',
                method: 'GET',
                onSuccess: data => {
                    const selectElement = document.getElementById('select2Success');
                    if (data && typeof data === 'object') {
                        for (const testId in data) {
                            if (data.hasOwnProperty(testId)) {
                                const testName = data[testId];
                                const option = document.createElement('option');
                                option.value = testId;
                                option.textContent = testName;
                                selectElement.appendChild(option);
                            }
                        }
                    } else {
                        showToast('error', 'Invalid data format, ' + data);
                    }
                },
                onError: error => {
                    showToast('error', 'Error Fetching Test Records, ' + error);
                }
            });
        } catch (error) {
            showToast('error', 'Error Fetching Test Records ' + error);
        }
    }
    function setButtonLoading(button, isLoading) {
        if (isLoading) {
            button.data('original-html', button.html());
            const spinner = document.createElement('span');
            spinner.className = 'spinner-border spinner-border-sm me-2';
            spinner.setAttribute('role', 'status');
            spinner.setAttribute('aria-hidden', 'true');
            const loadingText = document.createTextNode('Saving...');
            button.empty();
            button.append(spinner);
            button.append(loadingText);
            button.prop('disabled', true);
        } else {
            button.html(button.data('original-html'));
            button.prop('disabled', false);
            button.removeData('original-html');
        }
    }
    $('button[type="submit"]').click(function (event) {
        event.preventDefault();
        const submitButton = $(this);
        let isValid = true;
        const showError = (input, message) => {
            if (!$(input).next('small.text-danger').length) {
                $(input).after(`<small class="text-danger">${message}</small>`);
            }
            $(input).addClass('is-invalid');
        };
        const clearErrors = () => {
            $('.text-danger').remove();
            $('.is-invalid').removeClass('is-invalid');
        };
        const removeErrorOnInput = input => {
            $(input).on('input change', function () {
                $(this).removeClass('is-invalid');
                $(this).next('small.text-danger').remove();
            });
        };
        clearErrors();
        if (!$('#hra-question').val().trim()) {
            showError('#hra-question', 'Question is required.');
            removeErrorOnInput('#hra-question');
            isValid = false;
        }
        let hasValidAnswer = false;
        $('input[name="hra_answers[]"]').each(function () {
            if ($(this).val().trim()) {
                hasValidAnswer = true;
            }
            removeErrorOnInput(this);
        });
        const selectedOptionType = $('#select2Additional').val();
        const isInputBox = selectedOptionType === 'Input Box';
        if (!isInputBox) {
            let hasValidAnswer = false;
            $('input[name="hra_answers[]"]').each(function () {
                if ($(this).val().trim()) {
                    hasValidAnswer = true;
                }
                removeErrorOnInput(this);
            });
            if (!hasValidAnswer) {
                showError($('input[name="hra_answers[]"]').first(), 'At least one answer is required for this option type.');
                isValid = false;
            }
        }
        $('#select2Additional').on('change', function () {
            const selectedType = $(this).val();
            const answerContainer = $('#answer-points-container');
            if (selectedType === 'Input Box') {
                answerContainer.hide();
            } else {
                answerContainer.show();
            }
        });
        if ($('#hra_compare_value').is(':checked')) {
            let hasAtLeastOneValue = false;
            $('input[name="compare_values[]"]').each(function () {
                if ($(this).val().trim()) {
                    hasAtLeastOneValue = true;
                    return false;
                }
            });
            if (!hasAtLeastOneValue) {
                showError(
                    $('input[name="compare_values[]"]').first(),
                    'At least one Compare Value is required when Compare Values is checked.'
                );
                removeErrorOnInput($('input[name="compare_values[]"]').first());
                isValid = false;
            }
        }
        if (!$('#select2Additional').val()) {
            const dropdownParent = $('#select2Additional').parent();
            if (!dropdownParent.find('small.text-danger').length) {
                dropdownParent.append('<small class="text-danger">Please select an option type.</small>');
            }
            $('#select2Additional').on('change', function () {
                $(this).removeClass('is-invalid');
                dropdownParent.find('small.text-danger').remove();
            });
            $('#select2Additional').addClass('is-invalid');
            isValid = false;
        }
        if (!isValid) {
            return;
        }
        const checkboxes = document.querySelectorAll("input[name='hra_gender']");
        function getSelectedGenders() {
            let selectedValues = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedValues.push(checkbox.value);
                }
            });
            return selectedValues;
        }
        var gender = getSelectedGenders();
        if (gender.length === 0) {
            showError($('input[name="hra_gender"]').first().closest('.d-flex'), 'Gender selection is required.');
            $('input[name="hra_gender"]').on('change', function () {
                $(this).closest('.d-flex').next('.text-danger').remove();
            });
            isValid = false;
        }
        if (!isValid) {
            return;
        }
        setButtonLoading(submitButton, true);
        var formData = new FormData();
        formData.append('formula', $('#hra-formula').val());
        formData.append('question', $('#hra-question').val());
        formData.append('dashboard_text', $('#dashboard-text').val());
        formData.append('comments', $('#hra_comments').val());
        gender.forEach(g => {
            formData.append('gender[]', g);
        });
        formData.append('tests', JSON.stringify($('#select2Success').val().map(Number)));
        formData.append('option_type', $('#select2Additional').val());
        formData.append('is_compare_values', $('#hra_compare_value').is(':checked') ? 1 : 0);
        $('input[name="hra_answers[]"]').each(function (index) {
            formData.append(`answers[${index}]`, $(this).val());
        });
        $('input[name="hra_points[]"]').each(function (index) {
            formData.append(`points[${index}]`, $(this).val());
        });
        $('input[name="compare_values[]"]').each(function (index) {
            formData.append(`compare_values[${index}]`, $(this).val());
        });
        if ($('#dropzone-basic')[0].dropzone.files.length > 0) {
            formData.append('image', $('#dropzone-basic')[0].dropzone.files[0]);
        }
        $.ajaxSetup({
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/hra/add-question',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                setButtonLoading(submitButton, false);
                if (response.result) {
                    showToast('success', response.message || 'Operation was successful!');
                    Swal.fire({
                        icon: 'success',
                        title: 'Question Added Successfully!',
                        text: response.message,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    showToast('error', response.message || 'An error occurred!');
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to add Questions, try again.',
                        text: response.message,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                setButtonLoading(submitButton, false);
                const errorMessage =
                    xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred!';
                showToast('error', errorMessage);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to add Questions, try again.',
                    text: errorMessage,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });
    });
    function clearForm() {
        $('#hra-formula').val('');
        $('#hra_comments').val('');
        $('#hra-question').val('');
        $('#dashboard-text').val('');
        $('#hra-comments').val('');
        $('input[name="hra_answers[]"]').val('');
        $('input[name="hra_points[]"]').val('');
        $('input[name="compare_values[]"]').val('').prop('disabled', true);
        $('#select2Success').val(null).trigger('change');
        $('#select2Additional').val('selectBox').trigger('change');
        $('#hra_compare_value').prop('checked', false);
        $('input[name="hra_gender"]').prop('checked', false);
        $('#dropzone-basic')[0].dropzone.removeAllFiles();
    }
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.row').remove();
    });
});

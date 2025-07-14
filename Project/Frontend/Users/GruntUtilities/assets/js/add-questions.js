

const maxRows = 8;
const addRowButton = document.getElementById('add-row');
const container = document.getElementById('answer-points-container');

addRowButton.addEventListener('click', function () {
    const existingRows = container.querySelectorAll('.row').length;
    if (existingRows < maxRows) {
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 align-items-center';
        newRow.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control" placeholder="Type Answers Here" name="hra_answers[]">
                </div>
                <div class="col-3">
                    <input type="text" class="form-control" placeholder="Type Points Here" name="hra_points[]">
                </div>
                <div class="col-2">
                    <input type="text" class="form-control" placeholder="Type Compare Values Here" name="compare_values[]">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger w-100 remove-row">
                        <i class="fas fa-minus fa-md"></i> 
                    </button>
                </div>`;
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
                onSuccess: (data) => {
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
                        // console.error('Invalid data format: ', data);
                        showToast('error', 'Invalid data format, ' + data);
                    }
                },
                onError: (error) => {
                    showToast('error', 'Error Fetching Test Records, ' + error);
                }
            });
        } catch (error) {
            showToast('error', 'Error Fetching Test Records ' + error);
        }
    }

    $('button[type="submit"]').click(function (event) {
        event.preventDefault();

        // Validation
        let isValid = true;

        const showError = (input, message) => {
            if (!$(input).next("small.text-danger").length) {
                $(input).after(`<small class="text-danger">${message}</small>`);
            }
            $(input).addClass("is-invalid");
        };

        const clearErrors = () => {
            $(".text-danger").remove();
            $(".is-invalid").removeClass("is-invalid");
        };

        const removeErrorOnInput = (input) => {
            $(input).on("input change", function () {
                $(this).removeClass("is-invalid");
                $(this).next("small.text-danger").remove();
            });
        };

        clearErrors();

        // Validate Question
        if (!$('#hra-question').val().trim()) {
            showError('#hra-question', 'Question is required.');
            removeErrorOnInput('#hra-question');
            isValid = false;
        }
        // Validate at least one Answer
        let hasValidAnswer = false;
        $('input[name="hra_answers[]"]').each(function () {
            if ($(this).val().trim()) {
                hasValidAnswer = true;
            }
            removeErrorOnInput(this);
        });
        if (!hasValidAnswer) {
            showError($('input[name="hra_answers[]"]').first(), 'At least one answer is required.');
            isValid = false;
        }

        // Validate Gender
        if (!$('input[name="hra_gender"]:checked').val()) {
            showError($('input[name="hra_gender"]').first().parent(), 'Gender selection is required.');
            $('input[name="hra_gender"]').on("change", function () {
                $(this).parent().find(".text-danger").remove();
            });
            isValid = false;
        }
        if (!$('#select2Success').val() || $('#select2Success').val().length === 0) {
            const select2Container = $('#select2Success').next('.select2-container');
            showError(select2Container, 'Please select at least one master test.');

            // Add change event handler
            $('#select2Success').on("change", function () {
                if ($(this).val() && $(this).val().length > 0) {
                    select2Container.removeClass("is-invalid");
                    select2Container.next("small.text-danger").remove();
                }
            });
            isValid = false;
        }

        // Validate Compare Values
        if ($('#hra_compare_value').is(':checked')) {
            let hasAtLeastOneValue = false;
            $('input[name="compare_values[]"]').each(function () {
                if ($(this).val().trim()) {
                    hasAtLeastOneValue = true;
                    return false; // Break the loop once we find one filled value
                }
            });

            if (!hasAtLeastOneValue) {
                showError($('input[name="compare_values[]"]').first(), 'At least one Compare Value is required when Compare Values is checked.');
                removeErrorOnInput($('input[name="compare_values[]"]').first());
                isValid = false;
            }
        }
        // Validate Option Type
        if (!$('#select2Additional').val()) {
            const dropdownParent = $('#select2Additional').parent();
            if (!dropdownParent.find('small.text-danger').length) {
                dropdownParent.append('<small class="text-danger">Please select an option type.</small>');
            }
            $('#select2Additional').on("change", function () {
                $(this).removeClass("is-invalid");
                dropdownParent.find("small.text-danger").remove();
            });
            $('#select2Additional').addClass("is-invalid");
            isValid = false;
        }


        if (!isValid) {
            return;
        }

        // Prepare Form Data
        var formData = new FormData();
        formData.append('formula', $('#hra-formula').val());
        formData.append('question', $('#hra-question').val());
        formData.append('dashboard_text', $('#dashboard-text').val());
        formData.append('comments', $('#hra_comments').val());
        formData.append('gender', $('input[name="hra_gender"]:checked').val());
        formData.append('tests', JSON.stringify($('#select2Success').val().map(Number)));
        formData.append('option_type', $('#select2Additional').val());
        formData.append('input_box', $('#hra_input_box').is(':checked') ? 1 : 0);
        formData.append('is_compare_values', $('#hra_compare_value').is(':checked') ? 1 : 0);

        // Add multiple answers, points, and compare values
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
                // 'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // to start here 
        $.ajax({
            url: '/hra/add-question',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
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
                        icon: 'Failed',
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
                const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred !';
                showToast('error', errorMessage);
                Swal.fire({
                    icon: 'Failed',
                    title: 'Failed to add Questions, try again.',
                    text: errorMessage,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });
        // to end here 
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
        $('#hra_input_box').prop('checked', false);
        $('#hra_compare_value').prop('checked', false);
        $('input[name="hra_gender"]').prop('checked', false);
        $('#dropzone-basic')[0].dropzone.removeAllFiles();
    }
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.row').remove();
    });
});

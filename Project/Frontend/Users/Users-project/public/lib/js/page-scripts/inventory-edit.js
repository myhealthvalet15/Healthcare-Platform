$(document).ready(function () {
    const url = window.location.href;
    const inventoryId = url.split('/').pop();
    apiRequest({
        url: `/others/inventory-history/${inventoryId}`,
        method: 'GET',
        onSuccess: function (data) {
            if (data.calibration_history) {
                renderCalibrationHistory(data.calibration_history);

                const history = data.calibration_history;
                const latestStatus = history.length > 0 && history[history.length - 1].in_use
                    ? 'In Use'
                    : 'Not In Use';

                handleStatus(latestStatus);
            } else {
                console.error('Calibration history not found');
            }
        },
        onError: function (error) {
            console.error('Error fetching calibration history:', error);
        }
    });

    function renderCalibrationHistory(calibrationHistory) {
        const historyList = $('#calibration-history-list');
        historyList.empty();
        if (calibrationHistory.length > 0) {
            calibrationHistory.forEach(function (item) {
                const status = item.in_use ? 'In Use' : 'Not In Use';
                const row = `<tr>
                                <td>${moment(item.calibrated_date).format('DD-MM-YYYY')}</td>
                                <td>${item.calibration_comments}</td>
                                <td>${status}</td>
                                <td>${moment(item.updated_at).format('DD-MM-YYYY HH:mm:ss')}</td>
                            </tr>`;
                historyList.append(row);
            });
        } else {
            historyList.append('<tr><td colspan="4" class="text-center">No calibration history available</td></tr>');
        }
    }
    function handleStatus(status) {
        if (status === 'Not In Use') {
            $('#wizard-validation-form input, #wizard-validation-form textarea, #edit-inventory').prop('disabled', true);
            $('#wizard-validation-form').hide();
            $('#status-label').text('Not In Use');
            $('#status-toggle').prop('disabled', true);
            $('#status-toggle').prop('checked', false);
        } else {
            $('#status-label').text('In Use');
            $('#wizard-validation-form').show();
            $('#wizard-validation-form input, #wizard-validation-form textarea, #edit-inventory').prop('disabled', false);
            $('#status-toggle').prop('disabled', false);
            $('#status-toggle').prop('checked', true);
        }
    }
    handleStatus('In Use');

    const statusToggle = document.getElementById('status-toggle');
    const statusLabel = document.getElementById('status-label');
    statusToggle.addEventListener('change', function () {
        if (this.checked) {
            statusLabel.textContent = 'In Use';
        } else {
            statusLabel.textContent = 'Not In Use';
        }
    });
    $("#calibrated_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
    });
    $("#next_calibration_date").flatpickr({
        dateFormat: "d-m-Y",
    });

    $(document).on('click', '#edit-inventory', function () {
        var calibratedDate = $('#calibrated_date').val();
        var nextCalibrationDate = $('#next_calibration_date').val();
        var comments = $('#comments').val();
        var status = statusToggle.checked ? 'active' : 'inactive';
        var corporate_inventory_id = $('#corporate_inventory_id').val();
        var formIsValid = true;
        function containsSpecialCharacters(value) {
            var regex = /[^a-zA-Z0-9 ]/;
            return regex.test(value);
        }
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        if (calibratedDate.trim() === '') {
            formIsValid = false;
            $('#calibrated_date').addClass('is-invalid');
            $('#calibrated_date').after('<div class="invalid-feedback">The Calibration Date is required.</div>');
        }
        if (nextCalibrationDate.trim() === '') {
            formIsValid = false;
            $('#next_calibration_date').addClass('is-invalid');
            $('#next_calibration_date').after('<div class="invalid-feedback">The Next Calibration Date is required.</div>');
        }
        if (comments.trim() === '') {
            formIsValid = false;
            $('#comments').addClass('is-invalid');
            $('#comments').after('<div class="invalid-feedback">The Comments are required.</div>');
        } else if (containsSpecialCharacters(comments)) {
            formIsValid = false;
            $('#comments').addClass('is-invalid');
            $('#comments').after('<div class="invalid-feedback">Comments should not contain special characters.</div>');
        }
        if (formIsValid) {
            var formData = {
                calibrated_date: calibratedDate,
                next_calibration_date: nextCalibrationDate,
                comments: comments,
                status: status
            };
            apiRequest({
                url: "/others/update/" + corporate_inventory_id,
                method: "POST",
                data: formData,
                onSuccess: function (response) {
                    console.log(response);
                    if (response.success === true) {
                        showToast("success", "Inventory updated successfully!");
                        window.location.href = 'https://login-users.hygeiaes.com/others/inventory';
                    } else if (response.success === false) {
                        alert('Error.');
                    } else {
                        alert('Unexpected response from the server.');
                    }
                },
                onError: function (error) {
                    console.error('An error occurred: ' + error);
                    alert('An error occurred while updating the inventory.');
                }
            });

        }
    });
});
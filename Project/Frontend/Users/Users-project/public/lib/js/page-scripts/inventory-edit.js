$(document).ready(function() {
    const url = window.location.href;
    const inventoryId = url.split('/').pop(); // Extract the last part of the URL (ID)

    // Fetch calibration history data from the API using the extracted ID
    fetch(`/others/inventory-history/${inventoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.calibration_history) {
                renderCalibrationHistory(data.calibration_history);
                // Check the latest calibration status and handle accordingly
                const latestStatus = data.calibration_history.length > 0 ? 
                                     data.calibration_history[data.calibration_history.length - 1].in_use ? 'In Use' : 'Not In Use' 
                                     : 'In Use';
                handleStatus(latestStatus); // Pass the latest calibration status to handle
            } else {
                console.error('Calibration history not found');
            }
        })
        .catch(error => {
            console.error('Error fetching calibration history:', error);
        });

    // Render the calibration history into a table
    function renderCalibrationHistory(calibrationHistory) {
        const historyList = $('#calibration-history-list');
        historyList.empty(); // Clear any existing history

        if (calibrationHistory.length > 0) {
            calibrationHistory.forEach(function(item) {
                const status = item.in_use ? 'In Use' : 'Not In Use'; // Display status as 'In Use' or 'Not In Use'
                const row = `<tr>
                                <td>${moment(item.calibrated_date).format('DD-MM-YYYY')}</td>
                                <td>${item.calibration_comments}</td>
                                <td>${status}</td>
                                <td>${moment(item.updated_at).format('DD-MM-YYYY HH:mm:ss')}</td>
                            </tr>`;
                historyList.append(row); // Append the new row to the table body
            });
        } else {
            historyList.append('<tr><td colspan="4" class="text-center">No calibration history available</td></tr>');
        }
    }

    // Function to handle form visibility and input disabling based on the calibration status
    function handleStatus(status) {
    if (status === 'Not In Use') {
        // Disable form elements if the latest calibration status is 'Not In Use'
        $('#wizard-validation-form input, #wizard-validation-form textarea, #edit-inventory').prop('disabled', true); // Disable all inputs and buttons
        $('#wizard-validation-form').hide(); // Optionally hide the form
        $('#status-label').text('Not In Use'); // Set the label to 'Not In Use'
        $('#status-toggle').prop('disabled', true); // Disable the status toggle switch
        $('#status-toggle').prop('checked', false); // Make sure toggle is off
    } else {
        // Ensure form remains visible and editable if status is 'In Use'
        $('#status-label').text('In Use'); // Otherwise, set the status to 'In Use'
        $('#wizard-validation-form').show(); // Show the form if status is 'In Use'
        $('#wizard-validation-form input, #wizard-validation-form textarea, #edit-inventory').prop('disabled', false); // Enable inputs
        $('#status-toggle').prop('disabled', false); // Enable the toggle switch
        $('#status-toggle').prop('checked', true); // Ensure toggle is checked
    }
}

// Assuming the default status is 'In Use' when page loads, initialize the toggle switch
$(document).ready(function() {
    handleStatus('In Use'); // Set default state as 'In Use' on page load
});


    // Toggle Status Label based on the checkbox
    const statusToggle = document.getElementById('status-toggle');
    const statusLabel = document.getElementById('status-label');
    statusToggle.addEventListener('change', function() {
        if (this.checked) {
            statusLabel.textContent = 'In Use';  // When checked, show "In Use"
        } else {
            statusLabel.textContent = 'Not In Use';  // When unchecked, show "Not In Use"
        }
    });

    // Initialize flatpickr for dates
    $("#calibrated_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today", 
    });
    $("#next_calibration_date").flatpickr({
        dateFormat: "d-m-Y",
    });

    var csrfToken = $('meta[name="csrf-token"]').attr('content');        
    $('#corporate_inventory_id').val("{{ $inventory['corporate_inventory_id'] ?? '' }}");
    $('#equipment_name_display').text("{{ $inventory['equipment_name'] ?? '' }} ");
    $('#equipment_code_display').text("{{ $inventory['equipment_code'] ?? '' }} ");
    $('#equipment_cost_display').text("Rs. " + ({{ $inventory['equipment_cost'] ?? '0' }}).toLocaleString());
    $('#manufacturers_display').text("{{ $inventory['manufacturers'] ?? '' }}");   
    $('#vendors_display').text("{{ $inventory['vendors'] ?? '' }}");
    $('#purchase_order_display').text("{{ $inventory['purchase_order'] ?? '' }}");
    $('#manufacture_date_display').text(moment("{{ $inventory['manufacture_date'] ?? '' }}").format('DD-MM-YYYY'));
    $('#purchase_date_display').text(moment("{{ $inventory['date'] ?? '' }}").format('D-M-Y'));

    // Update inventory
    $(document).on('click', '#edit-inventory', function() {    
        var calibratedDate = $('#calibrated_date').val();
        var nextCalibrationDate = $('#next_calibration_date').val();
        var comments = $('#comments').val();
        var status = statusToggle.checked ? 'active' : 'inactive';  // Determine status based on checkbox
        var corporate_inventory_id = $('#corporate_inventory_id').val();
        var formIsValid = true;

        // Helper function to check special characters
        function containsSpecialCharacters(value) {
            var regex = /[^a-zA-Z0-9 ]/; // Accepts only alphanumeric characters and spaces
            return regex.test(value);
        }

        // Remove existing invalid feedback messages
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();          

        // Validate Calibration Date
        if (calibratedDate.trim() === '') {
            formIsValid = false;
            $('#calibrated_date').addClass('is-invalid');
            $('#calibrated_date').after('<div class="invalid-feedback">The Calibration Date is required.</div>');
        }

        // Validate Next Calibration Date
        if (nextCalibrationDate.trim() === '') {
            formIsValid = false;
            $('#next_calibration_date').addClass('is-invalid');
            $('#next_calibration_date').after('<div class="invalid-feedback">The Next Calibration Date is required.</div>');
        }

        // Validate Comments (not empty or special characters)
        if (comments.trim() === '') {
            formIsValid = false;
            $('#comments').addClass('is-invalid');
            $('#comments').after('<div class="invalid-feedback">The Comments are required.</div>');
        } else if (containsSpecialCharacters(comments)) {
            formIsValid = false;
            $('#comments').addClass('is-invalid');
            $('#comments').after('<div class="invalid-feedback">Comments should not contain special characters.</div>');
        }

        // If all fields are valid, proceed with the request
        if (formIsValid) {
            var formData = {
                _token: csrfToken,
                calibrated_date: calibratedDate,
                next_calibration_date: nextCalibrationDate,
                comments: comments,
                status: status
            };

            // Send data via AJAX to backend
            $.ajax({
                url: "/others/update/" + corporate_inventory_id, // The route for updating data
                method: 'POST',
                data: formData,
                success: function(response) {
                    console.log(response); // Log the response
                    if (response.success === true) { // Only treat as success if true
                        showToast("success", "Inventory updated successfully!");
                        // Redirect to the specified URL after successful submission
                        window.location.href = 'https://login-users.hygeiaes.com/others/inventory';
                    } else if (response.success === false) {
                        alert('Error.');
                    } else {
                        alert('Unexpected response from the server.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('An error occurred: ' + error);
                    alert('An error occurred while updating the inventory.');
                }
            });
        }
    });
});


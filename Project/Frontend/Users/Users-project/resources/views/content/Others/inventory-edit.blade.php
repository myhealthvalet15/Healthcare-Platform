@extends('layouts/layoutMaster')

@section('title', 'Edit Inventory - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss','resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite(['resources/assets/js/form-wizard-numbered.js', 'resources/assets/js/form-wizard-validation.js'])
@endsection
<!-- Include jQuery from CDN (Content Delivery Network) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
 #inventory-display {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Creates 3 equal columns */
    gap: 20px; /* Spacing between grid items */
    padding: 20px;
}



.inventory-item strong {
    display: block;
    margin-bottom: 5px; /* Space between label and value */
    color: #333;
}

@media (max-width: 768px) {
    #inventory-display {
        grid-template-columns: repeat(2, 1fr); /* 2 columns on smaller screens */
    }
}

@media (max-width: 480px) {
    #inventory-display {
        grid-template-columns: 1fr; /* 1 column on very small screens */
    }
}

/* Toggle Switch Styles */

/* Style for the switch toggle */
.switch input:checked + .slider {
    background-color: #4caf50; /* Green when checked */
}

/* Optional: additional styles to make the toggle switch look nice */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    border-radius: 50%;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
}

input:checked + .slider:before {
    transform: translateX(26px); /* Moves the knob to the right */
}

input:checked + .slider {
    background-color: #4caf50; /* Green background when checked */
}

</style>
@section('content')
    <!-- Validation Wizard -->
    <div class="col-12 mb-6">
        <div id="wizard-validation" class="bs-stepper mt-2">            
            <div class="bs-stepper-content" style="display:block;">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" class="btn btn-primary" id="back-to-list" onclick="window.location.href='/others/inventory'" style="margin-right: 20px;">Back to Inventory</button>
            </div>
                <div id="inventory-display" class="inventory-grid">
                <div class="inventory-item">
                    <strong>Equipment Name:</strong> <span id="equipment_name_display"></span>
                </div>
                <div class="inventory-item">
                    <strong>Equipment Code:</strong> <span id="equipment_code_display"></span>
                </div>
                <div class="inventory-item">
                    <strong>Equipment Cost:</strong> <span id="equipment_cost_display"></span>
                </div>
                <div class="inventory-item">
                    <strong>Manufacturer Name:</strong> <span id="manufacturers_display"></span>
                </div>
                <div class="inventory-item">
                    <strong>Manufacture Date:</strong> <span id="manufacture_date_display"></span>
                </div>
                <div class="inventory-item">
                    <strong>Vendor:</strong> <span id="vendors_display"></span>
                </div>
                <div class="inventory-item">
                    <strong>Purchase Date:</strong> <span id="purchase_date_display"></span>
                </div>
                <div class="inventory-item">
                    <strong>Purchase Order:</strong> <span id="purchase_order_display"></span>
                </div>
   
</div>
<form id="wizard-validation-form" method="post">
    <!-- Account Details -->
    <div id="account-details-validation" class="content" style="display:block;">
        <div class="row g-6">
            <!-- Calibration Date -->
            <div class="col-sm-2" style="margin-left:10px;">
                <label for="calibrated_date" class="form-label" required>Calibration Date</label>
                <input type="date" id="calibrated_date" class="form-control">
            </div>

            <!-- Comment Section -->
            <div class="col-sm-3" style="margin-left: 10px;">
                <label for="comments" class="form-label">Comments</label>
                <textarea id="comments" class="form-control" rows="4" style="height:10px;" placeholder="Enter your comments here" style="width: 100%;"></textarea>
            </div>

            <!-- Next Calibration Date -->
            <div class="col-sm-2" style="margin-left: 10px;">
                <label for="next_calibration_date" class="form-label" required>Next Calibration Date</label>
                <input type="date" id="next_calibration_date" class="form-control">
            </div>

            <!-- Status Toggle -->
            <div class="col-sm-3" style="margin-left: 20px; display: flex; align-items: center;">
                <label for="status" class="form-label">Usage Status</label><br>
                <label class="switch" style="margin-left: 10px;">
                    <input type="checkbox" id="status-toggle">
                    <span class="slider round"></span>
                </label>
                <span id="status-label" style="margin-left: 10px;">In Use</span>
            </div>

            <input type="hidden" id="corporate_inventory_id">

            <!-- Save and Cancel Buttons -->
            <div class="col-sm-12" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-primary" id="edit-inventory" style="margin-left: 10px;">Save</button>
                <button type="reset" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas" style="margin-left: 10px;">Cancel</button>
            </div>
        </div>
        <br /><br />
    </div>
</form>

<table id="calibration-history-table" class="table table-bordered">
<thead>
    
        <tr class="advance-search mt-3"><th colspan="9" style="background-color:rgb(107, 27, 199);" rowspan="1">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Text on the left side -->
                            <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">Calibration History</span>
                         </th></tr>
        
    </thead>
    <thead>
        <tr>
            <th>Calibration Date</th>
            <th>Comments</th>
            <th>Status</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody id="calibration-history-list">
        <!-- Calibration History entries will be added here -->
    </tbody>
</table>

            </div>
        </div>
    </div>
    <!-- /Validation Wizard -->
    

    </div>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script> 
    <script>
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

</script>


@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">

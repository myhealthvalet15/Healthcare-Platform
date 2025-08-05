        const attachmentInput = document.getElementById('prescriptionAttachment');
        const thumbnailsContainer = document.getElementById('prescriptionThumbnails');

        attachmentInput.addEventListener('change', function () {
            thumbnailsContainer.innerHTML = ''; // Clear previous previews

            Array.from(this.files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();

                reader.onload = function (e) {
                    const thumbWrapper = document.createElement('div');
                    thumbWrapper.className = 'position-relative';
                    thumbWrapper.style.width = '100px';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.cursor = 'pointer';
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.addEventListener('click', () => {
                        document.getElementById('prescriptionModalImage').src = e.target.result;
                        const modal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
                        modal.show();
                    });

                    const deleteBtn = document.createElement('span');
                    deleteBtn.className = 'position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger';
                    deleteBtn.textContent = '×';
                    deleteBtn.style.cursor = 'pointer';
                    deleteBtn.title = 'Remove';
                    deleteBtn.onclick = () => {
                        thumbWrapper.remove();
                    };

                    thumbWrapper.appendChild(img);
                    thumbWrapper.appendChild(deleteBtn);
                    thumbnailsContainer.appendChild(thumbWrapper);
                };

                reader.readAsDataURL(file);
            });
        });
        function addRow1(isPrefilling = false, lastRowId = 0) {
            var container = document.querySelector('.prescription-inputs');

            // Determine the next row id based on whether we are pre-filling or not
            var rowCount = isPrefilling ? lastRowId + 1 : container.querySelectorAll('.prescription-row').length + 1;

            // Create a new row with a unique ID for the select dropdown
            var newRow = `
        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
            <!-- Drug Name Input -->
            <input type="hidden" name="rowid[]" value="${rowCount}">
            
            <div style="width: 31%;">      
                <div class="drug_name" title="drug_name">           
                    <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">    
                        <option value="">Select a Drug</option>
                        <!-- Add drug options dynamically -->
                    </select>
                </div>
            </div>
            <div style="width: 5%;"><span id="result_${rowCount}"></span></div>
            <!-- Days Input -->
            <div style="width: 5%;">
                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
            </div>

            <!-- Morning, Noon, Evening, Night Inputs -->
            <div style="width: 30%;margin-left:20px;">
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="morning[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
            </div>

            <!-- AF/BF Select -->
            <div style="width: 15%;text-align:center;">
                <select name="drugintakecondition[]" class="form-select">
                    <option value="">-Select-</option>
                    <option value="1">Before Food</option>
                    <option value="2">After Food</option>
                    <option value="3">With Food</option>
                    <option value="4">SOS</option>
                    <option value="5">Stat</option>
                </select>
            </div>

            <!-- Remarks Input -->
            <div style="width: 15%;">
                <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;">
            </div>

            <!-- Buttons for Add/Remove Rows -->
            <div style="width: 5%; text-align: center;">
                <div style="cursor: pointer;" class="margin-t-8" onclick="deleteRow(this)">
                    <i class="fa-sharp fa-solid fa-minus"></i> <!-- Replace plus with minus for subsequent rows -->
                </div>
            </div>
        </div>
    `;

            // Append the new row to the container
            container.insertAdjacentHTML('beforeend', newRow);

            // Initialize select2 for the newly added drug dropdown
            var newSelectElement = document.querySelector(`#drug_template_${rowCount}`);
            $(newSelectElement).select2();

            // Fetch and add drug options to the new select dropdown
            fetchDrugOptions(newSelectElement);
        }

        // Function to check if a selected drug has already been chosen in any row
        function checkDrugSelection() {
            console.log('AM here');
            var selectedDrugs = [];
            var selects = document.querySelectorAll('.hiddendrugname.select2'); // Get all select elements with the class 'hiddendrugname'

            // Convert NodeList to Array so you can use forEach
            Array.from(selects).forEach(function (select) {
                var selectedValue = select.value;

                // Check if a valid drug is selected and if it has already been selected
                if (selectedValue && selectedDrugs.includes(selectedValue)) {
                    // If the drug has already been selected, alert the user
                    alert('This drug has already been selected. Please choose another drug.');
                    select.value = '';  // Clear the selection
                    $(select).trigger('change');  // Trigger change to refresh the select2 UI
                } else if (selectedValue) {
                    // Add the drug to the list if it is valid and not already selected
                    selectedDrugs.push(selectedValue);
                }
            });
        }

        // Attach the change event to all select elements with the class 'hiddendrugname'
        $('.hiddendrugname').on('change', checkDrugSelection);

        function deleteRow(btn) {
            var row = btn.closest('.prescription-row');
            row.remove();
        }

        function fetchDrugOptions(selectElement) {
            $.ajax({
                url: "{{ route('getDrugTemplateDetails') }}",
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    var drugTypeMapping = {
                        1: "Capsule",
                        2: "Cream",
                        3: "Drops",
                        4: "Foam",
                        5: "Gel",
                        6: "Inhaler",
                        7: "Injection",
                        8: "Lotion",
                        9: "Ointment",
                        10: "Powder",
                        11: "Shampoo",
                        12: "Syringe",
                        13: "Syrup",
                        14: "Tablet",
                        15: "Toothpaste",
                        16: "Suspension",
                        17: "Spray",
                        18: "Test"
                    };

                    if (response && response.drugTemplate && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                        var defaultOption = document.createElement('option');
                        defaultOption.text = 'Select Drug';
                        defaultOption.value = '';
                        defaultOption.selected = true;
                        selectElement.appendChild(defaultOption);

                        response.drugTemplate.forEach(function (drug) {
                            var drugName = drug.drug_name || 'Unknown Drug';
                            var drugStrength = drug.drug_strength || 'Unknown Strength';
                            var drugType = drug.drug_type || 0;
                            var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                            var drugId = drug.drug_template_id;

                            var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                            var option = document.createElement('option');
                            option.value = drugId;
                            option.textContent = formattedDrug;
                            selectElement.appendChild(option);
                        });
                    } else {
                        console.error('No drug types available');
                        var noOption = document.createElement('option');
                        noOption.text = 'No drug types available';
                        noOption.value = '';
                        noOption.selected = true;
                        selectElement.appendChild(noOption);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching drug details: ' + error);
                }
            });
        }

        function addOutside() {
            var container = document.querySelector('.prescription-output');

            // Calculate the next row id
            var rowCount = container.querySelectorAll('.prescription-row-output').length;

            // Create a new row as a string
            var newRow = `
        <div class="prescription-row-output" style="display: flex; align-items: center; gap: 10px;">
            <!-- Drug Name Input -->
            <input type="hidden" name="rowid[]" value="${rowCount}">
            <div style="width: 27%;">      
                <div class="drug_name" title="drug_name">
                           <input type="text" name="drugname[]" placeholder="Drug Name - Type - Strength" style="padding:5px;width:290px;height:37px;border:1px solid #d1d0d4;">
                        </div>
            </div>
            <div style="width: 5%;">
           </div>
            <!-- Days Input -->
            <div style="width: 5%;">
                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days"  style="width:65px;">
            </div>

            <!-- Morning, Noon, Evening, Night Inputs -->
            <div style="width: 30%;margin-left:20px;">
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="morning" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
            </div>

            <!-- AF/BF Select -->
            <div style="width: 15%;text-align:center;">
                <select name="drugintakecondition[]" class="form-select">
                    <option value="">-Select-</option>
                    <option value="1">Before Food</option>
                    <option value="2">After Food</option>
                    <option value="3">With Food</option>
                    <option value="4">SOS</option>
                    <option value="5">Stat</option>
                </select>
            </div>

            <!-- Remarks Input -->
            <div style="width: 15%;">
                <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;">
            </div>

            <!-- Buttons for Add/Remove Rows -->
            <div style="width: 5%; text-align: center;">
                <div style="cursor: pointer;" class="margin-t-8" onclick="deleteRowoutpatient(this)">
                    <i class="fa-sharp fa-solid fa-minus"></i> <!-- Replace plus with minus for subsequent rows -->
                </div>
            </div>
        </div>
    `;

            // Append the new row to the container
            container.insertAdjacentHTML('beforeend', newRow);

            // Fetch the dynamic options for the drug select dropdown after appending
            var newSelectElement = document.querySelector(`#drug_template_${rowCount}`);

            // Apply Select2 to the new select element
            $(newSelectElement).select2();

            // Fetch and add drug options to the new select dropdown
            fetchDrugOptions(newSelectElement);
        }

        function deleteRowoutpatient(btn) {
            var row = btn.closest('.prescription-row-output');
            row.remove();
        }

        $(document).ready(function () {


            flatpickr("#prescription_date", {
                dateFormat: "d-m-Y",
                maxDate: "today", // disables future dates
                defaultDate: "today", // sets default to current date
            });

            function ValidNumber(event) {
                // Allow only numbers and control keys
                const key = event.key;
                return /\d/.test(key) || event.keyCode === 8 || event.keyCode === 9;
            }

            const currentUrl = window.location.href;

            // Use a regular expression to extract the emp_id (e.g., emp00003)
            const empIdMatch = currentUrl.match(/emp(\d{5})/);  // Adjust the regex if needed for different patterns

            // If a match is found, set it in the hidden field
            if (empIdMatch) {
                document.getElementById('emp_id').value = empIdMatch[0];
            } else {
                console.error('Employee ID not found in the URL.');
            }

            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Fetch drug types and ingredients on page load
            $.ajax({
                url: "{{ route('getDrugTemplateDetails') }}",
                method: 'GET',
                dataType: 'json', // Ensure response is treated as JSON
                success: function (response) {
                    console.log("Full Response:", response);

                    var drugTypeMapping = {
                        1: "Capsule",
                        2: "Cream",
                        3: "Drops",
                        4: "Foam",
                        5: "Gel",
                        6: "Inhaler",
                        7: "Injection",
                        8: "Lotion",
                        9: "Ointment",
                        10: "Powder",
                        11: "Shampoo",
                        12: "Syringe",
                        13: "Syrup",
                        14: "Tablet",
                        15: "Toothpaste",
                        16: "Suspension",
                        17: "Spray",
                        18: "Test"
                    };

                    if (response && response.drugTemplate && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                        var drugSelect = $('#drug_template_0');
                        drugSelect.append(new Option('Select Drug Type', '', true, true));

                        response.drugTemplate.forEach(function (drug) {
                            var drugName = drug.drug_name || 'Unknown Drug';
                            var drugStrength = drug.drug_strength || 'Unknown Strength';
                            var drugType = drug.drug_type || 0;
                            var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                            var drugId = drug.drug_template_id;

                            var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                            drugSelect.append(new Option(formattedDrug, drugId));
                        });

                        drugSelect.change(function () {
                            var selectedDrugId = $(this).val();
                            if (!selectedDrugId) {
                                $('#drug_details_section').hide();
                                return;
                            }

                            var selectedDrug = response.drugTemplate.find(function (drug) {
                                return drug.drug_template_id == selectedDrugId;
                            });

                        });
                    } else {
                        console.error('No drug types or ingredients found');
                        $('#drug_template_0').append(new Option('No drug types available', '', true, true));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching drug details: ' + error);
                }
            });

            $('#add_prescription').on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission immediately
                const prescriptionDate = document.getElementById('prescription_date').value;
                if (!prescriptionDate) {
                    alert("Please select a prescription date.");
                    return; // Prevent further execution if validation fails
                }
                const prescriptionTemplate = document.getElementById('prescriptionTemplate');
                const rows = document.querySelectorAll('.prescription-row');
                let isValid = true;
                rows.forEach((row, index) => {
                    const drugTemplate = row.querySelector('select[name="drug_template_id[]"]');
                    const duration = row.querySelector('input[name="duration[]"]');
                    const intakeCondition = row.querySelector('select[name="drugintakecondition[]"]');
                    const morning = row.querySelector('input[name="morning[]"]');
                    const afternoon = row.querySelector('input[name="afternoon[]"]');
                    const evening = row.querySelector('input[name="evening[]"]');
                    const night = row.querySelector('input[name="night[]"]');

                    // If drug_template_id is selected, validate duration and intake condition
                    if (drugTemplate.value === "" && prescriptionTemplate.value === "") {
                        alert("Please select a Drug Template.");
                        isValid = false;
                        return; // Stop further execution if prescriptionTemplate is empty and drugTemplate is not selected
                    }

                    // If drugTemplate has a value, validate duration and intakeCondition
                    if (drugTemplate.value !== "") {
                        // Check if duration is empty or invalid (must be a number)
                        if (!duration.value || isNaN(duration.value) || duration.value <= 0) {
                            alert(`Please enter a valid duration for drug template at row ${index + 1}.`);
                            isValid = false;
                        }

                        // Check if intakeCondition is selected
                        if (!intakeCondition.value) {
                            alert(`Please select an intake condition for drug template at row ${index + 1}.`);
                            isValid = false;
                        }
                        if (duration.value && (!morning.value && !afternoon.value && !evening.value && !night.value)) {
                            alert(`Please enter at least one time value (Morning, Afternoon, Evening, Night) for drug template at row ${index + 1}.`);
                            isValid = false;
                        }
                    }

                });

                // If validation fails, stop the process
                if (!isValid) {
                    return;
                }
                // Get the value of the input field
                let prescription_Date = $('input[name="prescription_date"]').val();
                let formattedDate = '';

                if (prescription_Date) {
                    let parts = prescription_Date.split('-'); // assuming input is in yyyy-mm-dd format
                    if (parts.length === 3) {
                        // Normalize to yyyy-mm-dd
                        let yyyy = parts[0];
                        let mm = parts[1].padStart(2, '0');
                        let dd = parts[2].padStart(2, '0');
                        formattedDate = `${yyyy}-${mm}-${dd}`;
                    }
                }

                const formData = {
                    _token: csrfToken,
                    prescriptionTemplate: $('#prescriptionTemplate').val(),
                    drugs: [], // filled as you already do
                    pharmacy: $('select[name="fav_pharmacy"]').val(),
                    shareWithPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
                    sendMailToPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
                    doctorNotes: $('textarea[name="doctorNotes"]').val(),
                    patientNotes: $('textarea[name="patientNotes"]').val(),
                    user_id: $('input[name="emp_id"]').val(),
                    prescription_date: formattedDate,
                    ohc: 1,
                    op_registry_id: opRegistryId,


                    prescription_attachments: prescriptionFilesBase64,
                };
                //console.log('Form Data:', formData); // Log the form data to check its structure

                // Loop through all prescription rows to get drug data
                $('.prescription-row').each(function (index, row) {
                    var rowData = {
                        drugTemplateId: $(row).find('select[name="drug_template_id[]"]').val(),
                        drugName: 0,
                        duration: $(row).find('input[name="duration[]"]').val(),
                        morning: $(row).find('input[name="morning[]"]').val(),
                        afternoon: $(row).find('input[name="afternoon[]"]').val(),
                        evening: $(row).find('input[name="evening[]"]').val(),
                        night: $(row).find('input[name="night[]"]').val(),
                        drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                        remarks: $(row).find('input[name="remarks[]"]').val(),
                        ohc: 1,
                        prescription_type: 'Type1'

                    };

                    // Push each drug row data to the drugs array
                    formData.drugs.push(rowData);
                });

                // Loop through prescription rows outside to get additional drug data


                let drugNameAvailable = false; // Flag to check if at least one drug name is available

                $('.prescription-row-output').each(function (index, row) {
                    var drugName = $(row).find('input[name="drugname[]"]').val();

                    // Check if drug name is available
                    if (drugName) {
                        drugNameAvailable = true; // Set the flag to true if drug name is found
                    }

                    var rowOutsideData = {
                        drugTemplateId: 0,
                        drugName: drugName, // Store the drug name
                        duration: $(row).find('input[name="duration[]"]').val(),
                        morning: $(row).find('input[name="morning[]"]').val(),
                        afternoon: $(row).find('input[name="afternoon[]"]').val(),
                        evening: $(row).find('input[name="evening[]"]').val(),
                        night: $(row).find('input[name="night[]"]').val(),
                        drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                        remarks: $(row).find('input[name="remarks[]"]').val(),
                        ohc: 2,
                        prescription_type: 'Type2',
                        op_registry_id: opRegistryId
                    };

                    // Only push the row data to drugs array if drugName is available
                    if (drugNameAvailable) {
                        formData.drugs.push(rowOutsideData);
                    }
                });
                // Submit the form via AJAX 
                const userType = "{{ session('user_type') }}";

                $.ajax({
                    url: "{{ route('store_EmployeePrescription') }}", // Route for storing prescription
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),

                    success: function (response) {

                        if (response.message) {
                            toastr.success('Prescription saved successfully!', 'Success');

                            setTimeout(function () {
                                if (userType === 'MasterUser') {
                                    window.location.href = 'https://login-users.hygeiaes.com/UserEmployee/userPrescription';
                                } else {
                                    window.location.href = 'https://login-users.hygeiaes.com/prescription/prescription-view';
                                }
                            }, 2000);
                        } else {
                            toastr.error('An error occurred while saving the prescription.', 'Error');
                        }
                    },

                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + ': ' + error);
                        alert('An error occurred while saving the prescription.');
                    }
                });
            });

            $('#addTest').on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission immediately
                const prescriptionDate = document.getElementById('prescription_date').value;
                if (!prescriptionDate) {
                    alert("Please select a prescription date.");
                    return; // Prevent further execution if validation fails
                }
                const prescriptionTemplate = document.getElementById('prescriptionTemplate');
                const rows = document.querySelectorAll('.prescription-row');
                let isValid = true;
                rows.forEach((row, index) => {
                    const drugTemplate = row.querySelector('select[name="drug_template_id[]"]');
                    const duration = row.querySelector('input[name="duration[]"]');
                    const intakeCondition = row.querySelector('select[name="drugintakecondition[]"]');
                    const morning = row.querySelector('input[name="morning[]"]');
                    const afternoon = row.querySelector('input[name="afternoon[]"]');
                    const evening = row.querySelector('input[name="evening[]"]');
                    const night = row.querySelector('input[name="night[]"]');

                    // If drug_template_id is selected, validate duration and intake condition
                    if (drugTemplate.value === "" && prescriptionTemplate.value === "") {
                        alert("Please select a Drug Template.");
                        isValid = false;
                        return; // Stop further execution if prescriptionTemplate is empty and drugTemplate is not selected
                    }

                    // If drugTemplate has a value, validate duration and intakeCondition
                    if (drugTemplate.value !== "") {
                        // Check if duration is empty or invalid (must be a number)
                        if (!duration.value || isNaN(duration.value) || duration.value <= 0) {
                            alert(`Please enter a valid duration for drug template at row ${index + 1}.`);
                            isValid = false;
                        }

                        // Check if intakeCondition is selected
                        if (!intakeCondition.value) {
                            alert(`Please select an intake condition for drug template at row ${index + 1}.`);
                            isValid = false;
                        }
                        if (duration.value && (!morning.value && !afternoon.value && !evening.value && !night.value)) {
                            alert(`Please enter at least one time value (Morning, Afternoon, Evening, Night) for drug template at row ${index + 1}.`);
                            isValid = false;
                        }
                    }

                });

                // If validation fails, stop the process
                if (!isValid) {
                    return;
                }
                // Get the value of the input field
                var prescription_Date = $('input[name="prescription_date"]').val();

                // Convert it to a Date object (assuming the input is in a standard date format, like mm/dd/yyyy)
                let dateObj = new Date(prescription_Date);

                // Format the date as yyyy-mm-dd
                let formattedDate = dateObj.toISOString().split('T')[0];

                // Log the formatted date or use it in your form submission
                console.log(formattedDate);

                var formData = {
                    _token: csrfToken,
                    prescriptionTemplate: $('#prescriptionTemplate').val(),
                    drugs: [],
                    pharmacy: $('select[name="fav_pharmacy"]').val(),
                    shareWithPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
                    sendMailToPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
                    doctorNotes: $('textarea[name="doctorNotes"]').val(),
                    patientNotes: $('textarea[name="patientNotes"]').val(),
                    user_id: $('input[name="emp_id"]').val(),
                    prescription_date: formattedDate,
                    ohc: 1,
                    op_registry_id: opRegistryId,
                    test: 1
                };

                // Loop through all prescription rows to get drug data
                $('.prescription-row').each(function (index, row) {
                    var rowData = {
                        drugTemplateId: $(row).find('select[name="drug_template_id[]"]').val(),
                        drugName: 0,
                        duration: $(row).find('input[name="duration[]"]').val(),
                        morning: $(row).find('input[name="morning[]"]').val(),
                        afternoon: $(row).find('input[name="afternoon[]"]').val(),
                        evening: $(row).find('input[name="evening[]"]').val(),
                        night: $(row).find('input[name="night[]"]').val(),
                        drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                        remarks: $(row).find('input[name="remarks[]"]').val(),
                        ohc: 1,
                        prescription_type: 'Type1'

                    };

                    // Push each drug row data to the drugs array
                    formData.drugs.push(rowData);
                });

                // Loop through prescription rows outside to get additional drug data


                let drugNameAvailable = false; // Flag to check if at least one drug name is available

                $('.prescription-row-output').each(function (index, row) {
                    var drugName = $(row).find('input[name="drugname[]"]').val();

                    // Check if drug name is available
                    if (drugName) {
                        drugNameAvailable = true; // Set the flag to true if drug name is found
                    }

                    var rowOutsideData = {
                        drugTemplateId: 0,
                        drugName: drugName, // Store the drug name
                        duration: $(row).find('input[name="duration[]"]').val(),
                        morning: $(row).find('input[name="morning[]"]').val(),
                        afternoon: $(row).find('input[name="afternoon[]"]').val(),
                        evening: $(row).find('input[name="evening[]"]').val(),
                        night: $(row).find('input[name="night[]"]').val(),
                        drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                        remarks: $(row).find('input[name="remarks[]"]').val(),
                        ohc: 2,
                        prescription_type: 'Type2',
                        op_registry_id: opRegistryId
                    };

                    // Only push the row data to drugs array if drugName is available
                    if (drugNameAvailable) {
                        formData.drugs.push(rowOutsideData);
                    }
                });
                // Submit the form via AJAX
                $.ajax({
                    url: "{{ route('store_EmployeePrescription') }}", // Route for storing prescription
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.message) {
                            console.log(response);
                            toastr.success('Prescription saved successfully!', 'Success');

                            // Build the dynamic URL using the returned prescription_id and employee_id
                            var redirectUrl = 'https://login-users.hygeiaes.com/ohc/health-registry/add-test/' + response.employee_id + '/prescription/' +
                                response.prescription_id

                            // Redirect after a short delay to show the toastr notification
                            setTimeout(function () {
                                window.location.href = redirectUrl;
                            }, 2000);
                        } else {
                            toastr.error('An error occurred while saving the prescription.', 'Error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + ': ' + error);
                        alert('An error occurred while saving the prescription.');
                    }
                });

            });
        });
        $(document).ready(function () {
            // Fetch drug template details on page load
            $.ajax({
                url: "{{ route('getDrugTemplateDetails') }}",
                method: 'GET',
                dataType: 'json', // Ensure response is treated as JSON
                success: function (response) {
                    console.log("Full Response:", response);

                    var drugTypeMapping = {
                        1: "Capsule",
                        2: "Cream",
                        3: "Drops",
                        4: "Foam",
                        5: "Gel",
                        6: "Inhaler",
                        7: "Injection",
                        8: "Lotion",
                        9: "Ointment",
                        10: "Powder",
                        11: "Shampoo",
                        12: "Syringe",
                        13: "Syrup",
                        14: "Tablet",
                        15: "Toothpaste",
                        16: "Suspension",
                        17: "Spray",
                        18: "Test"
                    };

                    // Fetch prescription data when template is selected
                    $(document).on('change', '#prescriptionTemplate', function () {
                        var prescriptionTemplateId = $(this).val();

                        if (prescriptionTemplateId) {
                            $.ajax({
                                url: `/prescription/prescription-editById/${prescriptionTemplateId}`,
                                method: 'GET',
                                dataType: 'json',
                                success: function (data) {
                                    console.log("Prefilling prescription data:", data);

                                    if (data.length > 0) {
                                        // Clear existing rows
                                        $('.prescription-inputs').empty();
                                        var headerRow = `
                        <div class="prescription-header" style="display: flex; justify-content: space-between;">
                            <div style="width: 23%;">Drug Name - Type - Strength</div>
                            <div style="width: 8%;">Available</div>
                            <div style="width: 5%;">Days</div>
                            <div style="width: 30%;">
                                <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                    <img src="">
                                </div>
                                <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                    <img src="/assets/img/prescription-icons/noon.png">
                                </div>
                                <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                    <img src="https://www.hygeiaes.co/img/Evening.png">
                                </div>
                                <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                    <img src="https://www.hygeiaes.co/img/Night.png">
                                </div>
                            </div>
                            <div style="width: 15%;">AF/BF</div>
                            <div style="width: 15%;">Remarks</div>
                        </div>
                    `;
                                        $('.prescription-inputs').append(headerRow);
                                        // Loop through each prescription data and create dynamic rows
                                        data.forEach((prescriptionData, index) => {
                                            var rowCount = index + 1;
                                            var isFirstRow = (rowCount === 1); // Check if this is the first row
                                            var addButtonIcon = isFirstRow ? 'fa-square-plus' : 'fa-minus';  // Show "+" for the first row and "-" for others

                                            var newRow = `
                                    
                                        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
                                            <!-- Drug Name Input -->
                                            <input type="hidden" name="rowid[]" value="${rowCount}">
                                            <div style="width: 31%;">      
                                                <div class="drug_name" title="drug_name">           
                                                    <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">    
                                                        <option value="">Select a Drug</option>
                                                        <!-- Drug options will be populated here -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div style="width: 5%;"><span id="result_${rowCount}"></div>
                                            <!-- Days Input -->
                                            <div style="width: 5%;">
                                                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;" value="${prescriptionData.intake_days}">
                                            </div>

                                            <!-- Morning, Noon, Evening, Night Inputs -->
                                            <div style="width: 30%;margin-left:20px;">
                                                <div style="float:left;width: 60px;">
                                                    <input type="text" maxlength="2" name="morning[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.morning}">
                                                </div>
                                                <div style="float:left;width: 60px;">
                                                    <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.afternoon}">
                                                </div>
                                                <div style="float:left;width: 60px;">
                                                    <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.evening}">
                                                </div>
                                                <div style="float:left;width: 60px;">
                                                    <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.night}">
                                                </div>
                                            </div>

                                            <!-- AF/BF Select -->
                                            <div style="width: 15%;text-align:center;">
                                                <select name="drugintakecondition[]" class="form-select">
                                                    <option value="">-Select-</option>
                                                    <option value="1" ${prescriptionData.intake_condition == 1 ? 'selected' : ''}>Before Food</option>
                                                    <option value="2" ${prescriptionData.intake_condition == 2 ? 'selected' : ''}>After Food</option>
                                                    <option value="3" ${prescriptionData.intake_condition == 3 ? 'selected' : ''}>With Food</option>
                                                    <option value="4" ${prescriptionData.intake_condition == 4 ? 'selected' : ''}>SOS</option>
                                                    <option value="5" ${prescriptionData.intake_condition == 5 ? 'selected' : ''}>Stat</option>
                                                </select>
                                            </div>

                                            <!-- Remarks Input -->
                                            <div style="width: 15%;">
                                                <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;" value="${prescriptionData.remarks}">
                                            </div>

                                            <!-- Buttons for Add/Remove Rows -->
                                            <div style="width: 5%; text-align: center;">
                                                <div style="cursor: pointer;" class="margin-t-8" onclick="addRow1()">
                                                    <i class="fa-sharp fa-solid ${addButtonIcon}"></i>
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                            // Append the new row
                                            $('.prescription-inputs').append(newRow);

                                            // Initialize select2 for the newly added drug dropdown
                                            var drugSelect = $('#drug_template_' + rowCount);
                                            drugSelect.select2();

                                            // Prefill the drug name dropdown based on drug template list
                                            var drugTemplateList = response.drugTemplate; // Assuming this is the list fetched via AJAX

                                            drugTemplateList.forEach(function (drug) {
                                                var drugName = drug.drug_name || 'Unknown Drug';
                                                var drugStrength = drug.drug_strength || 'Unknown Strength';
                                                var drugType = drug.drug_type || 0;
                                                var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                                                var drugId = drug.drug_template_id;

                                                var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                                                drugSelect.append(new Option(formattedDrug, drugId));
                                            });

                                            // After appending the options, set the correct value
                                            drugSelect.val(prescriptionData.drug_template_id).trigger('change');
                                        });
                                    } else {
                                        console.error('No prescription data available for this template');
                                        alert('No data found for this template');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error fetching prescription data: ' + error);
                                }
                            });
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching drug details: ' + error);
                }
            });
            $(document).on('change', '.hiddendrugname', function () {
                var drugTemplateId = $(this).val();
                var index = $(this).attr('id').split('_')[2];  // Get the index number from the id (e.g., 0, 1, 2)

                console.log("Drug Template ID:", drugTemplateId);
                console.log("Index:", index);

                if (drugTemplateId) {
                    var isDuplicate = false;

                    // Check if the selected drug is already chosen in another select element
                    $('.hiddendrugname').each(function () {
                        var selectedDrug = $(this).val();
                        var currentIndex = $(this).attr('id').split('_')[2];  // Get the index number of this select element

                        // Compare selected drugs, skip comparison if it's the same element (the one that triggered the change event)
                        if (selectedDrug && selectedDrug === drugTemplateId && currentIndex !== index) {
                            isDuplicate = true;  // Mark as duplicate if found
                        }
                    });

                    if (isDuplicate) {
                        // Alert the user that the drug has already been selected
                        alert('This drug has already been selected. Please choose another drug.');
                        $(this).val('');  // Clear the current selection
                        $(this).trigger('change');  // Trigger change to refresh the select2 UI
                        $('#result_' + index).text('');  // Clear availability result
                        return;  // Exit the function early
                    }

                    // If no duplicates, proceed to fetch stock availability
                    $.ajax({
                        url: `/prescription/getStockByDrugId/${drugTemplateId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            console.log("Response Data:", data); // Log the full response

                            if (data && data.data && typeof data.data.total_current_availability !== 'undefined') {
                                $('#result_' + index).text(data.data.total_current_availability); // Set the availability
                            } else {
                                console.log("Availability is undefined or missing.");
                                $('#result_' + index).text('Not Available'); // Show 'Not Available' if the data is missing
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log("Error fetching stock data:", error);
                            $('#result_' + index).text('Error');
                        }
                    });
                } else {
                    // Clear the result if no drug is selected
                    $('#result_' + index).text('');
                }
            });



            // Function to delete a row
            function deleteRow(row) {
                // Ensure you don't delete the last row if only one row exists
                if ($('.prescription-row').length > 1) {
                    $(row).closest('.prescription-row').remove();
                }
            }

        });

        function resetForm() {
            location.reload(); // Reloads the page
        }

        let prescriptionFilesBase64 = [];

                    document.getElementById('prescriptionAttachment').addEventListener('change', async function () {
                        const container = document.getElementById('prescriptionThumbnails');
                        container.innerHTML = '';
                        prescriptionFilesBase64 = []; // Reset stored images

                        const files = Array.from(this.files);
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                        const maxSize = 1024 * 1024; // 1MB per file

                        for (const file of files) {
                            if (!allowedTypes.includes(file.type)) {
                                showToast('error', 'Invalid File', `${file.name} is not a valid image.`);
                                continue;
                            }

                            if (file.size > maxSize) {
                                showToast('error', 'File Too Large', `${file.name} exceeds 1MB.`);
                                continue;
                            }

                            const base64 = await fileToBase64(file);
                            prescriptionFilesBase64.push(base64);

                            const img = document.createElement('img');
                            img.src = base64;
                            img.className = 'img-thumbnail';
                            img.style.width = '100px';
                            img.style.height = '100px';
                            img.style.objectFit = 'cover';
                            container.appendChild(img);
                        }

                        function fileToBase64(file) {
                            return new Promise((resolve, reject) => {
                                const reader = new FileReader();
                                reader.onload = () => resolve(reader.result);
                                reader.onerror = reject;
                                reader.readAsDataURL(file);
                            });
                        }
                    });
              
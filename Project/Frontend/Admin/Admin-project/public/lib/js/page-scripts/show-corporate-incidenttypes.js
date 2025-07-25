document.addEventListener('DOMContentLoaded', function () {
    fetchIncidentTypes();
});

function fetchIncidentTypes() {
    const row = document.getElementById('incidentTypesRow');
    const inputFields = document.getElementById('inputFields');
    const incidentText = document.getElementById('incidentText');
    const colorPicker = document.getElementById('colorPicker');
    const colorCode = document.getElementById('colorCode');

    fetch('/corporate/getAllIncidentTypes')
        .then(res => res.json())
        .then(response => {
            if (response.result && Array.isArray(response.data)) {
                response.data.forEach(item => {
                    const col = document.createElement('div');
                    col.className = 'col-auto';

                    col.innerHTML = `
                        <div class="form-check">
                            <input class="form-check-input incident-checkbox" 
                                   type="checkbox" 
                                   name="incidentType" 
                                   id="incident-${item.incident_type_id}" 
                                   data-name="${item.incident_type_name}">
                            <label class="form-check-label" for="incident-${item.incident_type_id}">
                                ${item.incident_type_name}
                            </label>
                        </div>
                    `;

                    row.appendChild(col);
                });

                // Add event listeners to checkboxes
                document.querySelectorAll('.incident-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        // Uncheck other checkboxes
                        document.querySelectorAll('.incident-checkbox').forEach(cb => {
                            if (cb !== this) cb.checked = false;
                        });

                        if (this.checked) {
                            inputFields.style.display = 'block';
                            incidentText.value = this.dataset.name;
                        } else {
                            inputFields.style.display = 'none';
                            incidentText.value = '';
                            colorCode.value = '';
                        }
                    });
                });

                // Update color code field
                colorPicker.addEventListener('input', () => {
                    colorCode.value = colorPicker.value;
                });
            }
        });
}


function saveIncidentTypes() {
    const selectedCheckbox = document.querySelector('.incident-checkbox:checked');

    if (!selectedCheckbox) {
        Swal.fire('Error', 'Please select an incident type.', 'error');
        return;
    }

    const incidentTypeId = selectedCheckbox.dataset.id;
    const incidentTypeName = selectedCheckbox.dataset.name;
    console.log("xxxx",incidentTypeId);

    const colorPicker = document.getElementById('colorPicker'); 
    const colorCodeInput = document.getElementById('colorCode');

    if (colorPicker && colorCodeInput) {
        colorCodeInput.value = colorPicker.value;
    }

    const injuryText = document.getElementById('injuryText')?.value.trim();
    const colorCode = colorCodeInput?.value?.trim();

    if (!injuryText || !colorCode) {
        Swal.fire('Validation Error', 'All fields are required.', 'warning');
        return;
    }

    const corporateId = 'MCBoAmzVFigh';  

    const incident_types = [
        {
            id: incidentTypeId,
            injury_color_types: {
                [injuryText]: colorCode 
            }
        }
    ];

    apiRequest({
        url: "/corporate/assignIncidentTypes/" + corporateId,
        method: "POST",
        data: { incident_types },
        success: function (response) {
            Swal.fire('Success', 'Incident Types saved successfully!', 'success');
        },
        error: function (err) {
            const errorMsg = err.responseJSON?.errors?.incident_types?.[0] || 'Something went wrong!';
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

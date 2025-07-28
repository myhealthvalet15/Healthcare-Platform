document.addEventListener('DOMContentLoaded', function () {
    fetchIncidentTypes();
});
  let incidentTypeCount = 1; 
  const maxTypes = 5;

function fetchIncidentTypes() {
    const row = document.getElementById('incidentTypesRow');
    const inputFields = document.getElementById('inputFields');
    //const incidentText = document.getElementById('incidentText');

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
                                   data-id="${item.incident_type_id}" 
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
                           // incidentText.value = this.dataset.name;
                        } else {
                            inputFields.style.display = 'none';
                           // incidentText.value = '';
                        }
                    });
                });

               
            }
        });
}


  function incrementIncidentTypes() {
    if (incidentTypeCount >= maxTypes) {
      alert("You can add only up to 5 incident types.");
      return;
    }

    const addInputFields = document.getElementById('addinputFields');
    const div = document.createElement('div');
    div.className = 'row align-items-center mb-2';

    div.innerHTML = `
      <div class="col-md-3">
      <input type="text" class="form-control injury-text" placeholder="Injury Type Text">
    </div>
    <div class="col-md-1 text-center">
      <input type="color" class="form-control form-control-color color-picker" style="width: 40px; height: 40px; padding: 0;">
    </div>
    <div class="col-md-4">
      <input type="text" class="form-control color-code" placeholder="Color Code" readonly>
    </div>
    `;

    addInputFields.appendChild(div);
    incidentTypeCount++;
  }

document.addEventListener('input', function (e) {
  if (e.target.classList.contains('color-picker')) {
    const row = e.target.closest('.row');
    const colorCodeInput = row.querySelector('.color-code');
    if (colorCodeInput) {
      colorCodeInput.value = e.target.value;
    }
  }
});

function saveIncidentTypes() {
    const selectedCheckbox = document.querySelector('.incident-checkbox:checked');

    if (!selectedCheckbox) {
        Swal.fire('Error', 'Please select an incident type.', 'error');
        return;
    }

    const incidentTypeId = selectedCheckbox.dataset.id;
    const incidentTypeName = selectedCheckbox.dataset.name;

console.log(incidentTypeId);

    // const colorPicker = document.getElementById('colorPicker'); 
    // const colorCodeInput = document.getElementById('colorCode');

    // if (colorPicker && colorCodeInput) {
    //     colorCodeInput.value = colorPicker.value;
    // }

    // const injuryText = document.getElementById('injuryText')?.value.trim();
    // const colorCode = colorCodeInput?.value?.trim();

    // if (!injuryText || !colorCode) {
    //     Swal.fire('Validation Error', 'All fields are required.', 'warning');
    //     return;
    // }
 const injuryTexts = document.querySelectorAll('.injury-text');
  const colorPickers = document.querySelectorAll('.color-picker');
  const colorCodes = document.querySelectorAll('.color-code');

  const injury_color_types = {};

  for (let i = 0; i < injuryTexts.length; i++) {
    const text = injuryTexts[i].value.trim();
    const code = colorCodes[i].value.trim();

    if (text && code) {
    injury_color_types[text] = code;
  }
    
  }
    const corporateId = 'MCBoAmzVFigh';  

    const incident_types = [
        {
            id: incidentTypeId,
            injury_color_types: injury_color_types
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

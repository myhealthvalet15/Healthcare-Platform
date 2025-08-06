document.getElementById('vehicleType').addEventListener('change', function () {
    const ambulanceFields = document.getElementById('ambulanceFields');
    if (this.value === 'ambulance') {
        ambulanceFields.classList.remove('hidden');
    } else {
        ambulanceFields.classList.add('hidden');
    }
});
function formatTime(dateTime) {
    if (!dateTime) return '';
    const date = new Date(dateTime);
    return date.toISOString().substr(11, 5);
}
function populateAddedOutpatientData() {
    const fieldMapping = {
        167: 'vpTemperature_167',
        168: 'vpSystolic_168',
        169: 'vpDiastolic_169',
        170: 'vpPulseRate_170',
        171: 'vpRespiratory_171',
        172: 'vpSPO2_172',
        173: 'vpRandomGlucose_173',
    };
    prescribedTestData.forEach((data) => {
        let masterTestId = data.master_test_id;
        let testResult = data.test_results;
        if (fieldMapping[masterTestId]) {
            let fieldId = fieldMapping[masterTestId];
            let inputElement = document.getElementById(fieldId);
            if (inputElement) {
                inputElement.value = testResult;
            }
        }
    });
    var opRegistryTimes = employeeData.op_registry_datas?.op_registry_times || {};
    var opRegistry = employeeData.op_registry_datas?.op_registry || {};
    var prescribedTests = employeeData.op_registry_datas?.prescribed_test || {};
    var currentDateTime = new Date();
    var year = currentDateTime.getFullYear();
    var month = (currentDateTime.getMonth() + 1).toString().padStart(2, '0');
    var day = currentDateTime.getDate().toString().padStart(2, '0');
    var hours = currentDateTime.getHours().toString().padStart(2, '0');
    var minutes = currentDateTime.getMinutes().toString().padStart(2, '0');
    var formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    document.getElementById('reporting-datetime').value = formattedDateTime;
    var incidentDateTime = opRegistryTimes.incident_date_time ?? '';
    if (incidentDateTime) {
        document.getElementById('incident-datetime').value = formatDate(incidentDateTime);
    }
    document.getElementById('incident-datetime').disabled = true;
    var leaveFrom = opRegistryTimes.leave_from_date_time ?? '';
    if (leaveFrom) {
        document.getElementById('leave-from').value = formatDate(leaveFrom);
    }
    var leaveUpto = opRegistryTimes.leave_upto_date_time ?? '';
    if (leaveUpto) {
        document.getElementById('leave-upto').value = formatDate(leaveUpto);
    }
    var lostHours = opRegistryTimes.lost_hours;
    if (lostHours) {
        document.getElementById('lostHours').value = lostHours;
    }
    var outTime = opRegistryTimes.out_date_time;
    if (outTime) {
        document.getElementById('outTime').value = formatDate(outTime);
    }
    var doctorNotes = opRegistry.doctor_notes;
    if (doctorNotes) {
        document.getElementById('doctorNotes').value = doctorNotes;
    }
    var medicalHistory = opRegistry.past_medical_history;
    if (medicalHistory) {
        document.getElementById('medicalHistory').value = medicalHistory;
    }
    var shift = parseInt(opRegistry.shift ?? 4);
    if (shift) {
        document.getElementById('workShift').value = shift.toString();
    }
    var firstAidBy = opRegistry.first_aid_by ?? '';
    if (firstAidBy) {
        document.getElementById('firstAidBy').value = firstAidBy;
    }
    var movement_slip = opRegistry.movement_slip;
    if (movement_slip) {
        document.getElementById('movementSlip').checked = movement_slip;
    }
    var physiotherapy = opRegistry.physiotherapy;
    if (physiotherapy) {
        document.getElementById('physiotherapy').checked = physiotherapy;
    }
    var fitness_certificate = opRegistry.fitness_certificate;
    if (fitness_certificate) {
        document.getElementById('fitnessCert').checked = fitness_certificate;
    }
    var isOutPatientAdded = employeeData.isPrescriptionAdded;
    if (isOutPatientAdded) {
        $('#addPrescription').text('View Prescription');
        $('#addPrescription').removeClass('btn-warning');
        $('#addPrescription').addClass('btn-secondary');
    }
    var type_of_incident = opRegistry.type_of_incident;
    if (type_of_incident === 'medicalIllness' || type_of_incident === 'Medical Illness') {
        medicalFields.style.display = 'block';
        industrialFields.style.display = 'none';
        document.getElementById('incidentType').value = 'medicalIllness';
        selectValues('select2Primary_body_part', opRegistry.body_part);
        selectValues('select2Primary_symptoms', opRegistry.symptoms);
        selectValues('select2Primary_medical_system', opRegistry.medical_system);
        selectValues('select2Primary_diagnosis', opRegistry.diagnosis);
    } else if (type_of_incident === 'industrialAccident' || type_of_incident === 'Industrial Accident') {
        medicalFields.style.display = 'none';
        industrialFields.style.display = 'block';
        siteOfInjury.style.display = 'flex';
        document.getElementById('incidentType').value = 'industrialAccident';
        selectValues('select2Primary_body_part_IA', opRegistry.body_part);
        selectValues('select2Primary_nature_of_injury', opRegistry.nature_injury);
        selectValues('select2Primary_injury_mechanism', opRegistry.mechanism_injury);
        var description = opRegistry.description;
        if (description) {
            document.getElementById("injury_description").value = opRegistry.description;
        }
        if (opRegistry.injury_color_text) {
            const selectedValue = opRegistry.injury_color_text;
            const injuryColorRadios = document.querySelectorAll('input[name="injuryColor"]');
            injuryColorRadios.forEach(radio => {
                if (radio.value.toLowerCase() === selectedValue.toLowerCase()) {
                    radio.checked = true;
                }
            });
        }
        initializeCheckboxes();
        initializeSiteOfInjuryCheckboxes();
    } else if (type_of_incident === 'outsideAccident' || type_of_incident === 'Outside Accident') {
        medicalFields.style.display = 'none';
        industrialFields.style.display = 'block';
        siteOfInjury.style.display = 'none';
        document.getElementById('incidentType').value = 'outsideAccident';
        selectValues('select2Primary_body_part_IA', opRegistry.body_part);
        selectValues('select2Primary_nature_of_injury', opRegistry.nature_injury);
        selectValues('select2Primary_injury_mechanism', opRegistry.mechanism_injury);
        var description = opRegistry.description;
        if (description) {
            document.getElementById("injury_description").value = opRegistry.description;
        }
        if (opRegistry.injury_color_text) {
            const selectedValue = opRegistry.injury_color_text;
            const injuryColorRadios = document.querySelectorAll('input[name="injuryColor"]');
            injuryColorRadios.forEach(radio => {
                if (radio.value.toLowerCase() === selectedValue.toLowerCase()) {
                    radio.checked = true;
                }
            });
        }
        initializeCheckboxes();
        initializeSiteOfInjuryCheckboxes();
    }
    var doctorId = prescribedTests.doctor_id;
    if (doctorId > 0) {
        document.getElementById('doctorSelect').value = doctorId;
    }
    function initializeCheckboxes() {
        let bodySideObj;
        if (typeof opRegistry['body_side'] === 'string') {
            try {
                bodySideObj = JSON.parse(opRegistry['body_side']);
            } catch (error) {
                return;
            }
        } else {
            bodySideObj = opRegistry['body_side'];
        }
        if (bodySideObj && typeof bodySideObj === 'object') {
            document.getElementById('leftSide').checked = bodySideObj.left === true;
            document.getElementById('rightSide').checked = bodySideObj.right === true;
        } else {
            console.error("Could not get valid body_side data");
        }
    }
    function initializeSiteOfInjuryCheckboxes() {
        let siteOfInjuryObj;
        if (typeof opRegistry['site_of_injury'] === 'string') {
            try {
                siteOfInjuryObj = JSON.parse(opRegistry['site_of_injury']);
            } catch (error) {
                return;
            }
        } else {
            siteOfInjuryObj = opRegistry['site_of_injury'];
        }
        if (siteOfInjuryObj && typeof siteOfInjuryObj === 'object') {
            document.getElementById('shopFloor').checked = siteOfInjuryObj.shopFloor === true;
            document.getElementById('nonShopFloor').checked = siteOfInjuryObj.nonShopFloor === true;
        } else {
            console.error("Could not get valid site_of_injury data");
        }
    }
    function formatDate(dateTimeStr) {
        try {
            if (dateTimeStr.includes('.')) {
                dateTimeStr = dateTimeStr.split('.')[0];
            }
            let date = new Date(dateTimeStr);
            if (isNaN(date.getTime())) {
                return '';
            }
            let year = date.getFullYear();
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let day = date.getDate().toString().padStart(2, '0');
            let hours = date.getHours().toString().padStart(2, '0');
            let minutes = date.getMinutes().toString().padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        } catch (e) {
            console.error('Error formatting date:', e);
            return '';
        }
    }
};
function toggleIncidentFields() {
    const incidentType = document.getElementById('incidentType').value;
    const medicalFields = document.getElementById('medicalFields');
    const industrialFields = document.getElementById('industrialFields');
    const siteOfInjury = document.getElementById('siteOfInjury');
    if (incidentType === 'medicalIllness') {
        medicalFields.style.display = 'block';
        industrialFields.style.display = 'none';
    } else if (incidentType === 'industrialAccident') {
        medicalFields.style.display = 'none';
        industrialFields.style.display = 'block';
        siteOfInjury.style.display = 'flex';
    } else {
        medicalFields.style.display = 'none';
        industrialFields.style.display = 'block';
        siteOfInjury.style.display = 'none';
    }
}
function populateSelect(selectId, data) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return;
    selectElement.innerHTML = '';
    data.forEach((item) => {
        let option = document.createElement('option');
        option.value = item.op_component_id;
        option.textContent = item.op_component_name;
        selectElement.appendChild(option);
    });
    if ($(selectElement).hasClass('select2')) {
        $(selectElement).trigger('change');
    }
}
function selectValues(selectId, selectedValues = []) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return;
    const options = selectElement.options;
    for (let i = 0; i < options.length; i++) {
        if (selectedValues.includes(parseInt(options[i].value))) {
            options[i].selected = true;
        }
    }
    if ($(selectElement).hasClass('select2')) {
        $(selectElement).trigger('change');
    }
}
function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}
function validateFields(fields) {
    for (const [id, label] of Object.entries(fields)) {
        const value = document.getElementById(id)?.value.trim() || '';
        if (!value) {
            showToast('error', `Please fill ${label}`);
            return false;
        }
    }
    return true;
}
function toggleCheckbox(selectedId) {
    const checkboxes = document.querySelectorAll('.site-of-injury');
    checkboxes.forEach(checkbox => {
        if (checkbox.id !== selectedId) {
            checkbox.checked = false;
        }
    });
}
function handleIncidentType() {
    const incidentType = document.getElementById('incidentType').value;
    const doctorSelect = document.getElementById('doctorSelect').value;
    if (incidentType != "medicalIllness") {
        const injuryColor = document.querySelector('input[name="injury_color_text"]:checked');
        if (!injuryColor) {
            showToast('error', 'Please select Injury Color');
            return false;
        }
    }
    if (!incidentType) {
        showToast('error', 'Please select Incident Type');
        return false;
    }
    const doctor = {
        doctorId: doctorSelect,
        doctorName: $('#doctorSelect option:selected').text()
    };
    return true;
}
function handleObservations() {
    const observations = {
        doctorNotes: $('#doctorNotes').val(),
        medicalHistory: $('#medicalHistory').val(),
        referral: $('#referralSelect').val(),
        movementSlip: $('#movementSlip').is(':checked'),
        fitnessCert: $('#fitnessCert').is(':checked'),
        physiotherapy: $('#physiotherapy').is(':checked')
    };
    if ($('#referralSelect').val() === "OutsideReferral") {
        const hospitalName = document.getElementById("hospitalName").value;
        if (!hospitalName) {
            showToast('error', 'Please fill Hospital Name');
            return false;
        }
        const vehicleType = document.getElementById("vehicleType").value;
        if (!vehicleType) {
            showToast('error', 'Please select Vehicle Type');
            return false;
        }
        if (vehicleType === 'ambulance') {
            const driverName = document.getElementById("driverName").value;
            const ambulanceNumber = document.getElementById("ambulanceNumber").value;
            const odometerIn = document.getElementById("odometerIn").value;
            const odometerOut = document.getElementById("odometerOut").value;
            const timeIn = document.getElementById("timeIn").value;
            const timeOut = document.getElementById("timeOut").value;
            if (!driverName) {
                showToast('error', 'Please fill Driver Name');
                return false;
            }
            if (!ambulanceNumber) {
                showToast('error', 'Please fill Ambulance Number');
                return false;
            }
            if (!odometerIn) {
                showToast('error', 'Please fill Odometer In');
                return false;
            }
            if (!odometerOut) {
                showToast('error', 'Please fill Odometer Out');
                return false;
            }
            if (!timeIn) {
                showToast('error', 'Please fill Time In');
                return false;
            }
            if (!timeOut) {
                showToast('error', 'Please fill Time Out');
                return false;
            }
        }
    }
    return true;
}
function sendHealthRegistryData(close = false, onSuccessCallback = () => { }) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to submit the health registry data?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, submit it!"
    }).then((result) => {
        if (result.isConfirmed) {
            const healthRegistryData = {
                employeeId: $('#employeeId').val(),
                vitalParameters: {
                    vpTemperature_167: $('#vpTemperature_167').val(),
                    vpSystolic_168: $('#vpSystolic_168').val(),
                    vpDiastolic_169: $('#vpDiastolic_169').val(),
                    vpPulseRate_170: $('#vpPulseRate_170').val(),
                    vpRespiratory_171: $('#vpRespiratory_171').val(),
                    vpSPO2_172: $('#vpSPO2_172').val(),
                    vpRandomGlucose_173: $('#vpRandomGlucose_173').val()
                },
                incidentType: $('#incidentType').val(),
                incidentTypeId: incidentTypeId,
                observations: {
                    doctorNotes: $('#doctorNotes').val(),
                    medicalHistory: $('#medicalHistory').val(),
                    referral: $('#referralSelect').val(),
                    movementSlip: $('#movementSlip').is(':checked'),
                    fitnessCert: $('#fitnessCert').is(':checked'),
                    physiotherapy: $('#physiotherapy').is(':checked')
                },
                lostHours: {
                    leaveFrom: $('#leave-from').val(),
                    leaveUpto: $('#leave-upto').val(),
                    lostHours: $('#lostHours').val(),
                    outTime: $('#outTime').val()
                },
                close: close,
                workShift: $('#workShift').val(),
                firstAidBy: $('#firstAidBy').val(),
                reportingDateTime: $('#reporting-datetime').val(),
                incidentDateTime: $('#incident-datetime').val(),
                movementSlip: document.getElementById('movementSlip').checked ? 1 : 0,
                fitnessCert: document.getElementById('fitnessCert').checked ? 1 : 0,
                physiotherapy: document.getElementById('physiotherapy').checked ? 1 : 0
            };
            const doctorId = $('#doctorSelect').val();
            const doctorName = $('#doctorSelect option:selected').text();
            if (doctorId && doctorName !== "Select Doctor") {
                healthRegistryData.doctor = {
                    doctorId: doctorId,
                    doctorName: doctorName
                };
            }
            if (healthRegistryData.incidentType === "medicalIllness") {
                healthRegistryData.medicalFields = {
                    bodyPart: $('#select2Primary_body_part').val(),
                    symptoms: $('#select2Primary_symptoms').val(),
                    medicalSystem: $('#select2Primary_medical_system').val(),
                    diagnosis: $('#select2Primary_diagnosis').val(),
                    injuryColor: $('input[name="injury_color_text"]:checked').val(),
                };
            } else if (healthRegistryData.incidentType === "industrialAccident") {
                healthRegistryData.industrialFields = {
                    injuryColor: $('input[name="injury_color_text"]:checked').val(),
                    sideOfBody: {
                        left: $('#leftSide').is(':checked'),
                        right: $('#rightSide').is(':checked')
                    },
                    siteOfInjury: {
                        shopFloor: $('#shopFloor').is(':checked'),
                        nonShopFloor: $('#nonShopFloor').is(':checked')
                    },
                    natureOfInjury: $('#select2Primary_nature_of_injury').val(),
                    bodyPartIA: $('#select2Primary_body_part_IA').val(),
                    injuryMechanism: $('#select2Primary_injury_mechanism').val(),
                    description: $('#injury_description').val()
                };
            } else {
                healthRegistryData.industrialFields = {
                    injuryColor: $('input[name="injury_color_text"]:checked').val(),
                    sideOfBody: {
                        left: $('#leftSide').is(':checked'),
                        right: $('#rightSide').is(':checked')
                    },
                    natureOfInjury: $('#select2Primary_nature_of_injury').val(),
                    bodyPartIA: $('#select2Primary_body_part_IA').val(),
                    injuryMechanism: $('#select2Primary_injury_mechanism').val(),
                    description: $('#injury_description').val()
                };
            }
            let referral = $('#referralSelect').val();
            healthRegistryData.referral = referral;
            if (referral === 'OutsideReferral') {
                let hospitalName = document.getElementById("hospitalName").value;
                let esiScheme = document.getElementById("esiScheme").checked ? 1 : 0;
                let vehicleType = document.getElementById("vehicleType").value;
                healthRegistryData.hospitalDetails = {
                    hospitalName: hospitalName,
                    esiScheme: esiScheme,
                    vehicleType: vehicleType
                };
                if (vehicleType === 'ambulance') {
                    let driverName = document.getElementById("driverName").value;
                    let ambulanceNumber = document.getElementById("ambulanceNumber").value;
                    let accompaniedBy = document.getElementById("accompaniedBy").value;
                    let odometerIn = document.getElementById("odometerIn").value;
                    let odometerOut = document.getElementById("odometerOut").value;
                    let timeIn = document.getElementById("timeIn").value;
                    let timeOut = document.getElementById("timeOut").value;
                    healthRegistryData.hospitalDetails = {
                        driverName: driverName,
                        hospitalName: hospitalName,
                        esiScheme: esiScheme,
                        vehicleType: vehicleType,
                        ambulanceNumber: ambulanceNumber,
                        accompaniedBy: accompaniedBy,
                        odometerIn: odometerIn,
                        odometerOut: odometerOut,
                        timeIn: timeIn,
                        timeOut: timeOut
                    };
                }
            }
            const opRegistryId = employeeData.op_registry_datas?.op_registry?.op_registry_id;
            const editExistingOne = 1;
            healthRegistryData.editExistingOne = editExistingOne;
            const isFollowup = 1;
            healthRegistryData.isFollowup = isFollowup;
            apiRequest({
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/saveHealthRegistry/' + opRegistryId,
                method: 'POST',
                data: healthRegistryData,
                onSuccess: function (response) {
                    showToast('success', 'Success', 'Health registry saved successfully!');
                    onSuccessCallback(response.op_registry_id);
                },
                onError: function (error) {
                    console.error('Error saving health registry:', error);
                    showToast('error', 'Error', 'Failed to save health registry: ' + error);
                }
            });
        }
    });
}
function loadDataInParallel() {
    const spinnerLabel = document.getElementById('spinnerLabeltext');
    const spinner = document.getElementById('add-registry-spinner');
    const registryCard = document.getElementById('add-registry-card');
    const apiConfigs = [
        {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllSymptoms',
            name: 'Symptoms',
            selectId: 'select2Primary_symptoms'
        },
        {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllDiagnosis',
            name: 'Diagnosis',
            selectId: 'select2Primary_diagnosis'
        },
        {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllMedicalSystem',
            name: 'Medical Systems',
            selectId: 'select2Primary_medical_system'
        },
        {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllBodyParts',
            name: 'Body Parts',
            selectId: ['select2Primary_body_part', 'select2Primary_body_part_IA']
        },
        {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllNatureOfInjury',
            name: 'Nature of Injury',
            selectId: 'select2Primary_nature_of_injury'
        },
        {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllInjuryMechanism',
            name: 'Injury Mechanism',
            selectId: 'select2Primary_injury_mechanism'
        },
        {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getMRNumber',
            name: 'MR Number',
            isMRNumber: true
        }
    ];
    let completedRequests = 0;
    let loadedData = {};
    function updateSpinner() {
        const loadingItems = Object.keys(loadedData).filter(key => loadedData[key] === 'loading');
        const completedItems = Object.keys(loadedData).filter(key => loadedData[key] === 'completed');
        if (loadingItems.length > 0) {
            spinnerLabel.textContent = `Loading ${loadingItems.join(', ')}... (${completedItems.length}/${apiConfigs.length} completed)`;
        } else if (completedItems.length === apiConfigs.length) {
            spinnerLabel.textContent = "Finalizing data...";
        } else {
            spinnerLabel.textContent = "Initializing...";
        }
    }
    const apiPromises = apiConfigs.map((config) => {
        loadedData[config.name] = 'loading';
        updateSpinner();
        return new Promise((resolve, reject) => {
            apiRequest({
                url: config.url,
                onSuccess: function (response) {
                    if (response.result && response.message) {
                        if (config.isMRNumber) {
                        } else if (Array.isArray(config.selectId)) {
                            config.selectId.forEach(id => populateSelect(id, response.message));
                        } else {
                            populateSelect(config.selectId, response.message);
                        }
                    }
                    loadedData[config.name] = 'completed';
                    completedRequests++;
                    updateSpinner();
                    resolve({ config, response });
                },
                onError: function (error) {
                    console.error(`Error fetching ${config.name}:`, error);
                    showToast('error', 'Error', `Failed to load ${config.name}`);
                    loadedData[config.name] = 'error';
                    completedRequests++;
                    updateSpinner();
                    resolve({ config, error: true });
                }
            });
        });
    });
    Promise.allSettled(apiPromises)
        .then((results) => {
            const isOutPatientAdded = $('#isOutPatientAdded').val();
            if (isOutPatientAdded) {
                spinnerLabel.textContent = "Loading existing patient data...";
                setTimeout(() => {
                    populateAddedOutpatientData();
                    finalizeLoading();
                }, 200);
            } else {
                finalizeLoading();
            }
        })
        .catch((error) => {
            console.error('Critical error in API loading:', error);
            finalizeLoading();
        });
    function finalizeLoading() {
        spinnerLabel.textContent = "Ready!";
        setTimeout(() => {
            spinner.style.display = 'none';
            registryCard.style.display = 'block';
        }, 300);
    }
}
$(document).ready(function () {
    document.getElementById("saveChangesModal").addEventListener("click", function () {
        let hospitalNameInput = document.getElementById("hospitalName").value.trim();
        let sanitizedHospitalName = hospitalNameInput.replace(/[<>]/g, "");
        document.getElementById("outsideReferralHospitalName").textContent = sanitizedHospitalName || "No Hospital Name Entered";
        let modal = bootstrap.Modal.getInstance(document.getElementById("basicModal"));
        modal.hide();
        document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
        document.body.classList.remove("modal-open");
        document.body.style.overflow = "auto";
    });
    document.getElementById("referralSelect").addEventListener("change", function () {
        let selectedValue = this.value;
        if (selectedValue === "OutsideReferral") {
            document.getElementById('outsideReferralMR').style.display = 'block';
        } else {
            document.getElementById('outsideReferralMR').style.display = 'none';
        }
    });
    const referralSelect = document.getElementById("referralSelect");
    const outsideReferralMR = document.getElementById("outsideReferralMR");
    referralSelect.addEventListener("change", function () {
        const selectedValue = this.value;
        if (selectedValue === "OutsideReferral") {
            outsideReferralMR.style.display = "block";
            const myModal = new bootstrap.Modal(document.getElementById('basicModal'));
            myModal.show();
        } else {
            outsideReferralMR.style.display = "none";
        }
    });
    $('#isOutPatientAdded').val(1);
    $('#isOutPatientAddedAndOpen').val(1)
    const isOutPatientAdded = $('#isOutPatientAdded').val();
    const openStatus = Number(employeeData.op_registry_datas.op_registry.open_status);
    if (openStatus === 0) {
        $('#isOutPatientAddedAndOpen').val(0);
    }
    const isOutPatientAddedAndOpen = $('#isOutPatientAddedAndOpen').val();
    if (isOutPatientAdded === '1') {
        if (isOutPatientAddedAndOpen === '0') {
            $('#incidentType').prop('disabled', true);
            $('#select2Primary_body_part').prop('disabled', true);
            $('#select2Primary_symptoms').prop('disabled', true);
            $('#select2Primary_medical_system').prop('disabled', true);
            $('#select2Primary_diagnosis').prop('disabled', true);
            $('#select2Primary_nature_of_injury').prop('disabled', true);
            $('#select2Primary_body_part_IA').prop('disabled', true);
            $('#select2Primary_injury_mechanism').prop('disabled', true);
            $('#injury_description').prop('disabled', true);
            $('#leftSide').prop('disabled', true);
            $('#rightSide').prop('disabled', true);
            $('#shopFloor').prop('disabled', true);
            $('#nonShopFloor').prop('disabled', true);
            $('#doctorSelect').prop('disabled', true);
            const injuryColorRadios = document.querySelectorAll('input[name="injury_color_text"]');
            injuryColorRadios.forEach(radio => {
                radio.disabled = true;
            });
        } else if (isOutPatientAddedAndOpen === '1') {
            showToast('info', 'Out Patient already added and still open');
        }
    } else {
        const now = new Date();
        const formattedNow = formatDateForInput(now);
        const oneDayLater = new Date(now);
        oneDayLater.setDate(now.getDate() + 1);
        const formattedLater = formatDateForInput(now);
        $('#leave-from').val(formattedNow);
        $('#reporting-datetime, #incident-datetime').val(formattedNow);
        toggleIncidentFields();
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2();
        }
    }
    document.getElementById('addPrescription').addEventListener('click', () => {
        const employeeId = $('#employeeId').val().toString().toLowerCase();
        const opRegistry = employeeData.op_registry_datas?.op_registry || {};
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(false, (opRegistryId) => {
            window.location = '/prescription/add-employee-prescription/' + employeeId + '/op/' + opRegistryId;
        });
    });
    document.getElementById('backToList').addEventListener('click', () => {
        window.location = "/ohc/health-registry/list-registry";
    });
    document.getElementById('addTest').addEventListener('click', () => {
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(false, (opRegistryId) => {
            const employeeId = $('#employeeId').val().toString().toLowerCase();
            window.location = '/ohc/health-registry/add-test/' + employeeId + '/op/' + opRegistryId;
        });
    });
    document.getElementById('saveClose').addEventListener('click', () => {
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(true, () => {
            window.location = '/ohc/health-registry/list-registry';
        });
    });
    document.getElementById('saveHR').addEventListener('click', () => {
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(false, (opRegistryId) => {
            const employeeId = $('#employeeId').val().toString().toLowerCase();
            window.location = '/ohc/health-registry/edit-registry/edit-outpatient/' + employeeId + '/op/' + opRegistryId;
        });
    });
    loadDataInParallel();
});

var incidentTypesData = employeeData.incidentTypeColorCodes || [];
function populateIncidentTypes() {
    const s = document.getElementById('incidentType');
    while (s.firstChild) s.removeChild(s.firstChild);
    let selected = null,
        medicalIllness = null;
    incidentTypesData.forEach(i => {
        const o = document.createElement('option');
        const v = {
            'Medical Illness': 'medicalIllness',
            'Industrial Accident': 'industrialAccident',
            'Outside Accident': 'outsideAccident'
        };
        o.value = v[i.incident_type_name] || i.incident_type_name.replace(/\s+/g, '').toLowerCase();
        o.textContent = i.incident_type_name;
        o.dataset.incidentId = i.incident_type_id;
        s.appendChild(o);
        if (i.incident_type_name === 'Medical Illness') medicalIllness = o;
        if (!selected) selected = o
    });
    if (medicalIllness) {
        medicalIllness.selected = true
    } else if (selected) {
        selected.selected = true
    }
}
function createInjuryColorOptions(cId, colors) {
    const c = document.getElementById(cId);
    if (!c) return;
    while (c.firstChild) c.removeChild(c.firstChild);
    if (!colors || Object.keys(colors).length === 0) {
        c.style.display = 'none';
        return
    }
    const f = document.createElement('div');
    f.className = 'd-flex flex-wrap';
    Object.entries(colors).forEach(([l, col]) => {
        const id = `${cId}_${l.replace(/\s+/g, '').toLowerCase()}`;
        const fc = document.createElement('div');
        fc.className = 'form-check me-3 mb-2';
        const inp = document.createElement('input');
        inp.className = 'form-check-input custom-radio';
        inp.type = 'radio';
        inp.name = 'injury_color_text';
        inp.id = id;
        inp.value = `${l}_${col}`;
        inp.dataset.color = col;
        inp.dataset.label = l;
        const lbl = document.createElement('label');
        lbl.className = 'form-check-label';
        lbl.setAttribute('for', id);
        lbl.textContent = l;
        const st = document.createElement('style');
        st.textContent =
            `#${id}:checked{background-color:${col} !important;border-color:${col} !important;}`;
        document.head.appendChild(st);
        fc.appendChild(inp);
        fc.appendChild(lbl);
        f.appendChild(fc)
    });
    c.appendChild(f);
    c.style.display = 'block'
}
function toggleIncidentFields() {
    const t = document.getElementById('incidentType').value;
    const si = incidentTypesData.find(i => {
        const v = {
            'Medical Illness': 'medicalIllness',
            'Industrial Accident': 'industrialAccident',
            'Outside Accident': 'outsideAccident'
        };
        return (v[i.incident_type_name] || i.incident_type_name.replace(/\s+/g, '').toLowerCase()) === t
    });
    const mf = document.getElementById('medicalFields');
    const inf = document.getElementById('industrialFields');
    const of = document.getElementById('outsideFields');
    const soi = document.getElementById('siteOfInjury');
    const mic = document.getElementById('medicalInjuryColor');
    mf.style.display = 'none';
    inf.style.display = 'none';
    of.style.display = 'none';
    mic.style.display = 'none';
    if (t === 'medicalIllness') {
        mf.style.display = 'block';
        if (si && si.injury_color_types) {
            createInjuryColorOptions('medicalInjuryColorOptions', si.injury_color_types);
            mic.style.display = 'block'
        }
    } else if (t === 'industrialAccident') {
        inf.style.display = 'block';
        soi.style.display = 'flex';
        if (si && si.injury_color_types) createInjuryColorOptions('industrialInjuryColorOptions', si
            .injury_color_types)
    } else if (t === 'outsideAccident') {
        of.style.display = 'block';
        if (si && si.injury_color_types) createInjuryColorOptions('outsideInjuryColorOptions', si
            .injury_color_types)
    }
}
document.getElementById('vehicleType').addEventListener('change', function () {
    const a = document.getElementById('ambulanceFields');
    if (this.value === 'ambulance') a.classList.remove('hidden');
    else a.classList.add('hidden')
});
function populateSelect(sId, data) {
    const s = document.getElementById(sId);
    if (!s) return;
    while (s.firstChild) s.removeChild(s.firstChild);
    data.forEach(item => {
        let o = document.createElement('option');
        o.value = item.op_component_id;
        o.textContent = item.op_component_name;
        s.appendChild(o)
    });
    if ($(s).hasClass('select2')) $(s).trigger('change')
}
function selectValues(sId, sv = []) {
    const s = document.getElementById(sId);
    if (!s) return;
    const opts = s.options;
    for (let i = 0; i < opts.length; i++) {
        if (sv.includes(parseInt(opts[i].value))) opts[i].selected = true
    }
    if ($(s).hasClass('select2')) $(s).trigger('change')
}
function formatDateForInput(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const h = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return `${y}-${m}-${day}T${h}:${min}`
}
function validateFields(f) {
    for (const [id, lbl] of Object.entries(f)) {
        const v = document.getElementById(id)?.value.trim() || '';
        if (!v) {
            showToast('error', `Please fill ${lbl}`);
            return false
        }
    }
    return true
}
function toggleCheckbox(sId) {
    const cbs = document.querySelectorAll('.site-of-injury');
    cbs.forEach(cb => {
        if (cb.id !== sId) cb.checked = false
    })
}
function handleIncidentType() {
    const t = document.getElementById('incidentType').value;
    const ds = document.getElementById('doctorSelect').value;
    const ic = document.querySelector('input[name="injury_color_text"]:checked');
    if (!ic) {
        showToast('error', 'Please select Injury Color');
        return false
    }
    if (!t) {
        showToast('error', 'Please select Incident Type');
        return false
    }
    const doc = {
        doctorId: ds,
        doctorName: $('#doctorSelect option:selected').text()
    };
    return true
}
function handleObservations() {
    const obs = {
        doctorNotes: $('#doctorNotes').val(),
        medicalHistory: $('#medicalHistory').val(),
        referral: $('#referralSelect').val(),
        movementSlip: $('#movementSlip').is(':checked'),
        fitnessCert: $('#fitnessCert').is(':checked'),
        physiotherapy: $('#physiotherapy').is(':checked')
    };
    if ($('#referralSelect').val() === "OutsideReferral") {
        const hn = document.getElementById("hospitalName").value;
        if (!hn) {
            showToast('error', 'Please fill Hospital Name');
            return false
        }
        const vt = document.getElementById("vehicleType").value;
        if (!vt) {
            showToast('error', 'Please select Vehicle Type');
            return false
        }
        if (vt === 'ambulance') {
            const dn = document.getElementById("driverName").value;
            const an = document.getElementById("ambulanceNumber").value;
            const oi = document.getElementById("odometerIn").value;
            const oo = document.getElementById("odometerOut").value;
            const ti = document.getElementById("timeIn").value;
            const to = document.getElementById("timeOut").value;
            if (!dn) {
                showToast('error', 'Please fill Driver Name');
                return false
            }
            if (!an) {
                showToast('error', 'Please fill Ambulance Number');
                return false
            }
            if (!oi) {
                showToast('error', 'Please fill Odometer In');
                return false
            }
            if (!oo) {
                showToast('error', 'Please fill Odometer Out');
                return false
            }
            if (!ti) {
                showToast('error', 'Please fill Time In');
                return false
            }
            if (!to) {
                showToast('error', 'Please fill Time Out');
                return false
            }
        }
    }
    return true
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
    }).then(result => {
        if (result.isConfirmed) {
            const hrd = {
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
                incidentTypeId: $('#incidentType option:selected').data('incidentId'),
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
            const dId = $('#doctorSelect').val();
            const dName = $('#doctorSelect option:selected').text();
            if (dId && dName !== "Select Doctor") hrd.doctor = {
                doctorId: dId,
                doctorName: dName
            };
            if (hrd.incidentType === "medicalIllness") {
                hrd.medicalFields = {
                    bodyPart: $('#select2Primary_body_part').val(),
                    symptoms: $('#select2Primary_symptoms').val(),
                    medicalSystem: $('#select2Primary_medical_system').val(),
                    diagnosis: $('#select2Primary_diagnosis').val(),
                    injuryColor: $('input[name="injury_color_text"]:checked').val()
                }
            } else if (hrd.incidentType === "industrialAccident") {
                hrd.industrialFields = {
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
                }
            } else {
                hrd.industrialFields = {
                    injuryColor: $('input[name="injury_color_text"]:checked').val(),
                    sideOfBody: {
                        left: $('#leftSideOutside').is(':checked'),
                        right: $('#rightSideOutside').is(':checked')
                    },
                    natureOfInjury: $('#select2Primary_nature_of_injury_outside').val(),
                    bodyPartIA: $('#select2Primary_body_part_outside').val(),
                    injuryMechanism: $('#select2Primary_injury_mechanism_outside').val(),
                    description: $('#injury_description_outside').val()
                }
            }
            let ref = $('#referralSelect').val();
            hrd.referral = ref;
            if (ref === 'OutsideReferral') {
                let hn = document.getElementById("hospitalName").value;
                let es = document.getElementById("esiScheme").checked ? 1 : 0;
                let vt = document.getElementById("vehicleType").value;
                hrd.hospitalDetails = {
                    hospitalName: hn,
                    esiScheme: es,
                    vehicleType: vt
                };
                if (vt === 'ambulance') {
                    let dn = document.getElementById("driverName").value;
                    let an = document.getElementById("ambulanceNumber").value;
                    let ab = document.getElementById("accompaniedBy").value;
                    let oi = document.getElementById("odometerIn").value;
                    let oo = document.getElementById("odometerOut").value;
                    let ti = document.getElementById("timeIn").value;
                    let to = document.getElementById("timeOut").value;
                    hrd.hospitalDetails = {
                        driverName: dn,
                        hospitalName: hn,
                        esiScheme: es,
                        vehicleType: vt,
                        ambulanceNumber: an,
                        accompaniedBy: ab,
                        odometerIn: oi,
                        odometerOut: oo,
                        timeIn: ti,
                        timeOut: to
                    }
                }
            }
            hrd.editExistingOne = 0;
            hrd.isFollowup = 0;
            apiRequest({
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/saveHealthRegistry',
                method: 'POST',
                data: hrd,
                onSuccess: function (r) {
                    showToast('success', 'Success', 'Health registry saved successfully!');
                    onSuccessCallback(r.op_registry_id)
                },
                onError: function (e) {
                    console.error('Error saving health registry:', e);
                    showToast('error', 'Error', 'Failed to save health registry')
                }
            })
        }
    })
}
function loadAllDataParallel() {
    const sl = document.getElementById('spinnerLabeltext');
    const sp = document.getElementById('add-registry-spinner');
    const rc = document.getElementById('add-registry-card');
    const iopa = $('#isOutPatientAdded').val();
    sl.textContent = 'Loading data...';
    const apis = [{
        url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllSymptoms',
        selectIds: ['select2Primary_symptoms'],
        name: 'Symptoms'
    }, {
        url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllDiagnosis',
        selectIds: ['select2Primary_diagnosis'],
        name: 'Diagnosis'
    }, {
        url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllMedicalSystem',
        selectIds: ['select2Primary_medical_system'],
        name: 'Medical Systems'
    }, {
        url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllBodyParts',
        selectIds: ['select2Primary_body_part', 'select2Primary_body_part_IA',
            'select2Primary_body_part_outside'
        ],
        name: 'Body Parts'
    }, {
        url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllNatureOfInjury',
        selectIds: ['select2Primary_nature_of_injury', 'select2Primary_nature_of_injury_outside'],
        name: 'Nature of Injury'
    }, {
        url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllInjuryMechanism',
        selectIds: ['select2Primary_injury_mechanism', 'select2Primary_injury_mechanism_outside'],
        name: 'Injury Mechanism'
    }, {
        url: 'https://login-users.hygeiaes.com/ohc/health-registry/getMRNumber',
        isMRNumber: true,
        name: 'MR Number'
    }];
    let cr = 0;
    const tr = apis.length;
    const ld = [];
    function up() {
        const p = Math.round((cr / tr) * 100);
        sl.textContent = `Loading data... ${p}% (${cr}/${tr})`
    }
    const aps = apis.map((req, idx) => {
        return new Promise((res, rej) => {
            apiRequest({
                url: req.url,
                onSuccess: function (resp) {
                    cr++;
                    up();
                    if (resp.result && resp.message) {
                        if (req.isMRNumber) { } else {
                            req.selectIds.forEach(sId => {
                                populateSelect(sId, resp.message)
                            })
                        }
                    }
                    ld.push({
                        name: req.name,
                        success: true
                    });
                    res(resp)
                },
                onError: function (err) {
                    cr++;
                    up();
                    console.error(`Error fetching ${req.name}:`, err);
                    showToast('error', 'Error', `Failed to load ${req.name}`);
                    ld.push({
                        name: req.name,
                        success: false,
                        error: err
                    });
                    res(null)
                }
            })
        })
    });
    Promise.all(aps).then(results => {
        const sl = ld.filter(i => i.success);
        const fl = ld.filter(i => !i.success);
        if (sl.length > 0) {
            if (iopa) showToast('success', 'Data Fetched Successfully.');
            if (fl.length > 0) {
                console.warn('Some data failed to load:', fl.map(f => f.name));
                showToast('warning', 'Warning', `Some data failed to load: ${fl.map(f => f.name).join(', ')}`)
            }
        }
        sl.textContent = "Preparing Outpatient Data...";
        setTimeout(() => {
            sp.style.display = 'none';
            rc.style.display = 'block'
        }, 500)
    }).catch(err => {
        console.error('Unexpected error during parallel loading:', err);
        showToast('error', 'Error', 'An unexpected error occurred while loading data');
        setTimeout(() => {
            sp.style.display = 'none';
            rc.style.display = 'block'
        }, 500)
    })
}
$(document).ready(function () {
    populateIncidentTypes();
    setTimeout(() => {
        toggleIncidentFields()
    }, 100);
    document.getElementById('hospital_id').addEventListener('change', function () {
        const hnd = document.getElementById('hospital_name_div');
        if (this.value === "0") {
            hnd.style.display = 'block'
        } else {
            hnd.style.display = 'none';
            document.getElementById('hospitalName').value = ''
        }
    });
    document.getElementById("saveChangesModal").addEventListener("click", function () {
        const hs = document.getElementById("hospital_id");
        const shi = hs.value;
        const hti = document.getElementById("hospitalName").value.trim();
        const hnf = document.getElementById("hospitalName");
        let shn = "";
        if (hnf) hnf.value = shn;
        if (shi === "0") {
            let shn = hti.replace(/[<>]/g, "");
            hnf.value = shn;
            document.getElementById("outsideReferralHospitalName").textContent = shn ||
                "No Hospital Name Entered"
        } else {
            hnf.value = shi;
            const so = hs.options[hs.selectedIndex];
            document.getElementById("outsideReferralHospitalName").textContent = so.text ||
                "No Hospital Selected"
        }
        const m = bootstrap.Modal.getInstance(document.getElementById("basicModal"));
        m.hide();
        document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
        document.body.classList.remove("modal-open");
        document.body.style.overflow = "auto"
    });
    document.getElementById("referralSelect").addEventListener("change", function () {
        let sv = this.value;
        if (sv === "OutsideReferral") document.getElementById('outsideReferralMR').style.display =
            'block';
        else document.getElementById('outsideReferralMR').style.display = 'none'
    });
    const rs = document.getElementById("referralSelect");
    const orm = document.getElementById("outsideReferralMR");
    rs.addEventListener("change", function () {
        const sv = this.value;
        if (sv === "OutsideReferral") {
            orm.style.display = "block";
            const mm = new bootstrap.Modal(document.getElementById('basicModal'));
            mm.show()
        } else orm.style.display = "none"
    });
    document.addEventListener("DOMContentLoaded", function () {
        const rne = document.getElementById("outsideReferralHospitalName");
        if (rne) {
            rne.addEventListener("click", function () {
                const mm = new bootstrap.Modal(document.getElementById('basicModal'));
                mm.show()
            })
        } else console.warn("Element #outsideReferralHospitalName not found.")
    });
    const iopa = $('#isOutPatientAdded').val();
    const iopaao = $('#isOutPatientAddedAndOpen').val();
    const now = new Date();
    const fdt1 = formatDateForInput(now);
    document.getElementById('leave-from').value = fdt1;
    const dl = new Date(now);
    dl.setDate(dl.getDate() + 1);
    document.getElementById('leave-upto').value = fdt1;
    document.getElementById('outTime').value = fdt1;
    const fdt2 = formatDateForInput(now);
    document.getElementById('reporting-datetime').value = fdt2;
    document.getElementById('incident-datetime').value = fdt2;
    toggleIncidentFields();
    if (typeof $.fn.select2 !== 'undefined') $('.select2').select2();
    document.getElementById('addPrescription').addEventListener('click', () => {
        const eid = $('#employeeId').val().toString().toLowerCase();
        const opr = employeeData.op_registry_datas?.op_registry || {};
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(false, orid => {
            window.location = '/prescription/add-employee-prescription/' + eid + '/op/' +
                orid
        })
    });
    document.getElementById('addTest').addEventListener('click', () => {
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(false, orid => {
            const eid = $('#employeeId').val().toString().toLowerCase();
            window.location = '/ohc/health-registry/add-test/' + eid + '/op/' + orid
        })
    });
    document.getElementById('saveClose').addEventListener('click', () => {
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(true, orid => {
            window.location = '/ohc/health-registry/list-registry'
        })
    });
    document.getElementById('saveHR').addEventListener('click', () => {
        if (!handleIncidentType()) return;
        if (!handleObservations()) return;
        sendHealthRegistryData(false, orid => {
            const eid = $('#employeeId').val().toString().toLowerCase();
            window.location = '/ohc/health-registry/edit-registry/edit-outpatient/' + eid +
                '/op/' + orid
        })
    });
    loadAllDataParallel()
});

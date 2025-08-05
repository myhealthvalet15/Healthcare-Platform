let allHealthPlansData = [];
let filteredData = [];
let currentCertificationData = null;
function bindModalEvents() {
    $('#conditionSelect').on('change', function () {
        updateBadgePreview();
    });
    $('#saveCertification').on('click', saveCertification);
}
    

function loadHealthPlans() {
    $('#healthPlanTableBody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    apiRequest({
        url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllAssignHealthPlans',
        method: 'GET',
        dataType: 'json',         
        onSuccess: function (response) {              
            if (response.result && Array.isArray(response.data)) {
                allHealthPlansData = response.data;
                filteredData = [...allHealthPlansData];
                populateHealthPlanTable(filteredData);
            } else {
                showToast('info', 'Notice', response.message || 'No health plans found.');
                $('#healthPlanTableBody').html('<tr><td colspan="5" class="text-center text-muted">No health plans found</td></tr>');
            }
        },
        onError: function (error) {
            showToast('error', 'Error', 'Failed to load health plans');
            $('#healthPlanTableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}
function getItemStatus(item) {
    if (item.cancelled) return 'Cancelled';
    if (item.certified) return 'Certified';
    if (item.no_show) return 'No Show';
    if (item.result_ready) return 'Result Ready';
    if (item.test_completed) return 'Test Completed';
    if (item.in_process) return 'In Process';
    if (item.schedule) return 'Schedule';
    if (item.pending) return 'Pending';
    return 'Pending';
}
function populateHealthPlanTable(data) {
    let tableBody = $('#healthPlanTableBody');
    tableBody.empty();
    if (data.length === 0) {
        tableBody.html('<tr><td colspan="5" class="text-center text-muted">No health plans found matching the selected criteria</td></tr>');
        return;
    }
    data.forEach(function (item) {
        let currentStatus = getItemStatus(item);
        let statusBadgeClass = getStatusBadgeClass(currentStatus);
        let formattedTestDate = formatDateToDDMMYYYY(item.test_date);
        let healthplanUrl = `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/health-plan/${item.id}/prescription-test/${item.test_id}/${item.test_code}`;
        let certificationBadges = '';
        if (item.certifications && Array.isArray(item.certifications)) {
            certificationBadges = item.certifications.map(function (cert) {
                let isResultReady = item.result_ready && item.result_ready !== null;
                let isMainCertified = item.certified && item.certified !== null;
                let badgeClass = 'certification-badge';
                let badgeStyle = '';
                let clickHandler = '';
                let cursorStyle = 'cursor: default;';
                let titleText = '';
                if (isResultReady) {
                    clickHandler = `onclick="openCertificationModal(this)"`;
                    cursorStyle = 'cursor: pointer;';
                    let hasHealthplanCert = cert.healthplan_certification &&
                        cert.healthplan_certification.condition !== null &&
                        cert.healthplan_certification.condition !== '';
                    if (hasHealthplanCert) {
                        badgeClass += ' certified';
                        let badgeColor = cert.healthplan_certification.color_condition || '#6c757d';
                        badgeStyle = `background-color: ${badgeColor} !important; color: white !important;`;
                        titleText = `Click to view ${capitalizeWords(cert.certification_title)} certification`;
                    } else {
                        badgeStyle = 'background-color: #f8f9fa !important; color: #495057 !important; border: 1px solid #dee2e6 !important;';
                        titleText = `Click to manage ${capitalizeWords(cert.certification_title)} certification`;
                    }
                } else {
                    clickHandler = '';
                    cursorStyle = 'cursor: default;';
                    badgeStyle = 'background-color: #e9ecef !important; color: #6c757d !important; opacity: 0.6 !important; border: 1px solid #dee2e6 !important;';
                    titleText = `${capitalizeWords(cert.certification_title)} - Available after result is ready`;
                }
                return `<span class="${badgeClass}" 
                style="${badgeStyle} ${cursorStyle}" 
                data-item-id="${item.id}"
                data-cert-id="${cert.certificate_id}"
                data-test-id="${item.test_id}"
                ${clickHandler}
                title="${titleText}">
                ${capitalizeWords(cert.certification_title)}
            </span>`;
            }).join(' ');
        }
        let row = `
            <tr>
                <td>
                    <div class="date-with-icon">
                        <span class="fw-medium">${formattedTestDate}</span>
                        <i class="fas fa-calendar-alt calendar-icon" 
                        onclick="openDatesModal(this)" 
                        data-item-id="${item.id}"
                        title="View all dates"></i>
                    </div>
                </td>
                
                <td>
                        <div class="text-truncate" style="max-width: 200px;" title="${item.healthplan_title || '-'}">
                            <a href="${healthplanUrl}" class="text-primary text-decoration-none fw-medium" 
                            style="cursor: pointer;">
                                ${item.healthplan_title || '-'}
                            </a>
                        </div>
                </td>

                <td>
                    <div class="d-flex flex-column" style="min-width: 200px;">
                        
                        <div class="mb-1" style="max-width: 200px;" title="${item.doctor_name || '-'}-${item.diagnosis_center || '-'}">
                            <span>${item.doctor_name || '-'} - ${item.diagnosis_center || '-'}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column align-items-start">
                        <span class="badge ${statusBadgeClass} mb-2">${currentStatus}</span>
                        <div class="d-flex flex-wrap gap-1" style="max-width: 150px;">
                            ${certificationBadges}
                        </div>
                    </div>
                </td>
            </tr>
        `;
        tableBody.append(row);
    });
}
function capitalizeWords(str) {
    return str.replace(/\b\w/g, char => char.toUpperCase()).toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
}
function parseDate(dateString) {
    let parts = dateString.split('/');
    return new Date(parts[2], parts[1] - 1, parts[0]);
}   
function openCertificationModal(element) {
    let itemId = $(element).data('item-id');
    let certId = $(element).data('cert-id');
    let testId = $(element).data('test-id');
    let currentItem = allHealthPlansData.find(item => item.id == itemId);
    if (!currentItem) return;
    if (!currentItem.result_ready || currentItem.result_ready === null) {
        showToast('warning', 'Not Available', 'Certification is only available after test results are ready.');
        return;
    }
    let currentCert = currentItem.certifications.find(cert => cert.certificate_id == certId);
    if (!currentCert) return;
    currentCertificationData = {
        item: currentItem,
        certification: currentCert,
        itemId: itemId,
        certId: certId,
        testId: testId
    };
    populateCertificationModal(currentCert);
    $('#certificationModal').modal('show');
}
const certificationBadgeCSS = `
        <style>
        .certification-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1.2;
            border-radius: 0.375rem;
            margin: 0.125rem;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            white-space: nowrap;
        }
        .certification-badge:hover:not([style*="opacity: 0.6"]) {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .certification-badge[style*="opacity: 0.6"] {
            cursor: not-allowed !important;
        }
        </style>
        `;
if (!$('#certificationBadgeCSS').length) {
    $('head').append(certificationBadgeCSS);
}
function populateCertificationModal(certification) {
    $('#certificationTitle').text(certification.certification_title);
    let currentItem = currentCertificationData.item;
    let isMainCertified = currentItem.certified && currentItem.certified !== null;
    let conditionSelect = $('#conditionSelect');
    let remarksInput = $('#remarksInput');
    let saveCertificationBtn = $('#saveCertification');
    let closeBtnModal = $('.modal-footer .btn-secondary');
    conditionSelect.empty();
    conditionSelect.append('<option value="">Select Condition</option>');
    let conditions = [];
    try {
        conditions = typeof certification.condition === 'string'
            ? JSON.parse(certification.condition)
            : (Array.isArray(certification.condition) ? certification.condition : []);
    } catch (e) {
        console.error('Error parsing conditions:', e);
        conditions = [];
    }
    conditions.forEach(function (condition) {
        conditionSelect.append(`<option value="${condition}">${condition}</option>`);
    });
    let healthplanCert = certification.healthplan_certification;
    if (isMainCertified) {
        $('#modalMode').text('View Certification Details');
        if (healthplanCert && healthplanCert.condition) {
            $('#conditionSelect').closest('.col-md-4').hide();
            $('#issueDateInput').closest('.col-md-4').hide();
            $('#nextAssessmentInput').closest('.col-md-4').hide();
            $('#remarksInput').closest('.col-12').hide();
            $('#certificationViewContent').remove();
            let conditionIndex = conditions.indexOf(healthplanCert.condition);
            let badgeColor = certification.color_condition &&
                certification.color_condition.length > conditionIndex &&
                conditionIndex >= 0
                ? certification.color_condition[conditionIndex].replace(/`/g, '')
                : '#6c757d';
            let viewContent = `
            <div id="certificationViewContent" class="row g-3">
                <div class="col-12">
                    <div class="alert alert-light border">
                        <h6 class="mb-3 text-dark"><i class="fas fa-certificate me-2"></i>Certification Details</h6>
                        <div class="mb-3">
                            <strong class="text-dark">Condition:</strong>
                            <span class="badge ms-2" style="background-color: ${badgeColor}; color: white; font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                                ${healthplanCert.condition}
                            </span>
                        </div>
                        ${healthplanCert.remarks ? `
                            <div class="mb-3">
                                <strong class="text-dark">Remarks:</strong>
                                <div class="mt-1 p-2 bg-light border rounded text-dark">
                                    ${healthplanCert.remarks}
                                </div>
                            </div>
                        ` : ''}
                        <div class="mb-3">
                            <strong class="text-dark">Certified On:</strong>
                            <span class="text-dark fw-medium">${formatDateToDDMMYYYY(currentItem.certified)}</span>
                        </div>
                        ${healthplanCert.next_assessment_date ? `
                            <div class="mb-0">
                                <strong class="text-dark">Next Assessment Date:</strong>
                                <span class="text-dark fw-medium">${formatDateToDDMMYYYY(healthplanCert.next_assessment_date)}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
            $('#badgePreview').before(viewContent);
            $('#certifiedInfo').hide();
            saveCertificationBtn.hide();
            closeBtnModal.hide();
            $('.modal-footer').hide();
        } else {
            $('#certifiedInfo').show();
            $('#certifiedInfo').html(`
            <div class="alert alert-success">
                <!-- <strong>Certified On:</strong> ${formatDateToDDMMYYYY(currentItem.certified)}<br> -->
                <strong><em>No specific certification details available for this category.</em></strong>
            </div>
        `);
            $('#conditionSelect').closest('.col-md-4').hide();
            $('#issueDateInput').closest('.col-md-4').hide();
            $('#nextAssessmentInput').closest('.col-md-4').hide();
            $('#remarksInput').closest('.col-12').hide();
            conditionSelect.prop('disabled', true);
            remarksInput.prop('disabled', true);
            saveCertificationBtn.hide();
            closeBtnModal.hide();
            $('.modal-footer').hide();
        }
    } else {
        $('#conditionSelect').closest('.col-md-4').show();
        $('#issueDateInput').closest('.col-md-4').show();
        $('#nextAssessmentInput').closest('.col-md-4').show();
        $('#remarksInput').closest('.col-12').show();
        const issueDateTime = healthplanCert?.certified_on;
        const issueDateOnly = issueDateTime ? issueDateTime.split(' ')[0] : '';
        $('#issueDateInput').val(issueDateOnly);
        $('#nextAssessmentInput').val(healthplanCert?.next_assessment_date || '');
        $('#certificationViewContent').remove();
        $('.modal-footer').show();
        closeBtnModal.show();
        let hasExistingData = healthplanCert && (healthplanCert.condition || healthplanCert.remarks);
        if (hasExistingData) {
            $('#modalMode').text('Edit Certification');
            conditionSelect.val(healthplanCert.condition || '');
            remarksInput.val(healthplanCert.remarks || '');
            saveCertificationBtn.text('Update Certification').show();
        } else {
            $('#modalMode').text('Add Certification');
            conditionSelect.val('');
            remarksInput.val('');
            saveCertificationBtn.text('Save Certification').show();
        }
        $('#certifiedInfo').hide();
        conditionSelect.prop('disabled', false);
        remarksInput.prop('disabled', false);
    }
    updateBadgePreview();
}
const additionalModalCSS = `
            <style>
                #certificationViewContent .alert {
                    border-left: 4px solid #6c757d;
                    background-color: #f8f9fa;
                }
                #certificationViewContent .badge {
                    font-size: 0.875rem !important;
                    padding: 0.375rem 0.75rem !important;
                    border-radius: 0.375rem !important;
                }
                #certificationViewContent .bg-light {
                    background-color: #f8f9fa !important;
                    border: 1px solid #dee2e6 !important;
                    border-radius: 0.375rem !important;
                    padding: 0.75rem !important;
                    font-style: italic;
                    color: #495057 !important;
                }
                .modal-content {
                    border-radius: 0.5rem;
                }
                .modal-header {
                    background-color: #f8f9fa;
                    border-bottom: 1px solid #dee2e6;
                }
                .modal-title {
                    color: #495057;
                    font-weight: 600;
                }
                #certificationViewContent .text-dark {
                    color: #495057 !important;
                }
            </style>
        `;
if (!$('#additionalModalCSS').length) {
    $('head').append(additionalModalCSS);
}
function updateBadgePreview() {
    let selectedCondition = $('#conditionSelect').val();
    let badgePreview = $('#badgePreview');
    if (selectedCondition && currentCertificationData) {
        let cert = currentCertificationData.certification;
        let currentItem = currentCertificationData.item;
        let isMainCertified = currentItem.certified && currentItem.certified !== null;
        let conditions = [];
        try {
            conditions = typeof cert.condition === 'string'
                ? JSON.parse(cert.condition)
                : (Array.isArray(cert.condition) ? cert.condition : []);
        } catch (e) {
            conditions = [];
        }
        let colorIndex = conditions.indexOf(selectedCondition);
        let badgeColor = cert.color_condition &&
            cert.color_condition.length > colorIndex &&
            colorIndex >= 0
            ? cert.color_condition[colorIndex]
            : '#6c757d';
        if (!isMainCertified) {
            badgePreview.html(`
            <div class="badge-preview" style="background-color: ${badgeColor}; color: white;">
                ${cert.certification_title}: ${selectedCondition}
            </div>
            <!-- <small class="text-muted d-block mt-1">Preview - will be applied after main certification</small> -->
        `).show();
        } else {
            badgePreview.html(`
            <div class="badge-preview" style="background-color: ${badgeColor}; color: white;">
                ${cert.certification_title}: ${selectedCondition}
            </div>
        `).show();
        }
    } else {
        badgePreview.hide();
    }
}

function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'bg-label-warning';
        case 'schedule':
            return 'bg-label-info';
        case 'in process':
            return 'bg-label-primary';
        case 'test completed':
            return 'bg-label-success';
        case 'result ready':
            return 'bg-label-success';
        case 'no show':
            return 'bg-label-danger';
        case 'certified':
            return 'bg-label-success';
        case 'cancelled':
            return 'bg-label-dark';
        default:
            return 'bg-label-secondary';
    }
}
function formatDateToDDMMYYYY(dateString) {
    if (!dateString) return '-';
    try {
        let date = new Date(dateString);
        if (isNaN(date.getTime())) return '-';
        let day = String(date.getDate()).padStart(2, '0');
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let year = date.getFullYear();
        return `${day}/${month}/${year}`;
    } catch (e) {
        return '-';
    }
}
function showDateDetailsModal(assigned, due, diagnosis, assessment) {
    $('#modalAssignedDate').text(assigned);
    $('#modalDueDate').text(due);
    $('#modalDiagnosisDate').text(diagnosis);
    $('#modalAssessmentDate').text(assessment);
    $('#dateDetailsModal').modal('show');
}
function openDatesModal(element) {
    let itemId = $(element).data('item-id');
    let currentItem = allHealthPlansData.find(item => item.id == itemId);
    if (!currentItem) return;
    $('#employeeName').text(`${currentItem.first_name} ${currentItem.last_name}`);
    let datesData = [
        {
            label: 'Assigned Date',
            value: formatDateToDDMMYYYY(currentItem.test_date),
            rawValue: currentItem.test_date,
            isBold: true
        },
        {
            label: 'Due Date',
            value: formatDateToDDMMYYYY(currentItem.test_due_date),
            rawValue: currentItem.test_due_date,
            isBold: true
        },
        {
            label: 'Diagnosis Date',
            value: currentItem.in_process ? formatDateToDDMMYYYY(currentItem.in_process) : 'Not Set',
            rawValue: currentItem.in_process,
            isBold: true
        },
        {
            label: 'Assessment Date',
            value: currentItem.result_ready ? formatDateToDDMMYYYY(currentItem.result_ready) : 'Not Set',
            rawValue: currentItem.result_ready,
            isBold: true
        }
    ];
    if (currentItem.certified) {
        datesData.push({
            label: 'Certification Date',
            value: formatDateToDDMMYYYY(currentItem.certified),
            rawValue: currentItem.certified,
            isBold: true
        });
    }
    let modalBody = $('#datesModalBody');
    modalBody.empty();
    datesData.forEach(function (dateItem) {
        let labelClass = dateItem.isBold ? 'date-label-bold' : 'date-label-normal';
        let dateHtml = `
        <div class="date-entry">
            <div class="date-entry-content">
                <span class="${labelClass}">${dateItem.label}</span> - <span class="date-value">${dateItem.value}</span>
            </div>
        </div>
    `;
        modalBody.append(dateHtml);
    });
    $('#datesModal').modal('show');
    $('#datesModal').on('shown.bs.modal', function () {
        $('.modal-backdrop').addClass('dates-modal-backdrop');
    });
    $('#datesModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').removeClass('dates-modal-backdrop');
    });
}

$(document).ready(function () {
    $('#certificationModal').on('click', '.btn-close', function () {
        $('#certificationModal').modal('hide');
    });
    $('#certificationModal').on('hidden.bs.modal', function () {
        $('#certificationViewContent').remove();
        $('#conditionSelect').closest('.col-md-4').show();
        $('#issueDateInput').closest('.col-md-4').show();
        $('#nextAssessmentInput').closest('.col-md-4').show();
        $('#remarksInput').closest('.col-12').show();
        $('.modal-footer').show();
        $('.modal-footer .btn-secondary').show();
        $('#saveCertification').show();
        $('#conditionSelect').prop('disabled', false);
        $('#remarksInput').prop('disabled', false);
        $('#conditionSelect').val('');
        $('#remarksInput').val('');
        $('#issueDateInput').val('');
        $('#nextAssessmentInput').val('');
        $('#badgePreview').hide();
    });
    loadHealthPlans();
    bindModalEvents();
    

});
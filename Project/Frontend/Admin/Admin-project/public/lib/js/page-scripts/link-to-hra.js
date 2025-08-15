
async function loadTemplates() {
    try {
        const response = await apiRequest({
            url: 'https://mhv-admin.hygeiaes.com/hra/fetch-templates',
            method: 'GET',
            onSuccess: function (data) {
                const selectTemplate = $('#select2Primary');
                selectTemplate.empty().append('<option value="">Select value</option>');
                if (data && Array.isArray(data)) {
                    data.forEach(template => {
                        selectTemplate.append(new Option(template.template_name, template.template_id));
                    });
                }
                selectTemplate.trigger('change');
            },
            onError: function (errorMessage) {
                console.error('Error loading templates:', errorMessage);
                $('#select2Primary').empty().append('<option value="">Select value</option>').trigger('change');
            }
        });
    } catch (error) {
        console.error('Error loading templates:', error);
    }
}
async function loadCorporates() {
    try {
        const response = await apiRequest({
            url: 'https://mhv-admin.hygeiaes.com/corporate/getCorporateOfHraTemplate',
            method: 'GET',
            onSuccess: function (data) {
                const selectCorporate = $('#select2Basic');
                const tableBody = $('.table-border-bottom-0');
                selectCorporate.empty().append('<option value="">Select value</option>');
                tableBody.empty();
                if (data && data.result && Array.isArray(data.data)) {
                    data.data.forEach(corporate => {
                        if (!corporate.hra_template_ids || corporate.hra_template_ids.length === 0) {
                            selectCorporate.append(new Option(corporate.corporate_name, corporate.corporate_id));
                        } else {
                            let templateBadges = corporate.hra_templates.map(template =>
                                `<span class="badge bg-label-primary me-1 mb-1">${template}</span>`
                            ).join('');
                            let row = `
                                <tr>
                                    <td>${corporate.corporate_name}</td>
                                    <td>${templateBadges}</td>
                                    <td>
                                        <a class="btn btn-sm btn-warning edit-btn" 
                                           href="javascript:void(0);"
                                           data-corporate-id="${corporate.corporate_id}"
                                           data-corporate-name="${corporate.corporate_name}"
                                           data-template-ids='${JSON.stringify(corporate.hra_template_ids)}'>
                                            <i class="ti ti-pencil me-1"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        }
                    });
                }
                selectCorporate.trigger('change');
            },
            onError: function (errorMessage) {
                console.error('Error loading corporates:', errorMessage);
                $('#select2Basic').empty().append('<option value="">Select value</option>').trigger('change');
            }
        });
    } catch (error) {
        console.error('Error loading corporates:', error);
    }
}
let isEditMode = false;
let editingCorporateId = null;
function switchToEditMode(corporateId, corporateName, templateIds) {
    isEditMode = true;
    editingCorporateId = corporateId;
    const newOption = new Option(corporateName, corporateId, true, true);
    $('#select2Basic').empty().append(newOption).trigger('change');
    $('#select2Basic').prop('disabled', true);
    $('#select2Primary').val(templateIds).trigger('change');
    $('#submitButton')
        .removeClass('btn-primary')
        .addClass('btn-warning')
        .html('Update&nbsp;&nbsp;<i class="fa-solid fa-pen"></i>');
}
function resetForm() {
    isEditMode = false;
    editingCorporateId = null;
    $('#select2Basic').prop('disabled', false);
    $('#addLink2HraForm')[0].reset();
    $('#select2Basic, #select2Primary').val(null).trigger('change');
    $('#submitButton').html('Add&nbsp;&nbsp;<i class="fa-solid fa-plus"></i>');
    loadCorporates();
}
async function initializePage() {
    try {
        await Promise.all([loadTemplates(), loadCorporates()]);
        document.getElementById('link-to-hra').style.display = 'none';
        document.getElementById('linkForm').style.display = 'block';
    } catch (error) {
        console.error('Error initializing page:', error);
    }
}
document.addEventListener('DOMContentLoaded', function () {
    initializePage();
    $('#select2Basic').select2({
        placeholder: "Select Corporate",
        allowClear: true
    });
    $('#select2Primary').select2({
        placeholder: "Select Template",
        multiple: true
    });
    $(document).on('click', '.edit-btn', function () {
        const corporateId = $(this).data('corporate-id');
        const corporateName = $(this).data('corporate-name');
        const templateIds = $(this).data('template-ids');
        switchToEditMode(corporateId, corporateName, templateIds);
    });
    $('#addLink2HraForm').on('submit', async function (event) {
        event.preventDefault();
        let corporateId = $('#select2Basic').val();
        let selectedTemplateIds = $('#select2Primary').val();
        let isValid = true;
        $('.error-text').remove();
        if (!corporateId) {
            $('#select2Basic').addClass('is-invalid');
            $('#select2Basic')
                .closest('.col-md-6')
                .append('<small class="error-text text-danger d-block mt-1">Select Corporate</small>');
            isValid = false;
        }
        if (!selectedTemplateIds || selectedTemplateIds.length === 0) {
            $('#select2Primary').addClass('is-invalid');
            $('#select2Primary')
                .closest('.col-md-6')
                .append('<small class="error-text text-danger d-block mt-1">Select 1 or more templates</small>');
            isValid = false;
        }
        if (!isValid) {
            showToast("error", "Please select the corporate and at least one template from the dropdown");
            return;
        }
        try {
            const endpoint = isEditMode ? '/corporate/updateCorporateHraLink' : '/corporate/linkCorporateToHra';
            const response = await apiRequest({
                url: endpoint,
                method: 'POST',
                data: {
                    corporate_id: corporateId,
                    template_ids: selectedTemplateIds
                },
                onSuccess: function (data) {
                    if (data.result) {
                        showToast("success", isEditMode ?
                            "Templates updated successfully!" :
                            "Corporate and templates linked successfully!");
                        resetForm();
                        loadCorporates();
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast("error", "Operation failed. Please try again.");
                    }
                },
                onError: function (errorMessage) {
                    showToast('error', 'An error occurred. Please try again.');
                }
            });
        } catch (error) {
            showToast('error', 'An error occurred. Please try again.');
        }
    });
    $('#select2Basic, #select2Primary').on('select2:select', function () {
        $(this).removeClass('is-invalid');
        $(this).closest('.col-md-6').find('.error-text').remove();
    });
});

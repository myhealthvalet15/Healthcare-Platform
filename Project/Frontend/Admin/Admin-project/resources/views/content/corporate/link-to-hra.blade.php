@extends('layouts/layoutMaster')
@section('title', ' Link to HRA - Corporate')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/forms-selects.js',
'resources/assets/js/form-layouts.js',
'resources/assets/js/forms-typeahead.js'
])
@endsection
@section('content')
<style>
    .link-to-hra {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 200px;
        text-align: center;
        margin-top: 75px;
    }

    .spinner-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sk-bounce {
        display: flex;
        justify-content: space-between;
        width: 50px;
    }

    .sk-bounce-dot {
        width: 30px;
        height: 30px;
        margin: 0 5px;
        background-color: #007bff;
        border-radius: 50%;
        animation: sk-bounce 1.4s infinite ease-in-out both;
    }
</style>
<div class="card mb-6">
    <h5 class="card-header">Link Corporates to HRA</h5>
    <div class="link-to-hra" id="link-to-hra" style="display: block;">
        <div class="spinner-container">
            <div class="sk-bounce sk-primary">
                <div class="sk-bounce-dot"></div>
                <div class="sk-bounce-dot"></div>
            </div>
            <label id="spinnerLabeltext">retrieving datas ...</label>
        </div>
    </div>
    <div id="linkForm" style="display: none;">
        <form class="card-body" id="addLink2HraForm">
            <div class="row g-6">
                <div class="col-md-6 mb-6">
                    <label for="select2Basic" class="form-label">Select Corporate</label>
                    <select id="select2Basic" class="select2 form-select form-select-lg" data-allow-clear="true">
                        <option value="">Select value</option>
                    </select>
                </div>
                <div class="col-md-6 mb-6">
                    <label for="select2Primary" class="form-label">Select Template</label>
                    <div class="select2-primary">
                        <select id="select2Primary" class="select2 form-select" multiple>
                            <option value="">Select value</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mt-4 text-start">
                    <button type="submit" id="submitButton" class="btn btn-primary">Add&nbsp;&nbsp;<i
                            class="fa-solid fa-plus"></i></button>
                </div>
            </div>
        </form>
        <h5 class="card-header">Linked Corporates</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Corporates</th>
                        <th>Templates</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <tr>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
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
</script>
@endsection
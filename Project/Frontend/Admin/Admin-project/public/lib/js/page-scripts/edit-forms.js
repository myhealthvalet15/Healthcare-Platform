let assignedFormIds = [];
$.ajax({
    url: `/getassign-forms/${corporateId}/${locationId}`,
    method: 'GET',
    dataType: 'json',
    success: function (assignResponse) {
        if (assignResponse.success) {
            assignedFormIds = assignResponse.data.selectedFormIds || [];
        }
        fetchAvailableForms();
    },
    error: function (xhr) {
        console.error("Error loading assigned forms:", xhr.responseText);
    }
});
function fetchAvailableForms() {
    $.ajax({
        url: '/corporate/module4-submodules',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            let container = $('#form-sections');
            container.empty();
            let formsBySubModule = response.data.formsBySubModule || [];
            if (formsBySubModule.length === 0) {
                container.append('<p>No forms found for module ID 4.</p>');
                return;
            }
            let section = `<div class="mb-4 border p-3">
                    <h5>Forms</h5>
                `;
            formsBySubModule.forEach(form => {
                const isChecked = assignedFormIds.includes(String(form.corporate_form_id));
                section += `
                        <div class="form-check" style="margin-left:10px;">
                            <input class="form-check-input" type="checkbox" name="form_ids[]" value="${form.corporate_form_id}" id="form_${form.corporate_form_id}" ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label" for="form_${form.corporate_form_id}">${form.sub_module_name}</label>
                        </div>
                    `;
            });
            section += `</div>`;
            container.append(section);
        },
        error: function (xhr) {
            console.error("Error loading forms:", xhr.responseText);
            $('#form-sections').append('<p class="text-danger">Failed to load submodules.</p>');
        }
    });
}
$('#assignFormsForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        url: '/corporate/module4-assignSubmodules',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showToast('success', response.message);
                setTimeout(function () {
                    window.location.href = response.redirect_url;
                }, 2000);
            } else {
                alert('Something went wrong. Please try again.');
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            alert('An error occurred while submitting the form.');
        }
    });
});

$('#add-employee-type').on('click', function () {
    var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddEmployeeType'));
    offcanvas.show();
});
$('#form-add-new-record').on('submit', function () {
    toastr.success('Employee Type Added!');
    $('#offcanvasAddEmployeeType').offcanvas('hide');
});
$(document).on('change', '.toggle-active-status', function () {
    const statusLabel = $(this).siblings('.form-check-label').find('.status-label');
    const isChecked = this.checked;
    statusLabel.text(isChecked ? 'Active' : 'Inactive');
    statusLabel.css('color', isChecked ? 'green' : 'lightcoral');
});
$(document).ready(function () {
    $(document).on('change', '.Contractors', function () {
        if (this.checked) {
            $('.Contractors').not(this).prop('checked', false);
        }
    });
    $('#step-3-form').on('submit', function (e) {
        let isValid = true;
        $('input[name="employee_type_name[]"]').each(function () {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        const contractorChecked = $('.Contractors:checked').length;
        if (contractorChecked > 1) {
            isValid = false;
            toastr.error('Only one "Contractor/Vendor" can be selected.');
        }
        if (!isValid) {
            e.preventDefault();
        }
    });
});

$(document).ready(function () {
    apiRequest({
        url: '/corporate/getDepartments',
        method: 'GET',
        onSuccess: function (response) {
            if (response.data) {
                const departmentSelect = $('#department');
                const departments = response.data;

                departments.forEach(function (departmentname) {
                    departmentSelect.append(new Option(departmentname.hl1_name, departmentname.hl1_id));
                });

                $('#department').on('change', function () {
                    const selectedValues = $(this).val();

                    if (selectedValues && selectedValues.includes('select-all')) {
                        $(this)
                            .find('option')
                            .prop('selected', function () {
                                return $(this).val() !== 'select-all';
                            });
                    } else if (selectedValues && selectedValues.length === departments.length) {
                        $(this).find('option[value="select-all"]').prop('selected', true);
                    } else {
                        $(this).find('option[value="select-all"]').prop('selected', false);
                    }

                    $(this).trigger('change');
                });

            } else {
                console.error('No department found');
            }
        },
        onError: function (error) {
            console.error('An error occurred: ' + error);
        }
    });

});
$('#add-corporate-user').click(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var first_name = $('#first_name').val();
    var last_name = $('#last_name').val();
    var email = $('#email').val();
    var mobile_country_code = $('#mobile_country_code').val();
    var mobile_num = $('#mobile_num').val();
    var department = $('#department').val();
    var aadhar = $('#aadhar').val();
    var gender = $('input[name="gender"]:checked').val();
    var setting = $('input[name="setting"]:checked')
        .map(function (_, el) {
            return $(el).val();
        })
        .get();
    if (Array.isArray(department)) {
        console.log('department is an array:', department);
    } else {
        department = [];
    }
    var formIsValid = true;
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    if (first_name.trim() === '') {
        formIsValid = false;
        $('#first_name').addClass('is-invalid');
        $('#first_name').after('<div class="invalid-feedback">The First Name is required.</div>');
    }
    if (last_name.trim() === '') {
        formIsValid = false;
        $('#last_name').addClass('is-invalid');
        $('#last_name').after('<div class="invalid-feedback">The Last Name is required.</div>');
    }
    if (email.trim() === '') {
        formIsValid = false;
        $('#email').addClass('is-invalid');
        $('#email').after('<div class="invalid-feedback">Email is required.</div>');
    }
    if (mobile_num.trim() === '') {
        formIsValid = false;
        $('#mobile_num').addClass('is-invalid');
        $('#mobile_num').after('<div class="invalid-feedback">The Mobile Number is required.</div>');
    }
    if (formIsValid) {
        var formData = {
            _token: csrfToken,
            first_name: first_name,
            last_name: last_name,
            email: email,
            mobile_country_code: mobile_country_code,
            mobile_num: mobile_num,
            department: department,
            aadhar: aadhar,
            gender: gender,
            setting: setting
        };
        apiRequest({
            url: '/corporate-users/insertUser',
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
                console.log('response:', response.result);
                if (response.result === true) {
                    showToast('success', 'Data saved successfully!');
                    window.location.href = 'https://login-users.hygeiaes.com/corporate-users/users-list';
                } else {
                    alert('An error occurred while saving the data.');
                }
            },
            onError: function (error) {
                console.error('An error occurred: ' + error);
                alert('An error occurred while saving the data.');
            }
        });

    }
});
$('#first_name').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('#last_name').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('#email').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('#mobile_country_code').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('#mobile_num').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('#department').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('#aadhar').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('[name="gender"]').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});
$('#setting').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
});

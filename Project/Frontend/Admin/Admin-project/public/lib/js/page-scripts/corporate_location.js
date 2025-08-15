$(function () {
    const select2 = $('.select2'),
        selectPicker = $('.selectpicker');
    const wizardValidation = document.querySelector('#wizard-validation');
    console.log(wizardValidation)
    if (wizardValidation) {
        const wizardValidationForm = wizardValidation.querySelector('#wizard-validation-form');
        const wizardSteps = {
            step1: wizardValidationForm.querySelector('#account-details-validation'),
            step2: wizardValidationForm.querySelector('#personal-info-validation'),
            step5: wizardValidationForm.querySelector('#adminuser-details-validation'),
        };
        const nextButtons = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
        const prevButtons = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));
        const validationStepper = new Stepper(wizardValidation, {
            linear: true
        });
        const commonPlugins = {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5(),
            autoFocus: new FormValidation.plugins.AutoFocus(),
            submitButton: new FormValidation.plugins.SubmitButton()
        };
        const FormValidation1 = FormValidation.formValidation(wizardSteps.step1, {
            fields: {
                corporate_name: {
                    validators: {
                        notEmpty: {
                            message: 'The name is required'
                        },
                        stringLength: {
                            min: 6,
                            max: 30,
                            message: 'The name must be between 6 and 30 characters'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: 'Only letters, numbers, and spaces are allowed'
                        }
                    }
                },
                display_name: {
                    validators: {
                        notEmpty: {
                            message: 'The display name is required'
                        },
                        stringLength: {
                            min: 6,
                            max: 30,
                            message: 'The name must be between 6 and 30 characters'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: 'Only letters, numbers, and spaces are allowed'
                        }
                    }
                },
                valid_from: {
                    validators: {
                        notEmpty: {
                            message: 'The start date is required'
                        },
                        date: {
                            format: 'YYYY-MM-DD',
                            message: 'The start date is not valid'
                        }
                    }
                },
                valid_upto: {
                    validators: {
                        notEmpty: {
                            message: 'The end date is required'
                        },
                        date: {
                            format: 'YYYY-MM-DD',
                            message: 'The end date is not valid'
                        }
                    }
                }
            },
            plugins: commonPlugins
        }).on('core.form.valid', () => validationStepper.next());
        const FormValidation2 = FormValidation.formValidation(wizardSteps.step2, {
            fields: {
                formValidationCountry: {
                    validators: {
                        notEmpty: {
                            message: 'Pincode is required'
                        },
                        regexp: {
                            regexp: /^[1-9][0-9]{5}$/,
                            message: 'Pincode must be a valid 6-digit number'
                        }
                    }
                }
            },
            plugins: commonPlugins
        }).on('core.form.valid', () => validationStepper.next());
        const FormValidation5 = FormValidation.formValidation(wizardSteps.step5, {
            fields: {
                first_name: {
                    validators: {
                        notEmpty: {
                            message: 'First Name is required.'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Email is required.'
                        },
                        emailAddress: {
                            message: 'Please enter a valid email address.'
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'Password is required.'
                        },
                        stringLength: {
                            min: 8,
                            message: 'Password must be at least 8 characters long.'
                        }
                    }
                },
                aadhar: {
                    validators: {
                        notEmpty: {
                            message: 'Aadhar is required.'
                        },
                        stringLength: {
                            min: 12,
                            max: 12,
                            message: 'Aadhar must be 12 digits.'
                        },
                        numeric: {
                            message: 'Only numeric values are allowed.'
                        }
                    }
                }
            },
            plugins: commonPlugins
        }).on('core.form.valid', () => {
            alert('All steps are valid. You can now submit the form!');
            wizardValidationForm.submit();
        });
        nextButtons.forEach((button, index) => {
            button.addEventListener('click', () => {
                switch (index) {
                    case 0:
                        FormValidation1.validate();
                        break;
                    case 1:
                        FormValidation2.validate();
                        break;
                    case 2:
                        FormValidation5.validate();
                        break;
                }
            });
        });
        prevButtons.forEach(button => {
            button.addEventListener('click', () => validationStepper.previous());
        });
    }
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            var placeholderText = $this.data('placeholder') ||
                'Please select';
            $this.select2({
                placeholder: placeholderText,
                dropdownParent: $this.parent(),
            });
        });
    }
    $(document).ready(function () {
        $('#display_name').after(
            '<div id="displayNameError" style="color: red; display: none;">This Display Name is already used. Please choose another.</div>'
        );
        $('#display_name').on('blur', function () {
            var enteredName = $(this).val().trim();
            if (enteredName === existingDisplayName) {
                $('#displayNameError').show();
                $(this).focus();
            } else {
                $('#displayNameError')
                    .hide();
            }
        });
        $('#formValidationCountry').select2({
            placeholder: "Select the pincode",
            allowClear: true,
            minimumInputLength: 4,
            ajax: {
                url: '/location/findpincode',
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        pincode: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.address_id,
                                text: item.address_name
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });
    $('#formValidationCountry').change(function () {
        var address_id = $('#formValidationCountry').val();
        var address_name = $('#formValidationCountry option:selected')
            .text();
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'POST',
            url: "/area_find",
            data: {
                address_name: address_name,
                _token: token
            },
            success: function (response) {
                $('#areaoptions').empty();
                $('#areaoptions').append(
                    '<option label=" ">Please search area </option>');
                $.each(response, function (index, area) {
                    $('#areaoptions').append('<option value="' + area
                        .address_id +
                        '">' + area.address_name + '</option>');
                });
                $('#areaoptions').trigger('change');
                initializeSelect2($('#areaoptions'));
            },
            error: function (xhr, status, error) {
                console.error("There was an error:", error);
            }
        });
    });
    $('#areaoptions').change(function () {
        var area_id = $('#areaoptions').val();
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "/location/findlocation",
            type: "POST",
            data: {
                address_id: area_id,
                _token: token
            },
            success: function (response) {
                if (response?.state?.length > 0) {
                    populateDropdown('#stateoptions', response.state);
                    initializeSelect2($(
                        '#stateoptions'));
                    if (response?.state?.length > 0) {
                        $('#stateoptions').val(response.state[0].address_id).trigger(
                            'change');
                    }
                }
                if (response?.country?.length > 0) {
                    populateDropdown('#countryoptions', response.country);
                    initializeSelect2($(
                        '#countryoptions'));
                    if (response?.country?.length > 0) {
                        $('#countryoptions').val(response.country[0].address_id)
                            .trigger(
                                'change');
                    }
                }
                if (response?.city?.length > 0) {
                    populateDropdown('#cityoptions', response.city);
                    initializeSelect2($(
                        '#cityoptions'));
                    if (response?.city?.length > 0) {
                        $('#cityoptions').val(response.city[0].address_id).trigger(
                            'change');
                    }
                    $("#Country, #City, #State").show();
                }
            },
            error: function (xhr) {
                console.error('Error fetching addresses:', xhr.responseText || xhr
                    .statusText);
                alert('Error fetching addresses. Please try again later.');
            }
        });
    });

    function populateDropdown(selector, items) {
        if ($(selector).length) {
            $(selector).empty();
            $(selector).append('<option value="">Please select</option>');
            $.each(items, function (key, value) {
                $(selector).append(
                    `<option value="${value.address_id}">${value.address_name}</option>`);
            });
        }
    }

    function initializeSelect2($element) {
        if ($element.length && !$element.hasClass('select2-hidden-accessible')) {
            $element.select2({
                placeholder: $element.data('placeholder') || 'Please select',
                dropdownParent: $element.parent()
            });
        }
    }
});
$(document).ready(function () {
    $('#wizard-validation-form').on('submit', function (e) {
        e.preventDefault();
        var accountDetails = {
            corporate_name: $('#corporate_name').val(),
            corporate_no: $('#corporate_no').val(),
            corporate_id: $('#corporate_id').val(),
            location_id: $('#location_id').val(),
            display_name: $('#display_name').val(),
            registration_no: $('#registration_no').val(),
            company_profile: $('#company_profile').val(),
            prof_image: $('#prof_image').val(),
            industry: $('#industry').val(),
            industry_segment: $('#industry_segment').val(),
            gstin: $('#gstin').val(),
            discount: $('#discount').val(),
            valid_from: $('#valid_from').val(),
            valid_upto: $('#valid_upto').val(),
            corporate_color: $('#corporate_color').val(),
            active_status: $('input[name="active_status"]:checked').val()
        };
        var address = {
            corporate_id: $('#corporate_id').val(),
            pincode: $('#formValidationCountry').val(),
            area: $('#areaoptions').val(),
            city: $('#cityoptions').val(),
            state: $('#stateoptions').val(),
            country: $('#countryoptions').val(),
            latitude: $('#latitude').val(),
            longitude: $('#longitude').val(),
            website_link: $('#website_link').val()
        };
        var corporateAdminUser = {
            corporate_admin_user_id: $('#corporate_admin_user_id').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            dob: $('#dob').val(),
            gender: $('#gender').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            mobile_country_code: $('#mobile_country_code').val(),
            mobile_num: $('#mobile_num').val(),
            aadhar: $('#aadhar').val(),
            age: $('#age').val(),
            active_status: $('input[name="corp_active_status"]:checked').val(),
            super_admin: $('input[name="super_admin"]:checked').val(),
            signup_on: $('#signup_on').val(),
            signup_by: $('#signup_by').val()
        };
        console.log(corporateAdminUser);
        var formData = {
            accountDetails: accountDetails,
            address: address,
            corporateAdminUser: corporateAdminUser
        };
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "/corporate_locations/corporate/locations",
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(
                        'Corporate location added successfully! Redirecting to Corporate list...',
                        'Success', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 3000,
                        fadeOut: 1000,
                        extendedTimeOut: 1000
                    }
                    );
                    setTimeout(function () {
                        window.location.href = "/corporate/corporate-list";
                    }, 1000);
                } else {
                    toastr.error(
                        'Submission failed: ' + response.error,
                        'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                        fadeOut: 1000,
                        extendedTimeOut: 1000,
                        iconClass: 'toast-error'
                    }
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', error);
                console.log(xhr.responseText);
            }
        });
    });
});

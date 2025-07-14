'use strict';

(function () {
  const select2 = $('.select2'),
        selectPicker = $('.selectpicker');

  // Wizard Validation
  // --------------------------------------------------------------------
  const wizardValidation = document.querySelector('#wizard-validation');
  if (wizardValidation) {
    // Wizard form
    const wizardValidationForm = wizardValidation.querySelector('#wizard-validation-form');
    
    // Wizard steps
    const wizardSteps = {
      step1: wizardValidationForm.querySelector('#account-details-validation'),
      step2: wizardValidationForm.querySelector('#personal-info-validation'),
      step3: wizardValidationForm.querySelector('#social-links-validation'),
      step4: wizardValidationForm.querySelector('#components-details-validation'),
      step5: wizardValidationForm.querySelector('#adminuser-details-validation'),
    };

    // Wizard navigation buttons
    const nextButtons = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
    const prevButtons = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));

    const validationStepper = new Stepper(wizardValidation, {
      linear: true
    });

    // Common Plugins Configuration
    const commonPlugins = {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5(),
      autoFocus: new FormValidation.plugins.AutoFocus(),
      submitButton: new FormValidation.plugins.SubmitButton()
    };

    // Step 1: Account Details Validation
    const FormValidation1 = FormValidation.formValidation(wizardSteps.step1, {
      fields: {
        corporate_name: {
          validators: {
            notEmpty: { message: 'The name is required' },
            stringLength: {
              min: 6, max: 30,
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
            notEmpty: { message: 'The display name is required' },
            stringLength: {
              min: 6, max: 30,
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
            notEmpty: { message: 'The start date is required' },
            date: {
              format: 'YYYY-MM-DD',
              message: 'The start date is not valid'
            }
          }
        },
        valid_upto: {
          validators: {
            notEmpty: { message: 'The end date is required' },
            date: {
              format: 'YYYY-MM-DD',
              message: 'The end date is not valid'
            }
          }
        }
      },
      plugins: commonPlugins
    }).on('core.form.valid', () => validationStepper.next());

    // Step 2: Personal Info Validation
    const FormValidation2 = FormValidation.formValidation(wizardSteps.step2, {
      fields: {
        formValidationCountry: {
          validators: {
            notEmpty: { message: 'Pincode is required' },
            regexp: {
              regexp: /^[1-9][0-9]{5}$/,
              message: 'Pincode must be a valid 6-digit number'
            }
          }
        }
      },
      plugins: commonPlugins
    }).on('core.form.valid', () => validationStepper.next());

    // Step 3: Social Links Validation
    const FormValidation3 = FormValidation.formValidation(wizardSteps.step3, {
      fields: {
        'employee_type_name[]': {
          validators: {
            callback: {
              message: 'Please enter at least one Employee Type Name',
              callback: function (input) {
                const employeeTypeFields = document.querySelectorAll('input[name="employee_type_name[]"]');
                return Array.from(employeeTypeFields).some((field) => field.value.trim() !== '');
              }
            }
          }
        }
      },
      plugins: commonPlugins
    }).on('core.form.valid', () => validationStepper.next());

    // Step 4: Components Details Validation
    const FormValidation4 = FormValidation.formValidation(wizardSteps.step4, {
      fields: {
        'module_id[]': {
          validators: {
            callback: {
              message: 'Please select at least one module or submodule.',
              callback: function () {
                const moduleCheckboxes = document.querySelectorAll('input[name="module_id[]"]:checked');
                const submoduleCheckboxes = document.querySelectorAll('input[name^="sub_module_id"]:checked');
                return moduleCheckboxes.length > 0 || submoduleCheckboxes.length > 0;
              }
            }
          }
        }
      },
      plugins: {
        ...commonPlugins,
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          rowSelector: '.module-container'
        })
      }
    }).on('core.form.valid', () => validationStepper.next());

    // Step 5: Final Validation
    const FormValidation5 = FormValidation.formValidation(wizardSteps.step5, {
      fields: {
        first_name: {
          validators: {
            notEmpty: { message: 'First Name is required.' }
          }
        },
        email: {
          validators: {
            notEmpty: { message: 'Email is required.' },
            emailAddress: { message: 'Please enter a valid email address.' }
          }
        },
        password: {
          validators: {
            notEmpty: { message: 'Password is required.' },
            stringLength: {
              min: 8,
              message: 'Password must be at least 8 characters long.'
            }
          }
        },
        aadhar: {
          validators: {
            notEmpty: { message: 'Aadhar is required.' },
            stringLength: {
              min: 12, max: 12,
              message: 'Aadhar must be 12 digits.'
            },
            numeric: { message: 'Only numeric values are allowed.' }
          }
        }
      },
      plugins: commonPlugins
    }).on('core.form.valid', () => {
      alert('All steps are valid. You can now submit the form!');
      wizardValidationForm.submit();
    });

    // Handle Next Button Clicks
    nextButtons.forEach((button, index) => {
      button.addEventListener('click', () => {
        switch (index) {
          case 0: FormValidation1.validate(); break;
          case 1: FormValidation2.validate(); break;
          case 2: FormValidation3.validate(); break;
          case 3: FormValidation4.validate(); break;
          case 4: FormValidation5.validate(); break;
        }
      });
    });

    // Handle Previous Button Clicks
    prevButtons.forEach(button => {
      button.addEventListener('click', () => validationStepper.previous());
    });
  }
})();

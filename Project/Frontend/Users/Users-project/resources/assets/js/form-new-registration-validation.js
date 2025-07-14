/**
 *  Form Wizard
 */
'use strict';
document.addEventListener('DOMContentLoaded', function () {
  (function () {
    const select2 = $('.select2'),
      selectPicker = $('.selectpicker');
    const wizardValidation = document.querySelector('#wizard-validation');
    if (typeof wizardValidation !== undefined && wizardValidation !== null) {
      const wizardValidationForm = wizardValidation.querySelector('#wizard-validation-form');
      const wizardValidationFormStep1 = wizardValidationForm.querySelector('#account-details-validation');
      const wizardValidationFormStep2 = wizardValidationForm.querySelector('#personal-info-validation');
      const wizardValidationFormStep3 = wizardValidationForm.querySelector('#social-links-validation');
      const wizardValidationNext = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
      const wizardValidationPrev = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));
      const validationStepper = new Stepper(wizardValidation, {
        linear: true
      });
      const FormValidation1 = FormValidation.formValidation(wizardValidationFormStep1, {
        fields: {
          formValidationFirstName: {
            validators: {
              notEmpty: {
                message: 'The first name is required'
              },
              stringLength: {
                min: 3,
                max: 30,
                message: 'The name must be more than 3 and less than 30 characters long'
              },
              regexp: {
                regexp: /^[a-zA-Z0-9 ]+$/,
                message: 'The name can only consist of alphabetical, number and space'
              }
            }
          },
          formValidationLastName: {
            validators: {
              notEmpty: {
                message: 'The last name is required'
              },
              stringLength: {
                min: 3,
                max: 30,
                message: 'The name must be more than 3 and less than 30 characters long'
              },
              regexp: {
                regexp: /^[a-zA-Z0-9 ]+$/,
                message: 'The name can only consist of alphabetical, number and space'
              }
            }
          },
          formValidationEmail: {
            validators: {
              notEmpty: {
                message: 'The Email is required'
              },
              emailAddress: {
                message: 'The value is not a valid email address'
              }
            }
          },
          formValidationPassword: {
            validators: {
              notEmpty: {
                message: 'The password is required'
              }
            }
          },
          formValidationSelect2EType: {
            validators: {
              notEmpty: {
                message: 'The employee type is required'
              }
            }
          },
          formValidationSelect2Gender: {
            validators: {
              notEmpty: {
                message: 'Please select a gender'
              }
            }
          },
          formValidationDOB: {
            validators: {
              notEmpty: {
                message: 'The dob field is required'
              }
            }
          },
          formValidationConfirmPass: {
            validators: {
              notEmpty: {
                message: 'The Confirm Password is required'
              },
              identical: {
                compare: function () {
                  return wizardValidationFormStep1.querySelector('[name="formValidationPassword"]').value;
                },
                message: 'The password and its confirm are not the same'
              }
            }
          },
          formValidationMobileCountryCode: {
            validators: {
              notEmpty: {
                message: 'The country code is required'
              },
              regexp: {
                regexp: /^\+[0-9]{1,4}$/,
                message: 'Country code must start with + followed by 1-4 digits'
              }
            }
          },
          formValidationMobile: {
            validators: {
              notEmpty: {
                message: 'The mobile number is required'
              },
              stringLength: {
                min: 10,
                max: 10,
                message: 'Mobile number must be exactly 10 digits'
              },
              regexp: {
                regexp: /^[0-9]{10}$/,
                message: 'Mobile number must contain only digits'
              }
            }
          },
          formValidationAadhar: {
            validators: {
              stringLength: {
                min: 12,
                max: 12,
                message: 'Aadhar ID must be exactly 12 digits'
              },
              regexp: {
                regexp: /^[0-9]{12}$/,
                message: 'Aadhar ID must contain only digits'
              }
            }
          },
          formValidationabha: {
            validators: {
              stringLength: {
                min: 14,
                max: 14,
                message: 'ABHA ID must be exactly 14 digits'
              },
              regexp: {
                regexp: /^[0-9]{14}$/,
                message: 'ABHA ID must contain only digits'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.col-sm-4'
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        },
        init: instance => {
          instance.on('plugins.message.placed', function (e) {
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });
        }
      }).on('core.form.valid', function () {
        validationStepper.next();
      });
      const FormValidation2 = FormValidation.formValidation(wizardValidationFormStep2, {
        fields: {
          formValidationEmpId: {
            validators: {
              notEmpty: {
                message: 'The Employee Id is required'
              }
            }
          },
          formValidationSelect2EType: {
            validators: {
              notEmpty: {
                message: 'Please select an Employee Type'
              }
            }
          },
          formValidationFromDate: {
            validators: {
              notEmpty: {
                message: 'The From Date is required'
              }
            }
          },
          formValidationDepartment: {
            validators: {
              notEmpty: {
                message: 'The Department is required'
              }
            }
          },
          formValidationDesignation: {
            validators: {
              notEmpty: {
                message: 'The Designation is required'
              }
            }
          },
          formValidationContractor: {
            validators: {
              callback: {
                message: 'The Contractor is required',
                callback: function (value, validator, $field) {
                  const empType = document.getElementById('formValidationSelect2EType').value;
                  if (empType !== '') {
                    return value !== '';
                  }
                  return true;
                }
              }
            }
          },
          formValidationContractorWorkerId: {
            validators: {
              callback: {
                message: 'The Contractor Worker Id is required',
                callback: function (value, validator, $field) {
                  const empType = document.getElementById('formValidationSelect2EType').value;
                  if (empType !== '') {
                    return value !== '';
                  }
                  return true;
                }
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.col-sm-4'
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        }
      }).on('core.form.valid', function () {
        validationStepper.next();
      });
      if (selectPicker.length) {
        selectPicker.each(function () {
          var $this = $(this);
          $this.selectpicker().on('change', function () {
            FormValidation2.revalidateField('formValidationLanguage');
          });
        });
      }
      if (select2.length) {
        select2.each(function () {
          var $this = $(this);
          $this.wrap('<div class="position-relative"></div>');
          $this
            .select2({
              placeholder: 'Select an gender',
              dropdownParent: $this.parent()
            })
            .on('change', function () {
              FormValidation2.revalidateField('formValidationCountry');
            });
        });
      }
      // Get necessary elements
      const empTypeSelect = document.getElementById('formValidationSelect2EType');
      const contractorDetailsDiv = document.getElementById('contractorDetails');
      const contractorInput = document.getElementById('formValidationContractor');
      const contractorWorkerIdInput = document.getElementById('formValidationContractorWorkerId');

      empTypeSelect.addEventListener('change', function () {
        const selectedValue = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const isChecked = selectedOption.getAttribute("data-checked") === "1"; // Convert string to boolean

        if (isChecked) {
          contractorDetailsDiv.classList.add('show');
          contractorDetailsDiv.classList.remove('hidden');
          contractorInput.disabled = false;
          contractorWorkerIdInput.disabled = false;
          FormValidation2.revalidateField('formValidationContractor');
          FormValidation2.revalidateField('formValidationContractorWorkerId');
        } else {
          contractorDetailsDiv.classList.remove('show');
          setTimeout(() => {
            contractorDetailsDiv.classList.add('hidden');
          }, 500);
          contractorInput.disabled = true;
          contractorWorkerIdInput.disabled = true;
          contractorInput.value = '';
          contractorWorkerIdInput.value = '';
        }
      });
      wizardValidationNext.forEach(item => {
        item.addEventListener('click', event => {
          switch (validationStepper._currentIndex) {
            case 0:
              FormValidation1.validate();
              break;
            case 1:
              FormValidation2.validate();
              break;
            default:
              break;
          }
        });
      });
      wizardValidationPrev.forEach(item => {
        item.addEventListener('click', event => {
          switch (validationStepper._currentIndex) {
            case 2:
              validationStepper.previous();
              break;
            case 1:
              validationStepper.previous();
              break;
            case 0:
            default:
              break;
          }
        });
      });
    }
  })();
});

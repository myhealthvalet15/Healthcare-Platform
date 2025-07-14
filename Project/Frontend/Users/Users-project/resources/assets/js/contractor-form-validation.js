document.addEventListener('DOMContentLoaded', function () {
  const addNewModal = document.querySelector('#addNewModal');

  if (addNewModal) {
      const addNewForm = addNewModal.querySelector('#addNewForm');
      const statusSwitch = addNewForm.querySelector('#add_active_status');
      const statusLabel = addNewForm.querySelector('.status-label');
      const saveChangesButton = document.querySelector('#saveChangesButton');

      // Change status label text on toggle
      if (statusSwitch && statusLabel) {
          statusSwitch.addEventListener('change', function () {
              statusLabel.textContent = statusSwitch.checked ? 'Active' : 'Inactive';
          });
      }

      // Initialize FormValidation for the Add New form
      const FormValidationAddNew = FormValidation.formValidation(addNewForm, {
          fields: {
              contractor_name: {
                  validators: {
                      notEmpty: { message: 'The contractor name is required' },
                      stringLength: { min: 3, max: 50, message: 'Contractor name must be between 3 and 50 characters long' },
                      regexp: { regexp: /^[a-zA-Z0-9 ]+$/, message: 'Contractor name can only contain alphanumeric characters and spaces' }
                  }
              },
              contractor_email: {
                  validators: {
                      notEmpty: { message: 'The contractor email is required' },
                      emailAddress: { message: 'The value is not a valid email address' }
                  }
              },
              contractor_address: {
                  validators: {
                      notEmpty: { message: 'The contractor address is required' }
                  }
              }
             
          },
          plugins: {
              trigger: new FormValidation.plugins.Trigger(),
              bootstrap5: new FormValidation.plugins.Bootstrap5({
                  eleInvalidClass: 'is-invalid',
                  eleValidClass: 'is-valid',
                  rowSelector: '.col-sm-12'
              }),
              autoFocus: new FormValidation.plugins.AutoFocus(),
              submitButton: new FormValidation.plugins.SubmitButton()
          }
      });

      // Handle form submission
      if (saveChangesButton) {
          saveChangesButton.addEventListener('click', function (e) {
              e.preventDefault(); // Prevent the default form submission

              // Validate the form before submission
              FormValidationAddNew.validate().then(function (status) {
                  if (status === 'Invalid') {
                      console.warn('Form validation failed. Please check the form for errors.');
                      return;
                  }

                  // If form is valid, submit via AJAX
                  const formData = new FormData(addNewForm); // Serialize form data
                  formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token

                  $.ajax({
                      url: addNewForm.getAttribute('action'),
                      method: 'POST',
                      data: formData,
                      processData: false,  // Don't process the data
                      contentType: false,  // Don't set contentType
                      success: function (response) {
                        showToast("success", "Contractor Added successfully!");
                          location.reload(); // Reload the page to reflect the changes
                      },
                      error: function (xhr, error) {
                          //console.error('Error saving contractor:', error);
                          showToast('Failed to add contractor. Please try again.');
                      }
                  });
              });
          });
      }
  }
});
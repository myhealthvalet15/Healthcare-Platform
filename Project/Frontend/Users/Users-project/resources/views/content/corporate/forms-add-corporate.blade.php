@extends('layouts/layoutMaster')
@section('title', 'New Registration')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-new-registration-validation.js'
])
@endsection
@section('content')
<style>
  #contractorDetails {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.5s ease, opacity 0.5s ease;
  }

  #contractorDetails.show {
    max-height: 500px;
    opacity: 1;
  }

  .hidden {
    display: none;
  }
</style>
<!-- Default -->
<div class="row">
  <!-- Validation Wizard -->
  <div class="col-12 mb-6">
    <small class="text-light fw-medium">Add New Users</small>
    <div id="wizard-validation" class="bs-stepper mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details-validation">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">Account Details</span>
              <span class="bs-stepper-subtitle">Setup Account Details</span>
            </span>
          </button>
        </div>
        <div class="line">
          <i class="ti ti-chevron-right"></i>
        </div>
        <div class="step" data-target="#personal-info-validation">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">Employeement Details</span>
              <span class="bs-stepper-subtitle">Enter Users Employeement
                details.</span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form id="wizard-validation-form" onSubmit="return false">
          <!-- Account Details -->
          <div id="account-details-validation" class="content">
            <div class="content-header mb-4">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Users Personal Details</small>
            </div>
            <div class="row g-6">
              <div class="col-sm-4">
                <label class="form-label" for="formValidationFirstName">first
                  name</label>
                <input type="text" name="formValidationFirstName" id="formValidationFirstName" class="form-control"
                  placeholder="johndoe" />
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationLastName">last
                  name</label>
                <input type="text" name="formValidationLastName" id="formValidationLastName" class="form-control"
                  placeholder="johndoe" />
              </div>
              <div class="col-sm-4">
                <div class="row g-2">
                  <!-- Gender Dropdown -->
                  <div class="col-md-6">
                    <label class="form-label" for="formValidationSelect2Gender">Select Gender</label>
                    <select id="formValidationSelect2Gender" name="formValidationSelect2Gender"
                      class="form-select select2" data-allow-clear="true">
                      <option value>Select</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Others">Others</option>
                    </select>
                  </div>
                  <!-- Date of Birth Input -->
                  <div class="col-sm-6">
                    <label for="formValidationDOB" class="form-label">Date of
                      Birth</label>
                    <input type="date" id="formValidationDOB" name="formValidationDOB" class="form-control" />
                  </div>
                </div>
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationEmail">Email</label>
                <input type="email" name="formValidationEmail" id="formValidationEmail" class="form-control"
                  placeholder="john.doe@email.com" aria-label="john.doe" />
              </div>
              <div class="col-sm-4 form-password-toggle">
                <label class="form-label" for="formValidationPassword">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="formValidationPassword" name="formValidationPassword" class="form-control"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="formValidationPassword2" />
                  <span class="input-group-text cursor-pointer" id="formValidationPassword2"><i
                      class="ti ti-eye-off"></i></span>
                </div>
              </div>
              <div class="col-sm-4 form-password-toggle">
                <label class="form-label" for="formValidationConfirmPass">Confirm Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="formValidationConfirmPass" name="formValidationConfirmPass"
                    class="form-control"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="formValidationConfirmPass2" />
                  <span class="input-group-text cursor-pointer" id="formValidationConfirmPass2"><i
                      class="ti ti-eye-off"></i></span>
                </div>
              </div>
              <div class="col-sm-4">
                <label class="form-label">Mobile</label>
                <div class="d-flex">
                  <div class="me-2" style="flex: 0 0 100px;">
                    <input type="text" name="formValidationMobileCountryCode" id="formValidationMobileCountryCode"
                      class="form-control" placeholder="+91" />
                  </div>
                  <div style="flex: 1;">
                    <input type="text" name="formValidationMobile" id="formValidationMobile" class="form-control"
                      placeholder="Phone no." />
                  </div>
                </div>
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationAadhar">aadhar
                  id</label>
                <input type="text" name="formValidationAadhar" id="formValidationAadhar" class="form-control"
                  placeholder="aadhar id" />
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationabha">abha
                  id</label>
                <input type="text" name="formValidationabha" id="formValidationabha" class="form-control"
                  placeholder="abha id" />
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-label-secondary btn-prev" disabled> <i
                    class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span
                    class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
                  <i class="ti ti-arrow-right ti-xs"></i></button>
              </div>
            </div>
          </div>
          <!-- Personal Info -->
          <div id="personal-info-validation" class="content">
            <div class="content-header mb-4">
              <h6 class="mb-0">Employment Details</h6>
              <small>Enter Users Employment details.</small>
            </div>
            <div class="row g-6">
              <div class="col-sm-4">
                <label class="form-label" for="formValidationEmpId">Employee
                  ID</label>
                <input type="text" id="formValidationEmpId" name="formValidationEmpId" class="form-control"
                  placeholder="emp id" />
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationSelect2EType">Employee Type</label>
                <select id="formValidationSelect2EType" name="formValidationSelect2EType" class="form-control">
                  <option value disabled selected>Select Employee Type</option>
                </select>
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationFromDate">From
                  Date</label>
                <input type="date" id="formValidationFromDate" name="formValidationFromDate" class="form-control"
                  placeholder="from date" />
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationDepartment">Department</label>
                <select id="formValidationDepartment" name="formValidationDepartment" class="form-control">
                  <option value disabled selected>Select Department</option>
                </select>
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationDesignation">Designation</label>
                <input type="text" id="formValidationDesignation" name="formValidationDesignation" class="form-control"
                  placeholder="designation" />
              </div>
              <div class="col-sm-4">
                <label class="form-label" for="formValidationOtherId">Other
                  ID</label>
                <input type="text" id="formValidationOtherId" name="formValidationOtherId" class="form-control"
                  placeholder="other id" />
              </div>
              <div id="contractorDetails" class="row g-6 hidden">
                <div class="col-sm-4">
                  <label class="form-label" for="formValidationContractor">Contractor</label>
                  <select id="formValidationContractor" name="formValidationContractor" class="form-control">
                    <option value disabled selected>Select Contractor</option>
                  </select>
                </div>
                <div class="col-sm-4">
                  <label class="form-label" for="formValidationContractorWorkerId">Contract Worker
                    ID</label>
                  <input type="text" id="formValidationContractorWorkerId" name="formValidationContractorWorkerId"
                    class="form-control" placeholder="Contract Worker ID" disabled />
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-success btn-next btnsubmit-button" id="submit-button">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Validation Wizard -->
</div>
<script src="/lib/js/page-scripts/common.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    populateEmployeeTypeDropdown();
    populateDepartmentDropdown();
    populateContractorsDropdown();
    let employeeTypeData = [];
    function populateEmployeeTypeDropdown() {
      const employeeTypeDropdown = document.getElementById("formValidationSelect2EType");
      apiRequest({
        url: "https://login-users.hygeiaes.com/corporate/getEmployeeType",
        method: 'GET',
        onSuccess: (data) => {
          if (data.data.result === true) {
            employeeTypeData = data.data.data;
            employeeTypeData.forEach(type => {
              const option = document.createElement("option");
              option.value = type.employee_type_id;
              option.textContent = type.employee_type_name;
              option.setAttribute("data-id", type.employee_type_id);
              option.setAttribute("data-checked", type.checked);
              employeeTypeDropdown.appendChild(option);
            });
          }
        },
        onError: (error) => {
          console.error("Error fetching employee types:", error);
        }
      });
    }
    function populateDepartmentDropdown() {
      const departmentDropdown = document.getElementById("formValidationDepartment");
      apiRequest({
        url: "https://login-users.hygeiaes.com/corporate/getDepartments",
        method: 'GET',
        onSuccess: (data) => {
          if (data.data.result === true) {
            const departments = data.data.data;
            departments.forEach(department => {
              const option = document.createElement("option");
              option.value = department.hl1_id;
              option.textContent = department.hl1_name;
              option.setAttribute("data-id", department.hl1_id);
              departmentDropdown.appendChild(option);
            });
          }
        },
        onError: (error) => {
          console.error("Error fetching departments:", error);
        }
      });
    }
    function populateContractorsDropdown() {
      const contractorDropdown = document.getElementById("formValidationContractor");
      apiRequest({
        url: "https://login-users.hygeiaes.com/corporate/getCorporateContractors",
        method: 'GET',
        onSuccess: (data) => {
          if (data.result === "success" && data.data.result === true) {
            const contractors = data.data.data;
            contractors.forEach(contractor => {
              const option = document.createElement("option");
              option.value = contractor.corporate_contractors_id;
              option.textContent = contractor.contractor_name;
              option.setAttribute("data-id", contractor.corporate_contractors_id);
              contractorDropdown.appendChild(option);
            });
          }
        },
        onError: (error) => {
          console.error("Error fetching contractors:", error);
        }
      });
    }
    const submitButton = document.getElementById('submit-button');
    const form = document.getElementById('wizard-validation-form');
    const corporateId = document.getElementById('corporate_id');
    const locationId = document.getElementById('location_id');
    if (submitButton) {
      submitButton.addEventListener('click', function (event) {
        event.preventDefault();
        const employeeTypeDropdown = document.getElementById("formValidationSelect2EType");
        const departmentDropdown = document.getElementById("formValidationDepartment");
        const contractorDropdown = document.getElementById("formValidationContractor");
        const selectedEmployeeTypeId = employeeTypeDropdown.options[employeeTypeDropdown.selectedIndex].getAttribute("data-id");
        const selectedDepartmentId = departmentDropdown.options[departmentDropdown.selectedIndex].getAttribute("data-id");
        const selectedContractorId = contractorDropdown.options[contractorDropdown.selectedIndex].getAttribute("data-id");
        const formData = new FormData(form);
        formData.set("formValidationSelect2EType", selectedEmployeeTypeId);
        formData.set("formValidationDepartment", selectedDepartmentId);
        formData.set("formValidationContractor", selectedContractorId);
        const data = {};
        formData.forEach((value, key) => {
          data[key] = value;
        });
        const errors = validateFormData(data);
        if (errors.length > 0) {
          showToast('error', 'Error', errors.join(', '));
          return;
        }
        apiRequest({
          url: '/corporate/upload/add-corporate-user',
          method: 'POST',
          data: data,
          onSuccess: (responseData) => {
            showToast('success', 'Success', 'Data submitted successfully!');
            setTimeout(() => {
              location.reload();
            }, 1000);
          },
          onError: (errorMessage) => {
            showToast('error', 'Error', errorMessage);
          }
        });
      });
    }
    function validateFormData(data) {
      const errors = [];
      if (!data.formValidationFirstName || data.formValidationFirstName.trim() === '') {
        errors.push('First Name is required.');
      }
      if (!data.formValidationLastName || data.formValidationLastName.trim() === '') {
        errors.push('Last Name is required.');
      }
      if (!['Male', 'Female', 'Others'].includes(data.formValidationSelect2Gender)) {
        errors.push('Gender is required and should be Male, Female or Others.');
      }
      if (!data.formValidationDOB || !isValidDate(data.formValidationDOB)) {
        errors.push('Valid Date of Birth is required.');
      }
      if (!data.formValidationEmail || !isValidEmail(data.formValidationEmail)) {
        errors.push('A valid Email is required.');
      }
      if (!data.formValidationMobile || !isValidMobile(data.formValidationMobile)) {
        errors.push('A valid Mobile Number is required.');
      }
      if (data.formValidationAadhar && !isValidAadhar(data.formValidationAadhar)) {
        errors.push('Invalid Aadhar ID.');
      }
      if (data.formValidationabha && !isValidAbha(data.formValidationabha)) {
        errors.push('Invalid Abha ID.');
      }
      if (!data.formValidationEmpId || data.formValidationEmpId.trim() === '') {
        errors.push('Employee ID is required.');
      }
      if (!data.formValidationSelect2EType) {
        errors.push('Employee Type is required.');
      }
      if (data.formValidationSelect2EType) {
        if (!data.formValidationContractorWorkerId) {
          errors.push('Contract worker id is required, when employee type is selected');
        }
        if (!data.formValidationContractor) {
          errors.push('Contract is required, when employee type is selected');
        }
      }
      if (data.formValidationSelect2EType === 'contractor' && !data.formValidationContractorWorkerId) {
        errors.push('Contractor Worker ID is required.');
      }
      if (!data.formValidationFromDate || !isValidDate(data.formValidationFromDate)) {
        errors.push('Valid From Date is required.');
      }
      if (!data.formValidationDepartment || data.formValidationDepartment.trim() === '') {
        errors.push('Department is required.');
      }
      if (!data.formValidationDesignation || data.formValidationDesignation.trim() === '') {
        errors.push('Designation is required.');
      }
      const corporateId = data.corporate_id;
      const locationId = data.location_id;
      if (!corporateId || typeof corporateId !== 'string') {
        errors.push('Corporate ID must be a non-empty string.');
      }
      if (!locationId || typeof locationId !== 'string') {
        errors.push('Location ID must be a non-empty string.');
      }
      return errors;
    }
    function isValidDate(date) {
      return !isNaN(Date.parse(date));
    }
    function isValidEmail(email) {
      const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
      return regex.test(email);
    }
    function isValidMobile(mobile) {
      const regex = /^[0-9]{10}$/;
      return regex.test(mobile);
    }
    function isValidAadhar(aadhar) {
      return /^[0-9]{12}$/.test(aadhar);
    }
    function isValidAbha(abha) {
      return /^[0-9]{14}$/.test(abha);
    }
  });
</script>
@endsection
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

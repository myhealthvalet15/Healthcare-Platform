
$(document).ready(function () {
    fetchEmployeeDetails(employeeId);

});
function capitalizeFirstLetter(string) {
    if (typeof string !== 'string' || !string.length) return '';
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
function fetchEmployeeDetails(employeeId) {
    const url = `${employeeDetailsUrl}?employee_id=${employeeId}`;
    apiRequest({
        url: url,
        method: "GET",
        onSuccess: (data) => {
            if (data && data.employee_id) {
                document.getElementById("empId").textContent = data.employee_id;
                document.getElementById("empName").textContent = `${data.employee_firstname} ${data.employee_lastname}`;
                document.getElementById("empAge").textContent = data.employee_age;
                document.getElementById("empGender").textContent = capitalizeFirstLetter(data.employee_gender);
                document.getElementById("empDepartment").textContent = capitalizeFirstLetter(data.employee_department);
                document.getElementById("empDesignation").textContent = capitalizeFirstLetter(data.employee_designation);
                document.getElementById("empType").textContent = capitalizeFirstLetter(data.employee_type_name);
                document.getElementById("empdateOfJoining").textContent = data.dateOfJoining;
                const summaryCard = document.getElementById("employeeSummaryCard");
                if (summaryCard) {
                    summaryCard.style.display = "block";
                    summaryCard.classList.add("animate__animated", "animate__fadeIn");
                }
            } else {
                showErrorInTable('Invalid data format received');
            }
            if (data.result && data.data && Array.isArray(data.data)) {
                populateTable(data.data, filters);
            }
        },
        onError: (error) => {
            console.error('Error fetching employee details:', error);
            showErrorInTable('Error loading data: ' + error.message);
        }
    });
}

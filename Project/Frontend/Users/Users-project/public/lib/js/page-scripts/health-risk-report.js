document.addEventListener('DOMContentLoaded', function () {
  loadMedicalConditions();
  userdata(); // Draw chart
  loadEmployeeTypes();
  loadDepartments();

});
let areaChartInstance = null;

function userdata() {
  const ctx = document.getElementById('eventBarChart').getContext('2d');

  const employeeType = document.getElementById('jobvalue').value;
  const medicalCondition = document.getElementById('medtest').value;
  const department = document.getElementById('department').value;
  const ageGroup = document.getElementById('age').value;
  const fromDate = document.getElementById('frm').value;
  const toDate = document.getElementById('to').value;
  const reportType = document.querySelector('select[name="reppag"]').value;

  fetch('/mhc/report/health-data', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      employeeType,
      medicalCondition,
      department,
      ageGroup,
      fromDate,
      toDate,
      reportType
    })
  })
  .then(response => response.json())
  .then(response => {
    if (!response.result) {
      console.error('Failed to fetch chart data.');
      return;
    }

    const labels = response.labels;
    const lowData = response.datasets.low;
    const normalData = response.datasets.normal;
    const highData = response.datasets.high;

    const barChartData = {
      labels: labels,
      datasets: [
        {
          label: 'Low',
          data: lowData,
          backgroundColor: '#FFBABA',
          stack: 'health'
        },
        {
          label: 'Normal',
          data: normalData,
          backgroundColor: '#B9FBC0',
          stack: 'health'
        },
        {
          label: 'High',
          data: highData,
          backgroundColor: '#FFF3B0',
          stack: 'health'
        }
      ]
    };

    const barChartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: true,
          text: 'Health Risk Distribution',
          font: { size: 18, weight: '600' },
          padding: { bottom: 10 }
        },
        tooltip: {
          callbacks: {
            label: context => `${context.dataset.label}: ${context.raw}`
          }
        },
        legend: { display: false }
      },
      scales: {
        x: {
          stacked: true,
          title: {
            display: true,
            text: 'Age Group'
          }
        },
        y: {
          stacked: true,
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Employees'
          }
        }
      }
    };

    document.getElementById('range-info').innerHTML = `
      <span style="color:#dc3545;">Low:</span> Below normal range &nbsp; |
      <span style="color:#28a745;">Normal:</span> Within normal range &nbsp; |
      <span style="color:#ffc107;">High:</span> Above normal range
    `;

    if (areaChartInstance) areaChartInstance.destroy();

    areaChartInstance = new Chart(ctx, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    });
  })
  .catch(error => {
    console.error('Chart update failed:', error);
  });
}


function loadMedicalConditions() {
  apiRequest({
    url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
    method: 'GET',
    onSuccess: function (response) {
      if (response.result && response.data && Array.isArray(response.data.subgroups)) {
        const medtest = document.getElementById('medtest');
        medtest.innerHTML = ''; // Clear existing options
        const allTests = new Set();

        response.data.subgroups.forEach(subgroup => {
          // Direct tests
          if (Array.isArray(subgroup.tests)) {
            subgroup.tests.forEach(test => addUniqueOption(test));
          }

          // Nested sub-subgroups
          if (Array.isArray(subgroup.subgroups)) {
            subgroup.subgroups.forEach(subSubgroup => {
              if (Array.isArray(subSubgroup.tests)) {
                subSubgroup.tests.forEach(test => addUniqueOption(test));
              }
            });
          }
        });

        function addUniqueOption(test) {
          if (!allTests.has(test.master_test_id)) {
            const option = document.createElement('option');
            option.value = test.master_test_id;
            option.text = test.test_name; // ✅ Corrected here
            medtest.appendChild(option);
            allTests.add(test.master_test_id);
          }
        }

        // Optional: Re-initialize select2
        if (window.jQuery && jQuery().select2) {
          $('#medtest').select2({
            placeholder: 'Select a medical condition',
            allowClear: true,
            width: '100%'
          });
        }

        // Initial chart update with first available test
        userdata();
      } else {
        console.warn('Unexpected API format:', response);
      }
    },
    onError: function (error) {
      console.error('API Error:', error);
    }
  });
}
function loadEmployeeTypes() {
  apiRequest({
    url: '/corporate/getEmployeeType', // your Laravel route
    method: 'GET',
    onSuccess: function (response) {
      const empSelect = document.getElementById('jobvalue');
      empSelect.innerHTML = '';

      // Add default "All" option
      const allOption = document.createElement('option');
      allOption.value = '0';
      allOption.textContent = 'All Employees';
      empSelect.appendChild(allOption);

      if (response.result && Array.isArray(response.data)) {
        response.data.forEach(type => {
          const option = document.createElement('option');
          option.value = type.employee_type_id;
          option.textContent = type.employee_type_name;
          empSelect.appendChild(option);
        });

        // Reinitialize Select2
        if (window.jQuery && jQuery().select2) {
          $('#jobvalue').select2({
            placeholder: 'Select Employee Type',
            allowClear: true,
            width: '100%'
          });
        }

        userdata(); // trigger chart refresh
      } else {
        console.warn('Unexpected employee type response:', response);
      }
    },
    onError: function (error) {
      console.error('Failed to load employee types:', error);
    }
  });
}
function loadDepartments() {
  apiRequest({
    url: '/corporate/getDepartments',
    method: 'GET',
    onSuccess: function (response) {
      const deptSelect = document.getElementById('department');
      deptSelect.innerHTML = '';

      // Default "All" option
      const defaultOption = document.createElement('option');
      defaultOption.value = '0';
      defaultOption.textContent = 'All Departments';
      deptSelect.appendChild(defaultOption);

      if (response.result && Array.isArray(response.data)) {
        response.data.forEach(dept => {
          const option = document.createElement('option');
          option.value = dept.hl1_id ?? dept.id; // adjust key names if needed
          option.textContent = dept.hl1_name ?? dept.name; // adjust key names if needed
          deptSelect.appendChild(option);
        });

        // Re-init Select2
        if (window.jQuery && jQuery().select2) {
          $('#department').select2({
            placeholder: 'Select Department',
            allowClear: true,
            width: '100%'
          });
        }

        userdata(); // refresh chart
      } else {
        console.warn('Unexpected response structure for departments:', response);
      }
    },
    onError: function (error) {
      console.error('Failed to load departments:', error);
    }
  });
}

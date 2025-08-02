let areaChartInstance = null;

function userdata() {
  const ctx = document.getElementById('eventBarChart').getContext('2d');

  // Dummy sample data — replace with AJAX or dynamic logic
  const labels = ['20-30', '30-40', '40-50', '50+'];
  const lowData = [15, 40, 1, 0];
  const normalData = [88, 285, 13, 10];
  const highData = [1, 4, 0, 0];

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
        text: 'Age-wise Distribution of Health Parameters',
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
//document.addEventListener('DOMContentLoaded', userdata);
document.addEventListener('DOMContentLoaded', function () {
  loadMedicalConditions();
  loadEmployeeType();
  loadDepartment();
  loadLocation();
  userdata(); // Draw chart
});
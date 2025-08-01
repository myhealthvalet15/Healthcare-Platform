let chartInstance = null;

function userdata() {
  const ctx = document.getElementById('eventChart').getContext('2d');

  const testId = document.getElementById('medtest').value;

  const dataMap = {
    '51': {
      label: 'RBC Count',
      data: [4.1, 4.3, 4.0, 4.2, 4.5, 4.6, 4.4],
      borderColor: '#42a5f5',
      backgroundColor: 'rgba(66, 165, 245, 0.4)'
    },
    '52': {
      label: 'Haemoglobin',
      data: [13.5, 13.2, 13.8, 13.6, 14.0, 13.9, 14.1],
      borderColor: '#66bb6a',
      backgroundColor: 'rgba(102, 187, 106, 0.4)'
    },
    '53': {
      label: 'Hct (PCV)',
      data: [42, 43, 41, 44, 45, 46, 44],
      borderColor: '#ffa726',
      backgroundColor: 'rgba(255, 167, 38, 0.4)'
    }
  };

  const selected = dataMap[testId];
  const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];

  const chartData = {
    labels: labels,
    datasets: [
      {
        label: selected.label,
        data: selected.data,
        fill: true,
        tension: 0.3,
        borderColor: selected.borderColor,
        backgroundColor: selected.backgroundColor,
        pointBackgroundColor: selected.borderColor,
        pointRadius: 5,
        pointHoverRadius: 6
      }
    ]
  };

  const chartOptions = {
    responsive: true,
    plugins: {
      legend: { position: 'top' },
      title: {
        display: true,
        text: 'Health Condition Trend - ' + selected.label
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  };

  if (chartInstance) {
    chartInstance.destroy();
  }

  chartInstance = new Chart(ctx, {
    type: 'line', // still a line chart
    data: chartData,
    options: chartOptions
  });
}

document.addEventListener('DOMContentLoaded', function () {
  userdata();
});

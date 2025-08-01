let chartInstance = null;

function userdata() {
  const ctx = document.getElementById('eventChart').getContext('2d');
  const testId = document.getElementById('medtest').value;

  const dataMap = {
    '51': {
      label: 'RBC Count',
      data: [3.8, 4.3, 3.3, 4.2, 4.5, 4.6, 4.8],
      normal: [4.0, 4.6],
      high: 4.8
    },
    '52': {
      label: 'Haemoglobin',
      data: [11.5, 13.2, 13.8, 13.6, 14.0, 13.9, 15.8],
      normal: [12.5, 15.5],
      high: 16.0
    },
    '53': {
      label: 'Hct (PCV)',
      data: [36, 43, 41, 44, 45, 46, 48],
      normal: [38, 47],
      high: 49
    }
  };

  const selected = dataMap[testId];
  const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];

  const barColors = selected.data.map(val => {
    if (val < selected.normal[0]) return '#FFBABA';
    if (val > selected.normal[1]) return '#FFF3B0';
    return '#B9FBC0';
  });

  const chartData = {
    labels: labels,
    datasets: [{
      label: selected.label,
      data: selected.data,
      backgroundColor: barColors,
      borderRadius: 6,
      borderSkipped: false,
      barPercentage: 0.65,
      categoryPercentage: 0.65
    }]
  };

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      title: {
        display: true,
        text: selected.label + ' Trend Over Months',
        font: { size: 18, weight: '600' },
        padding: { bottom: 15 }
      },
      tooltip: {
        callbacks: {
          label: context => `${selected.label}: ${context.raw}`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        suggestedMax: selected.high + 2,
        ticks: {
          stepSize: 1
        },
        grid: {
          color: 'rgba(0, 0, 0, 0.05)'
        }
      },
      x: {
        grid: { display: false }
      }
    }
  };
// Set range description
const rangeInfo = document.getElementById('range-info');
rangeInfo.innerHTML = `
  <span style="color:#dc3545;">Low:</span> Less than ${selected.normal[0]} &nbsp; | 
  <span style="color:#28a745;">Normal:</span> ${selected.normal[0]} to ${selected.normal[1]} &nbsp; | 
  <span style="color:#ffc107;">High:</span> More than ${selected.normal[1]}
`;

  if (chartInstance) chartInstance.destroy();

  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: chartOptions
  });
}

document.addEventListener('DOMContentLoaded', userdata);

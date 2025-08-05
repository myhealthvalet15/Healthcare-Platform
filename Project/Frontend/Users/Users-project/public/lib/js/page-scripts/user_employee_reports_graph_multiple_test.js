const masterUserId = "{{ session('master_user_user_id') }}";
document.addEventListener('DOMContentLoaded', () => {
    const selectElement = document.getElementById('select2Primary_tests');
    apiRequest({
        url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
        method: 'GET',
        onSuccess: function (response) {
            if (response.result && response.data && Array.isArray(response.data.subgroups)) {
                const allTests = new Set();
                response.data.subgroups.forEach(subgroup => {
                    if (Array.isArray(subgroup.tests)) {
                        subgroup.tests.forEach(test => {
                            if (!allTests.has(test.master_test_id)) {
                                addOption(test);
                                allTests.add(test.master_test_id);
                            }
                        });
                    }
                    if (Array.isArray(subgroup.subgroups)) {
                        subgroup.subgroups.forEach(subSubgroup => {
                            if (Array.isArray(subSubgroup.tests)) {
                                subSubgroup.tests.forEach(test => {
                                    if (!allTests.has(test
                                        .master_test_id)) {
                                        addOption(test);
                                        allTests.add(test
                                            .master_test_id);
                                    }
                                });
                            }
                        });
                    }
                });
                const staticOption = document.createElement('option');
                staticOption.value = 'DIFF_COUNT_STATIC';
                staticOption.textContent = 'Differential Count (static)';
                selectElement.appendChild(staticOption);
                const staticOptionBP = document.createElement('option');
                staticOptionBP.value = 'BP_STATIC';
                staticOptionBP.textContent = 'Blood Pressure (static)';
                selectElement.appendChild(staticOptionBP);
                const staticOptionLFT = document.createElement('option');
                staticOptionLFT.value = 'LFT_BILIRUBIN_STATIC';
                staticOptionLFT.textContent = 'Liver Function Test - Bilirubin (static)';
                selectElement.appendChild(staticOptionLFT);
                if (window.jQuery && window.jQuery().select2) {
                    $('#select2Primary_tests').select2({
                        placeholder: 'Select a test',
                        allowClear: true,
                        width: '100%',
                    });
                }
            } else {
                console.warn('Unexpected API format:', response);
            }
        },
        onError: function (error) {
            console.error('API Error:', error);
        }
    });
    function addOption(test) {
        const option = document.createElement('option');
        option.value = test.master_test_id;
        option.textContent = test.test_name;
        selectElement.appendChild(option);
    }
});
document.getElementById('select2Primary_tests').addEventListener('change', function () {
    const val = this.value;
    if (!val || !masterUserId) return;
    if (val === 'DIFF_COUNT_STATIC') {
        const staticData = [{
            test_date: '2017-01-04',
            Neutrophils: 60,
            Lymphocyte: 30,
            Eosinophil: 3,
            Basophil: 1
        },
        {
            test_date: '2019-06-05',
            Neutrophils: 65,
            Lymphocyte: 28,
            Eosinophil: 4,
            Basophil: 2
        }
        ];
        updateChartWithData(staticData, 'differential');
    } else if (val === 'BP_STATIC') {
        const staticBPData = [{
            test_date: '2017-01-04',
            systolic: 120,
            diastolic: 80
        },
        {
            test_date: '2019-06-05',
            systolic: 120,
            diastolic: 80
        },
        {
            test_date: '2023-01-05',
            systolic: 130,
            diastolic: 90
        },
        {
            test_date: '2025-06-19',
            systolic: 135,
            diastolic: 95
        }
        ];
        updateChartWithBPData(staticBPData);
    } else if (val === 'LFT_BILIRUBIN_STATIC') {
        const lftData = [{
            test_date: '2017-01-04',
            total: 1.9,
            direct: 0.3,
            indirect: 1.6
        },
        {
            test_date: '2019-06-05',
            total: 1.9,
            direct: 0.3,
            indirect: 1.6
        },
        {
            test_date: '2019-12-02',
            total: 0.6,
            direct: 0.2,
            indirect: 0.4
        },
        {
            test_date: '2023-08-20',
            total: 1.2,
            direct: 0.18,
            indirect: 1.02
        },
        {
            test_date: '2025-05-10',
            total: 2.5,
            direct: 0.4,
            indirect: 2.1
        }
        ];
        updateChartWithLFTData(lftData);
    } else {
        apiRequest({
            url: `/UserEmployee/getEmployeeTestForGraph/${masterUserId}/${val}`,
            method: 'GET',
            onSuccess: function (resp) {
                if (resp.data && resp.data.length) {
                    updateChartWithData(resp.data, 'single');
                } else {
                    console.warn('No data found for this test');
                    clearChartAndTable();
                }
            },
            onError: function (err) {
                console.error('Error fetching test data:', err);
                clearChartAndTable();
            }
        });
    }
});
function clearChartAndTable() {
    const canvas = document.getElementById('lineChart');
    const chart = Chart.getChart(canvas);
    if (chart) chart.destroy();
    document.getElementById('testResultTable').innerHTML = '';
    document.getElementById('testResultTable').style.display = 'none';
    document.getElementById('chartLegend').innerHTML = '';
}
function updateChartWithData(data, mode) {
    const canvas = document.getElementById('lineChart');
    const existing = Chart.getChart(canvas);
    if (existing) existing.destroy();
    if (mode === 'differential') {
        updateChartWithDataForDifferential(data);
    } else {
        updateChartWithDataForSingle(data);
    }
}
function updateChartWithDataForSingle(data) {
    if (!data.length) return;
    const canvas = document.getElementById('lineChart');
    data.sort((a, b) => new Date(a.test_date) - new Date(b.test_date));
    const testName = data[0].test_name || 'Test';
    const unit = data[0].unit || '';
    let mMin = -Infinity,
        mMax = Infinity;
    try {
        const male = JSON.parse(data[0].m_min_max || '{}');
        mMin = parseFloat(male.min);
        mMax = parseFloat(male.max);
    } catch (e) {
        console.warn("Invalid m_min_max");
    }
    const results = [],
        labels = [],
        pointColors = [];
    data.forEach(item => {
        const date = new Date(item.test_date);
        const formattedDate =
            `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
        const value = parseFloat(item.test_result);
        let status = 'Normal';
        if (value < mMin) status = 'Low';
        else if (value > mMax) status = 'High';
        const color = status === 'Low' ? 'orange' : status === 'High' ? 'red' : 'green';
        results.push(value);
        labels.push(`${formattedDate} (${value})`);
        pointColors.push(color);
    });
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: `${testName} (${unit})`,
                data: results,
                borderColor: '#42a5f5',
                backgroundColor: 'rgba(66, 165, 245, 0.2)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: pointColors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        color: '#6e6b7b'
                    },
                    grid: {
                        color: '#ebedf3',
                        drawBorder: false
                    }
                },
                y: {
                    beginAtZero: false,
                    ticks: {
                        color: '#6e6b7b'
                    },
                    grid: {
                        color: '#ebedf3',
                        drawBorder: false
                    },
                    title: {
                        display: true,
                        text: `Result (${unit})`
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'start',
                    labels: {
                        usePointStyle: true,
                        color: '#5e5873'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.raw;
                            const color = context.dataset.pointBackgroundColor[context.dataIndex];
                            let status = '';
                            if (color === 'red') status = ' (High)';
                            else if (color === 'orange') status = ' (Low)';
                            else if (color === 'green') status = ' (Normal)';
                            return `${testName}: ${value} ${unit}${status}`;
                        }
                    }
                }
            }
        }
    });
    let tableHTML = `
    <table cellpadding="5" cellspacing="5" width="100%" border="1" class="table table-bordered">
      <thead>
        <tr style="line-height: 35px; background-color: rgb(107, 27, 199); color:#fff;">
          <th colspan="2" class="text-center fw-bold">${testName} (${unit})</th>
        </tr>
        <tr style="background-color:#d3d3d3; text-align:center; color: #333;">
          <th style="width: 50%;">Date</th>
          <th>Result</th>
        </tr>
      </thead>
      <tbody style="background-color:#fff !important;">
  `;
    data.forEach(item => {
        const date = new Date(item.test_date);
        const formattedDate =
            `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
        tableHTML += `
      <tr style="background-color:#fff;">
        <td class="text-center">${formattedDate}</td>
        <td class="text-center">${item.test_result}</td>
      </tr>`;
    });
    tableHTML += '</tbody></table>';
    let mRange = '',
        fRange = '';
    try {
        const male = JSON.parse(data[0].m_min_max);
        const female = JSON.parse(data[0].f_min_max);
        mRange = `${male.min} - ${male.max}`;
        fRange = `${female.min} - ${female.max}`;
    } catch (e) {
        console.warn('Invalid min/max format');
    }
    const referenceHTML = `
    <table cellpadding="5" cellspacing="5" width="100%" border="1" class="table table-bordered mt-4">
      <thead>
        <tr style="line-height: 35px;background-color: rgb(107, 27, 199); color:#fff;">
          <th colspan="2" class="text-center fw-bold">${testName} - Reference Range</th>
        </tr>
        <tr style="background-color:#d3d3d3;text-align:center; color: #333;">
          <th>Category</th>
          <th>Normal Range (${unit})</th>
        </tr>
      </thead>
      <tbody style="background-color: #fff !important;">
        <tr><td class="text-left">Male</td><td class="text-center">${mRange}</td></tr>
        <tr><td class="text-left">Female</td><td class="text-center">${fRange}</td></tr>
      </tbody>
    </table>`;
    document.getElementById('testResultTable').innerHTML = tableHTML;
    document.getElementById('referenceTable').innerHTML = referenceHTML;
    document.getElementById('testTablesContainer').style.display = 'flex';
    const tableContainer = document.getElementById('testResultTable');
    tableContainer.innerHTML = tableHTML + referenceHTML;
    tableContainer.style.display = 'block';
    const legendHTML = `
      <div class="custom-legend mt-3" style="display: flex; gap: 20px; align-items: center; font-size: 14px;">
        <div style="display: flex; align-items: center;">
          <span style="width: 14px; height: 14px; background-color: green; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> Normal
        </div>
        <div style="display: flex; align-items: center;">
          <span style="width: 14px; height: 14px; background-color: orange; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> Low
        </div>
        <div style="display: flex; align-items: center;">
          <span style="width: 14px; height: 14px; background-color: red; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> High
        </div>
      </div>
    `;
    document.getElementById('chartLegend').innerHTML = legendHTML;
}
function updateChartWithLFTData(data) {
    clearChartAndTable();
    const canvas = document.getElementById('lineChart');
    const additionalData = [{
        test_date: '2023-03-15',
        total: 1.3,
        direct: 0.4,
        indirect: 0.9
    },
    {
        test_date: '2024-11-10',
        total: 0.7,
        direct: 0.2,
        indirect: 0.5
    }
    ];
    data = data.concat(additionalData);
    data.sort((a, b) => new Date(a.test_date) - new Date(b.test_date));
    const labels = data.map(d => {
        const dt = new Date(d.test_date);
        return `${String(dt.getDate()).padStart(2, '0')}-${String(dt.getMonth() + 1).padStart(2, '0')}-${dt.getFullYear()}`;
    });
    const datasets = [{
        label: 'Total Bilirubin',
        data: data.map(d => d.total),
        borderColor: '#42a5f5',
        backgroundColor: '#42a5f533',
        fill: true,
        tension: 0.4,
        pointRadius: 5,
    },
    {
        label: 'Direct Bilirubin',
        data: data.map(d => d.direct),
        borderColor: '#66bb6a',
        backgroundColor: '#66bb6a33',
        fill: true,
        tension: 0.4,
        pointRadius: 5,
    },
    {
        label: 'Indirect Bilirubin',
        data: data.map(d => d.indirect),
        borderColor: '#ffa726',
        backgroundColor: '#ffa72633',
        fill: true,
        tension: 0.4,
        pointRadius: 5,
    }
    ];
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Bilirubin (mg/dL)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                },
                title: {
                    display: true,
                    text: 'Liver Function Test - Bilirubin Levels Over Time'
                }
            }
        }
    });
    let tableHTML =
        `<table class="table table-bordered"><thead><tr><th>Date</th><th>Total</th><th>Direct</th><th>Indirect</th></tr></thead><tbody>`;
    data.forEach(d => {
        const dt = new Date(d.test_date);
        const formatted =
            `${String(dt.getDate()).padStart(2, '0')}-${String(dt.getMonth() + 1).padStart(2, '0')}-${dt.getFullYear()}`;
        tableHTML +=
            `<tr><td>${formatted}</td><td>${d.total}</td><td>${d.direct}</td><td>${d.indirect}</td></tr>`;
    });
    tableHTML += `</tbody></table>`;
    const refTables = `
    <!-- Bilirubin Reference Ranges -->
    <table class="result-table" width="28%" style="float:left; margin: 0 2%;">
      <thead><tr><th colspan="2">Bilirubin - Total (Male)</th></tr></thead>
      <tbody><tr><td style="text-align:left !important;">Normal Range</td><td>0.3 - 1.2</td></tr></tbody>
    </table>
    <table class="result-table" width="28%" style="float:left; margin: 0 2%;">
      <thead><tr><th colspan="2">Bilirubin - Direct (Male)</th></tr></thead>
      <tbody><tr><td style="text-align:left !important;">Normal Range</td><td>0 - 0.25</td></tr></tbody>
    </table>
    <table class="result-table" width="28%" style="float:left; margin: 0 2%;">
      <thead><tr><th colspan="2">Bilirubin - Indirect (Male)</th></tr></thead>
      <tbody><tr><td style="text-align:left !important;">Normal Range</td><td>0 - 0.8</td></tr></tbody>
    </table>
  `;
    const testResultDiv = document.getElementById('testResultTable');
    testResultDiv.innerHTML =
        `<div style="width: 100%;">${tableHTML}</div><div style="display:flex;flex-wrap:wrap;margin-top:10px;">${refTables}</div>`;
    testResultDiv.style.display = 'block';
    document.getElementById('chartLegend').innerHTML = '';
}
function updateChartWithDataForDifferential(data) {
    const canvas = document.getElementById('lineChart');
    data.sort((a, b) => new Date(a.test_date) - new Date(b.test_date));
    const labels = data.map(d => {
        const dt = new Date(d.test_date);
        return `${String(dt.getDate()).padStart(2, '0')}-${String(dt.getMonth() + 1).padStart(2, '0')}-${dt.getFullYear()}`;
    });
    const types = ['Neutrophils', 'Lymphocyte', 'Eosinophil', 'Basophil'];
    const colors = ['#42a5f5', '#66bb6a', '#ffa726', '#ec407a'];
    const datasets = types.map((t, i) => ({
        label: t,
        data: data.map(d => parseFloat(d[t]) || 0),
        borderColor: colors[i],
        backgroundColor: colors[i] + '33',
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointHoverRadius: 7
    }));
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count (%)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                },
                title: {
                    display: true,
                    text: 'Differential Count Over Time'
                }
            }
        }
    });
    let valuesHtml = `<table class="table table-bordered"><thead><tr><th>Date</th>`;
    types.forEach(t => valuesHtml += `<th>${t}</th>`);
    valuesHtml += '</tr></thead><tbody>';
    data.forEach(d => {
        const dt = new Date(d.test_date);
        valuesHtml += `<tr><td>${String(dt.getDate()).padStart(2, '0')}-` +
            `${String(dt.getMonth() + 1).padStart(2, '0')}-${dt.getFullYear()}</td>`;
        types.forEach(t => valuesHtml += `<td>${d[t]}</td>`);
        valuesHtml += '</tr>';
    });
    valuesHtml += '</tbody></table>';
    const referenceHtml = `
  <table class="result-table" width="28%" style="float:left; margin: 0 2%;">
    <thead><tr><th colspan="2">Neutrophils (Male)</th></tr></thead>
    <tbody><tr><td style="text-align: left !important;">Normal Range</td><td>50 - 70</td></tr></tbody>
  </table>
  <table class="result-table" width="28%" style="float:left; margin: 0 2%;">
    <thead><tr><th colspan="2">Lymphocyte (Male)</th></tr></thead>
    <tbody><tr><td style="text-align: left !important;">Normal Range</td><td>20 - 40</td></tr></tbody>
  </table>
  <table class="result-table" width="28%" style="float:left; margin: 0 2%;">
    <thead><tr><th colspan="2">Eosinophil (Male)</th></tr></thead>
    <tbody><tr><td style="text-align: left !important;">Normal Range</td><td>01 - 06</td></tr></tbody>
  </table>
  <table class="result-table" width="28%" style="float:left; margin: 0 2%;">
    <thead><tr><th colspan="2">Basophil (Male)</th></tr></thead>
    <tbody><tr><td style="text-align: left !important;">Normal Range</td><td>00 - 02</td></tr></tbody>
  </table>
`;
    document.getElementById('testResultTable').innerHTML = valuesHtml;
    document.getElementById('referenceTable').innerHTML = referenceHtml;
    document.getElementById('testTablesContainer').style.display = 'flex';
    const legendHTML = types.map((t, i) =>
        `<div style="display:flex;align-items:center;">
      <span style="background:${colors[i]};width:14px;height:14px;display:inline-block;margin-right:6px;border-radius:50%;"></span>${t}</div>`).join(
            '');
    document.getElementById('chartLegend').innerHTML = `<div style="display:flex;gap:20px;">${legendHTML}</div>`;
}
function apiRequest({
    url,
    method,
    onSuccess,
    onError
}) {
    fetch(url, {
        method
    })
        .then(res => res.json())
        .then(onSuccess)
        .catch(onError);
}
function updateChartWithBPData(data) {
    const canvas = document.getElementById('lineChart');
    const chart = Chart.getChart(canvas);
    if (chart) chart.destroy();
    data.sort((a, b) => new Date(a.test_date) - new Date(b.test_date));
    const labels = data.map(d => {
        const dt = new Date(d.test_date);
        return `${String(dt.getDate()).padStart(2, '0')}-${String(dt.getMonth() + 1).padStart(2, '0')}-${dt.getFullYear()}`;
    });
    const systolicValues = data.map(d => d.systolic);
    const diastolicValues = data.map(d => d.diastolic);
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Systolic',
                data: systolicValues,
                borderColor: '#42a5f5',
                backgroundColor: '#42a5f522',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            },
            {
                label: 'Diastolic',
                data: diastolicValues,
                borderColor: '#ef5350',
                backgroundColor: '#ef535022',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'mmHg'
                    },
                    beginAtZero: false
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                }
            }
        }
    });
    let resultTable = `
    <table cellpadding="5" cellspacing="5" width="100%" align="center" border="1" class="table table-bordered table-striped">
      <thead>
        <tr><th colspan="3" class="text-center fw-bold">Blood Pressure - Systolic & Diastolic</th></tr>
        <tr style="background-color: #ad235e; color: #fff;">
          <th>Date</th>
          <th>Blood Pressure - Systolic</th>
          <th>Blood Pressure - Diastolic</th>
        </tr>
      </thead>
      <tbody>`;
    data.forEach(d => {
        const dt = new Date(d.test_date);
        const formatted =
            `${String(dt.getDate()).padStart(2, '0')}-${String(dt.getMonth() + 1).padStart(2, '0')}-${dt.getFullYear()}`;
        resultTable += `
      <tr>
        <td class="text-center">${formatted}</td>
        <td class="text-center">${d.systolic}</td>
        <td class="text-center">${d.diastolic}</td>
      </tr>`;
    });
    resultTable += '</tbody></table>';
    const referenceTables = `
    <div style="display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px; margin-top: 20px;">
      <table class="result-table" width="45%">
        <thead><tr><th colspan="2">Blood Pressure - Diastolic (Male)</th></tr></thead>
        <tbody>
          <tr><td style="text-align:left;">Low</td><td>0 - 59</td></tr>
          <tr><td style="text-align:left;">Normal</td><td>60 - 80</td></tr>
          <tr><td style="text-align:left;">Pre-Hypertension</td><td>81 - 90</td></tr>
          <tr><td style="text-align:left;">Hypertension</td><td>91 - ∞</td></tr>
        </tbody>
      </table>
      <table class="result-table" width="45%">
        <thead><tr><th colspan="2">Blood Pressure - Systolic (Male)</th></tr></thead>
        <tbody>
          <tr><td style="text-align:left;">Low</td><td>0 - 89</td></tr>
          <tr><td style="text-align:left;">Normal</td><td>90 - 120</td></tr>
          <tr><td style="text-align:left;">Pre-Hypertension</td><td>121 - 139</td></tr>
          <tr><td style="text-align:left;">Hypertension</td><td>140 - ∞</td></tr>
        </tbody>
      </table>
    </div>`;
    const tableContainer = document.getElementById('testResultTable');
    tableContainer.innerHTML = resultTable + referenceTables;
    tableContainer.style.display = 'block';
    document.getElementById('chartLegend').innerHTML = `
    <div class="custom-legend mt-3" style="display: flex; gap: 20px; font-size: 14px;">
      <div style="display: flex; align-items: center;">
        <span style="width: 14px; height: 14px; background-color: #42a5f5; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> Systolic
      </div>
      <div style="display: flex; align-items: center;">
        <span style="width: 14px; height: 14px; background-color: #ef5350; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> Diastolic
      </div>
    </div>`;
}

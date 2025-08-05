document.addEventListener('DOMContentLoaded', function () {
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
                                    if (!allTests.has(test.master_test_id)) {
                                        addOption(test);
                                        allTests.add(test.master_test_id);
                                    }
                                });
                            }
                        });
                    }
                });
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
function formatSelection(option) {
    return option.text;
}
$('#select2Primary_tests').on('change', function () {
    const selectedTestId = $(this).val();
    if (!masterUserId || !selectedTestId) {
        console.warn('Missing user ID or test ID');
        return;
    }
    apiRequest({
        url: `/UserEmployee/getEmployeeTestForGraph/${masterUserId}/${selectedTestId}`,
        method: 'GET',
        onSuccess: function (response) {
            console.log();
            console.log('Data received:', response);
            updateChartWithData(response.data);
        },
        onError: function (error) {
            console.error('Error fetching test data:', error);
        }
    });
});
let lineChartInstance = null;
function updateChartWithData(data, canvasId, tableContainerId) {
    const canvas = document.getElementById(canvasId);
    const existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();
    data.sort((a, b) => new Date(a.test_date) - new Date(b.test_date));
    const testName = data[0]?.test_name || 'Test';
    const unit = data[0]?.unit || '';
    let mMin = -Infinity, mMax = Infinity;
    let mRange = '', fRange = '';
    try {
        const male = JSON.parse(data[0]?.m_min_max || '{}');
        const female = JSON.parse(data[0]?.f_min_max || '{}');
        mMin = parseFloat(male.min);
        mMax = parseFloat(male.max);
        mRange = `${male.min} - ${male.max}`;
        fRange = `${female.min} - ${female.max}`;
    } catch (e) {
        console.warn("Invalid min/max reference range format", e);
    }
    const results = [], labels = [], pointColors = [];
    data.forEach(item => {
        const date = new Date(item.test_date);
        const formattedDate = `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
        const value = parseFloat(item.test_result);
        let status = 'Normal';
        if (value < mMin) status = 'Low';
        else if (value > mMax) status = 'High';
        const color = status === 'Low' ? 'orange' : status === 'High' ? 'red' : 'green';
        results.push(value);
        labels.push(`${formattedDate} (${value})`);
        pointColors.push(color);
    });
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
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
                    ticks: { color: '#6e6b7b' },
                    grid: { color: '#ebedf3', drawBorder: false }
                },
                y: {
                    beginAtZero: false,
                    ticks: { color: '#6e6b7b' },
                    grid: { color: '#ebedf3', drawBorder: false },
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
        const formattedDate = `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
        tableHTML += `
      <tr style="background-color:#fff;">
        <td class="text-center">${formattedDate}</td>
        <td class="text-center">${item.test_result}</td>
      </tr>
    `;
    });
    tableHTML += `</tbody></table>`;
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
        <tr>
          <td class="text-left">Male</td>
          <td class="text-center">${mRange}</td>
        </tr>
        <tr>
          <td class="text-left">Female</td>
          <td class="text-center">${fRange}</td>
        </tr>
      </tbody>
    </table>
  `;
    const tableContainer = document.getElementById(tableContainerId);
    tableContainer.innerHTML = tableHTML + referenceHTML;
    tableContainer.style.display = 'block';
    const legendContainer = document.getElementById('chartLegend');
    if (legendContainer && legendContainer.innerHTML.trim() === '') {
        legendContainer.innerHTML = `
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
        legendContainer.style.display = 'flex';
    }
}
const groupedTestData = {
    group_name: "Diabetic Panel",
    data: {
        "Glucose - Fasting": [
            { test_date: "2017-01-04", test_result: 92, unit: "mg/dL" },
            { test_date: "2019-06-05", test_result: 92, unit: "mg/dL" },
            { test_date: "2022-10-02", test_result: 80, unit: "mg/dL" }
        ],
        "Glucose - Post Prandial": [
            { test_date: "2017-01-04", test_result: 70, unit: "mg/dL" },
            { test_date: "2019-06-05", test_result: 70, unit: "mg/dL" },
            { test_date: "2022-10-02", test_result: 120, unit: "mg/dL" }
        ],
        "HbA1C": [
            { test_date: "2017-01-04", test_result: 5.1, unit: "%" },
            { test_date: "2019-06-05", test_result: 5.1, unit: "%" },
            { test_date: "2022-08-21", test_result: 6.5, unit: "%" },
            { test_date: "2022-10-02", test_result: 5.0, unit: "%" }
        ]
    }
};
const referenceRanges = {
    "Glucose - Fasting": [
        { label: "Normal", min: 0, max: 99 },
        { label: "Pre-diabetes", min: 100, max: 125 },
        { label: "Diabetes", min: 126, max: Infinity }
    ],
    "Glucose - Post Prandial": [
        { label: "Normal", min: 0, max: 139 },
        { label: "Pre-diabetes", min: 140, max: 199 },
        { label: "Diabetes", min: 200, max: Infinity }
    ],
    "HbA1C": [
        { label: "Normal", min: 0, max: 5.7 },
        { label: "Pre-diabetes", min: 5.71, max: 6.4 },
        { label: "Diabetes", min: 6.41, max: Infinity }
    ]
};
document.addEventListener("DOMContentLoaded", function () {
    renderGroupedTests(groupedTestData.data);
});
function renderGroupedTests(groupData) {
    document.getElementById('glucoseChartContainer').innerHTML = '';
    document.getElementById('hbChartContainer').innerHTML = '';
    document.getElementById('glucoseTables').innerHTML = '';
    document.getElementById('hbA1CTables').innerHTML = '';
    renderGlucoseChart(groupData, document.getElementById('glucoseChartContainer'));
    renderHbA1CChart(groupData["HbA1C"], document.getElementById('hbChartContainer'));
    renderGlucoseTable(groupData, document.getElementById('glucoseTables'));
    renderHbA1CTable(groupData["HbA1C"], document.getElementById('hbA1CTables'));
}
function renderGlucoseChart(data, container) {
    const canvas = document.createElement('canvas');
    container.appendChild(canvas);
    const dates = [...new Set(
        [...data["Glucose - Fasting"], ...data["Glucose - Post Prandial"]]
            .map(d => d.test_date)
    )].sort();
    const formattedDates = dates.map(d => formatDate(d));
    const fastingMap = Object.fromEntries(data["Glucose - Fasting"].map(d => [d.test_date, d.test_result]));
    const ppMap = Object.fromEntries(data["Glucose - Post Prandial"].map(d => [d.test_date, d.test_result]));
    const fastingValues = dates.map(d => fastingMap[d] || null);
    const ppValues = dates.map(d => ppMap[d] || null);
    new Chart(canvas.getContext("2d"), {
        type: "line",
        data: {
            labels: formattedDates,
            datasets: [
                {
                    label: "Glucose - Fasting (mg/dL)",
                    data: fastingValues,
                    borderColor: "rgba(54, 162, 235, 1)",
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5
                },
                {
                    label: "Glucose - Post Prandial (mg/dL)",
                    data: ppValues,
                    borderColor: "rgba(75, 192, 192, 1)",
                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: { display: true, text: 'mg/dL' }
                }
            }
        }
    });
}
function renderGlucoseTable(data, container) {
    const dates = [...new Set(
        [...data["Glucose - Fasting"], ...data["Glucose - Post Prandial"]]
            .map(d => d.test_date)
    )].sort();
    let html = `
    <h5>Blood Glucose</h5>
    <table class="table table-bordered">
      <thead><tr><th>Date</th><th>Fasting</th><th>Post-Prandial</th></tr></thead>
      <tbody>
  `;
    dates.forEach(date => {
        const fasting = data["Glucose - Fasting"].find(d => d.test_date === date)?.test_result || "-";
        const pp = data["Glucose - Post Prandial"].find(d => d.test_date === date)?.test_result || "-";
        html += `<tr><td>${formatDate(date)}</td><td>${fasting}</td><td>${pp}</td></tr>`;
    });
    html += '</tbody></table>';
    html += renderReferenceTable("Glucose - Fasting");
    html += renderReferenceTable("Glucose - Post Prandial");
    container.innerHTML += html;
}
function renderHbA1CChart(data, container) {
    const canvas = document.createElement('canvas');
    container.appendChild(canvas);
    data.sort((a, b) => new Date(a.test_date) - new Date(b.test_date));
    const dates = data.map(d => formatDate(d.test_date));
    const values = data.map(d => d.test_result);
    new Chart(canvas.getContext("2d"), {
        type: "line",
        data: {
            labels: dates,
            datasets: [{
                label: "HbA1C (%)",
                data: values,
                borderColor: "rgba(153, 102, 255, 1)",
                backgroundColor: "rgba(153, 102, 255, 0.2)",
                fill: true,
                tension: 0.4,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: { display: true, text: '%' }
                }
            }
        }
    });
}
function renderHbA1CTable(data, container) {
    let hba1cTables = `
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Date</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
  `;
    data.forEach(item => {
        const date = new Date(item.test_date);
        const formattedDate = `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
        hba1cTables += `
      <tr>
        <td>${formattedDate}</td>
        <td>${item.test_result}</td>
      </tr>
    `;
    });
    hba1cTables += `
      </tbody>
    </table>
  `;
    hba1cTables += `
    <table class="table table-bordered mt-4">
      <thead>
        <tr>
          <th>Category</th>
          <th>Normal Range</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>Normal</td><td>0 - 5.7</td></tr>
        <tr><td>Pre-Diabetes</td><td>5.71 - 6.4</td></tr>
        <tr><td>Diabetes</td><td>6.41 - Infinity</td></tr>
      </tbody>
    </table>
  `;
    container.innerHTML = hba1cTables;
}
function renderReferenceTable(testName) {
    const range = referenceRanges[testName];
    if (!range) return '';
    let html = `
    <h6>${testName} - Reference Range</h6>
    <table class="table table-bordered">
      <thead><tr><th>Status</th><th>Range</th></tr></thead>
      <tbody>
  `;
    range.forEach(r => {
        html += `<tr><td>${r.label}</td><td>${r.min} - ${r.max === Infinity ? '∞' : r.max}</td></tr>`;
    });
    html += '</tbody></table>';
    return html;
}
function formatDate(str) {
    const d = new Date(str);
    return `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
}
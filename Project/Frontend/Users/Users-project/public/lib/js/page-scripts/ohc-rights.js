$(document).ready(function () {
    apiRequest({
        url: url,
        method: 'GET',
        onSuccess: function (response) {
            if (response.data) {
                const data = response.data;
                const data2 = response.data2;
                const dataMenu = document.getElementById('data-menu');
                dataMenu.innerHTML = '';
                const createRadioInput = (name, value, isChecked = false) => {
                    const input = document.createElement('input');
                    input.type = 'radio';
                    input.className = 'form-check-input';
                    input.name = name;
                    input.value = value;
                    if (isChecked) input.checked = true;
                    return input;
                };
                const createLabelRow = (labelText, namePrefix, values = [0, 1, 2], checkedValue = null) => {
                    const row = document.createElement('div');
                    row.className = 'row g-12';
                    const colLabel = document.createElement('div');
                    colLabel.className = 'col-md-6';
                    const label = document.createElement('label');
                    label.textContent = labelText;
                    colLabel.appendChild(label);
                    row.appendChild(colLabel);
                    values.forEach(value => {
                        const col = document.createElement('div');
                        col.className = 'col-md-2';
                        col.appendChild(createRadioInput(namePrefix, value, checkedValue === value));
                        row.appendChild(col);
                    });
                    return row;
                };
                const createDashedLine = () => {
                    const hr = document.createElement('hr');
                    hr.style.borderTop = '2px dashed #ccc';
                    hr.style.marginBottom = '20px';
                    hr.style.width = '100%';
                    return hr;
                };
                data.forEach(item => {
                    if (item.submodules && item.submodules.length > 0) {
                        const headerRow = document.createElement('div');
                        headerRow.className = 'row g-12';
                        const headerCol = document.createElement('div');
                        headerCol.className = 'col-md-12';
                        const h6 = document.createElement('h6');
                        h6.textContent = item.component.module.module_name;
                        headerCol.appendChild(h6);
                        headerRow.appendChild(headerCol);
                        dataMenu.appendChild(headerRow);
                        item.submodules.forEach(submodule => {
                            let check = null;
                            if (data2?.length) {
                                const d = data2[0];
                                const map = {
                                    'Diagnostic Assessment': d.diagnostic_assessment,
                                    'Health Risk Assessment': d.hra,
                                    'Events': d.events,
                                };
                                check = map[submodule.sub_module_name];
                            }
                            const radioRow = createLabelRow(submodule.sub_module_name, `sub_module_${submodule.id}_radio`, [0, 1, 2], check);
                            dataMenu.appendChild(radioRow);
                        });
                        if (item.component.module.module_name === 'OHC') {
                            const d = data2?.[0];
                            const monitoring = d?.employee_monitoring;
                            const reports = d?.reports;
                            const monitoringRow = createLabelRow('Employee Monitoring', 'employee_monitoring_radio', [0, 1], monitoring);
                            const reportsRow = createLabelRow('Reports', 'reports_radio', [0, 1], reports);
                            dataMenu.appendChild(monitoringRow);
                            dataMenu.appendChild(reportsRow);
                        }
                    } else {
                        let checkModule = null;
                        const d = data2?.[0];
                        if (d) {
                            if (item.component.module.module_name === 'Pre-Employment') {
                                checkModule = d.pre_employment;
                            } else if (item.component.module.module_name === 'Health Partners') {
                                checkModule = d.health_partner;
                            }
                        }
                        const moduleRow = createLabelRow(item.component.module.module_name, `module_${item.component.module.id}_radio`, [0, 1, 2], checkModule);
                        dataMenu.appendChild(moduleRow);
                    }
                    dataMenu.appendChild(createDashedLine());
                });
                if (data2?.length) {
                    $(`input[name="employees"][value="${data2[0].employees}"]`).prop('checked', true);
                    if (Array.isArray(data2[0].landing_page)) {
                        data2[0].landing_page.forEach(page => {
                            $(`input[name="landing_page[]"][value="${page}"]`).prop('checked', true);
                        });
                    }
                    $('#corporate_menu_rights_id').val(data2[0].id);
                    $('#save_ohc_rights').attr('id', 'update_ohc_rights').text('Update');
                } else {
                    $('#save_ohc_rights').attr('id', 'save_ohc_rights').text('Save');
                }
            } else {
                console.error('An error occurred while fetching data');
            }
        },
        onError: function (error) {
            console.error('An error occurred:', error);
        }
    });
});
$('#save_ohc_rights').click(function () {
    if ($(this).text() === 'Save') {
        const formDataArray = $('form').serializeArray();
        const formDataObject = {};
        $.each(formDataArray, function () {
            formDataObject[this.name] = this.value;
        });
        apiRequest({
            url: "/corporate-users/save-ohc-rights",
            method: "POST",
            data: formDataObject,
            onSuccess: function (response) {
                if (response.result === true) {
                    showToast("success", "Data saved successfully!");
                    window.location.href = "https://login-users.hygeiaes.com/corporate-users/users-list";
                } else {
                    alert("An error occurred while saving the datas.");
                }
            },
            onError: function (error) {
                console.error("An error occurred: " + error);
                alert("An error occurred while saving the data.");
            }
        });
    }
    else {
        const formDataArray = $('form').serializeArray();
        const formDataObject = {};
        $.each(formDataArray, function () {
            formDataObject[this.name] = this.value;
        });
        apiRequest({
            url: "/corporate-users/update-ohc-rights",
            method: "POST",
            data: formDataObject,
            onSuccess: function (response) {
                console.log("response:", response.result);
                if (response.result === true) {
                    showToast("success", "Data Updated successfully!");
                    window.location.href = "https://login-users.hygeiaes.com/corporate-users/users-list";
                } else {
                    alert("An error occurred while saving the datas.");
                }
            },
            onError: function (error) {
                console.error("An error occurred: " + error);
                alert("An error occurred while saving the data.");
            }
        });
    }
});

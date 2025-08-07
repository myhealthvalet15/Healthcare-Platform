$(document).ready(function () {
    apiRequest({
        url: url,
        method: 'GET',
        onSuccess: function (response) {
            if (!response.data) {
                console.error('An error occurred while fetching data');
                return;
            }
            const data = response.data;
            const data2 = response.data2;
            const dataMenu = document.getElementById('data-menu');
            dataMenu.innerHTML = '';
            const createRow = (...children) => {
                const row = document.createElement('div');
                row.className = 'row g-12';
                children.forEach(child => row.appendChild(child));
                return row;
            };
            const createCol = (colClass, content) => {
                const col = document.createElement('div');
                col.className = colClass;
                if (typeof content === 'string') {
                    col.innerHTML = content;
                } else {
                    col.appendChild(content);
                }
                return col;
            };
            const createInput = (name, value, isChecked = false) => {
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = name;
                input.className = 'form-check-input';
                input.value = value;
                if (isChecked) input.checked = true;
                return input;
            };
            const appendDashedHr = () => {
                const hr = document.createElement('hr');
                hr.style.cssText = 'border-top: 2px dashed #ccc; margin-bottom: 20px; width: 100%;';
                dataMenu.appendChild(hr);
            };
            data.forEach(item => {
                const moduleName = item.component.module.module_name;
                if (item.submodules && item.submodules.length > 0) {
                    dataMenu.appendChild(createRow(createCol('col-md-12', `<h6>${moduleName}</h6>`)));
                    item.submodules.forEach(submodule => {
                        let check = null;
                        if (data2?.length) {
                            const d = data2[0];
                            if (submodule.sub_module_name === 'Diagnostic Assessment') check = d.diagnostic_assessment;
                            else if (submodule.sub_module_name === 'Health Risk Assessment') check = d.hra;
                            else if (submodule.sub_module_name === 'Events') check = d.events;
                        }
                        dataMenu.appendChild(createRow(
                            createCol('col-md-6', `<label>${submodule.sub_module_name}</label>`),
                            createCol('col-md-2', createInput(`sub_module_${submodule.id}_radio`, 0, check === 0)),
                            createCol('col-md-2', createInput(`sub_module_${submodule.id}_radio`, 1, check === 1)),
                            createCol('col-md-2', createInput(`sub_module_${submodule.id}_radio`, 2, check === 2))
                        ));
                    });
                    if (moduleName === 'MHC') {
                        const d = data2?.[0] || {};
                        dataMenu.appendChild(createRow(
                            createCol('col-md-6', '<label>Employee Monitoring</label>'),
                            createCol('col-md-2', createInput('employee_monitoring_radio', 0, d.employee_monitoring === 0)),
                            createCol('col-md-2', createInput('employee_monitoring_radio', 1, d.employee_monitoring === 1))
                        ));
                        dataMenu.appendChild(createRow(
                            createCol('col-md-6', '<label>Reports</label>'),
                            createCol('col-md-2', createInput('reports_radio', 0, d.reports === 0)),
                            createCol('col-md-2', createInput('reports_radio', 1, d.reports === 1))
                        ));
                    }
                } else {
                    let checkmodule = null;
                    const d = data2?.[0];
                    if (d) {
                        if (moduleName === 'Pre-Employment') checkmodule = d.pre_employment;
                        else if (moduleName === 'Health Partners') checkmodule = d.health_partner;
                    }
                    dataMenu.appendChild(createRow(
                        createCol('col-md-6', `<label>${moduleName}</label>`),
                        createCol('col-md-2', createInput(`module_${item.component.module.id}_radio`, 0, checkmodule === 0)),
                        createCol('col-md-2', createInput(`module_${item.component.module.id}_radio`, 1, checkmodule === 1)),
                        createCol('col-md-2', createInput(`module_${item.component.module.id}_radio`, 2, checkmodule === 2))
                    ));
                }
                appendDashedHr();
            });
            if (data2?.[0]) {
                const d = data2[0];
                document.querySelector(`input[name="employees"][value="${d.employees}"]`)?.setAttribute('checked', true);
                if (Array.isArray(d.landing_page)) {
                    d.landing_page.forEach(page => {
                        document.querySelector(`input[name="landing_page[]"][value="${page}"]`)?.setAttribute('checked', true);
                    });
                }
                document.getElementById('corporate_menu_rights_id').value = d.id;
                const saveBtn = document.getElementById('save_mhc_rights');
                saveBtn.id = 'update_mhc_rights';
                saveBtn.textContent = 'Update';
            } else {
                const saveBtn = document.getElementById('save_mhc_rights');
                saveBtn.textContent = 'Save';
            }
        },
        onError: function (error) {
            console.error('An error occurred:', error);
        }
    });
});
$('#save_mhc_rights').click(function () {
    if ($(this).text() === 'Save') {
        const formData = $('form').serializeArray();
        const formDataObject = {};
        $.each(formData, function () {
            formDataObject[this.name] = this.value;
        });
        apiRequest({
            url: '/corporate-users/save-mhc-rights',
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
                if (response.result === true) {
                    showToast('success', 'Data saved successfully!');
                    window.location.href = 'https://login-users.hygeiaes.com/corporate-users/users-list';
                } else {
                    alert('An error occurred while saving the data.');
                }
            },
            onError: function (error) {
                console.error('An error occurred:', error);
                alert('An error occurred while saving the data.');
            }
        });
    } else {
        const formData = $('form').serializeArray();
        const formDataObject = {};
        $.each(formData, function () {
            formDataObject[this.name] = this.value;
        });
        apiRequest({
            url: '/corporate-users/update-mhc-rights',
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
                console.log("response:", response.result);
                if (response.result === true) {
                    showToast("success", "Data Updated successfully!");
                    window.location.href = 'https://login-users.hygeiaes.com/corporate-users/users-list';
                } else {
                    alert('An error occurred while saving the data.');
                }
            },
            onError: function (error) {
                console.error('An error occurred:', error);
                alert('An error occurred while saving the data.');
            }
        });
    }
});

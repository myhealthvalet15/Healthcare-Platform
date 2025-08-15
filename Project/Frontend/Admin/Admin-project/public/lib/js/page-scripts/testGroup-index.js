document.addEventListener("DOMContentLoaded", function () {
    function setupValidation(formClass) {
        var forms = document.querySelectorAll(formClass);
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener("submit", function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add("was-validated");
            }, false);
        });
    }
    setupValidation(".needs-validation-group");
    setupValidation(".needs-validation-subGroup");
    setupValidation(".needs-validation-subSubGroup");
    $(".select2").select2();

    function validateSelect2(selectElement) {
        if ($(selectElement).val() === "0") {
            $(selectElement).next('.select2-container').addClass('is-invalid');
            $(selectElement).siblings('.invalid-feedback').show();
            return false;
        } else {
            $(selectElement).next('.select2-container').removeClass('is-invalid');
            $(selectElement).siblings('.invalid-feedback').hide();
            return true;
        }
    }
    $(".select2").on("change", function () {
        validateSelect2(this);
    });
    $("form.needs-validation").on("submit", function (event) {
        let isValid = true;
        $(this).find(".select2").each(function () {
            if (!validateSelect2(this)) {
                isValid = false;
            }
        });
        if (!this.checkValidity()) {
            isValid = false;
        }
        $(this).addClass("was-validated");
        if (!isValid) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
    const tableBody = document.querySelector('tbody');
    const apiBaseUrl = "https://mhv-admin.hygeiaes.com/test-group/";
    const stepTriggers = document.querySelectorAll('.step-trigger');
    const spinner = document.getElementById('existing-group-spinner');
    const groupDatas = document.getElementById('existing-group-data');
    fetchAndPopulateGroups();
    stepTriggers.forEach(trigger => {
        trigger.addEventListener('click', handleStepTrigger);
    });
    const groupForm = document.getElementById('testGroupForm');
    const statusSwitch = groupForm.querySelector('.switch-input');
    groupForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }
        const groupName = document.getElementById('grpName').value.trim();
        const activeStatus = statusSwitch.checked ? 1 : 0;
        try {
            var textContent = document.getElementById("submitButtonGroup").textContent.toLowerCase().trim();
            if (textContent === "update") {
                var groupId = document.getElementById('editGroupItemId').value;
            }
            var url = textContent == 'add' ? '/test-group/addGroup' : '/test-group/updateGroup'
            const data = await apiRequest({
                url: url,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                data: {
                    test_group_id: groupId ? groupId : 0,
                    test_group_name: groupName,
                    active_status: activeStatus
                }
            });
            if (data.result) {
                textContent === 'add' ? showToast('success', 'Success', 'Sub Group added successfully!') : showToast('success', 'Success', 'Sub Group updated successfully!');
                groupForm.reset();
                groupForm.classList.remove('was-validated');
                if (typeof fetchAndPopulateGroups === 'function') {
                    fetchAndPopulateGroups();
                }
            } else {
                showToast('error', 'Error', data.message || 'Failed to add group');
            }
        } catch (error) {
            showToast('error', 'Error', 'An error occurred while adding the group');
        }
    });
    const subGroupForm = document.getElementById('testSubGroupForm');
    subGroupForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }
        const subGroupName = document.getElementById('subGrpName').value.trim();
        const groupId = document.getElementById('bs_validation_group_inSubGroup').value;
        const activeStatus = document.getElementById('subGroupActiveStatus').checked ? 1 : 0;
        if (!groupId) {
            showToast('error', 'Error', 'Please select a Group.');
            return;
        }
        try {
            var textContent = document.getElementById("submitButtonSubGroup").textContent.toLowerCase().trim();
            if (textContent === "update") {
                var subGroupId = document.getElementById('editSubGroupItemId').value;
            }
            var url = textContent == 'add' ? '/test-group/addSubGroup' : '/test-group/updateSubGroup'
            const data = await apiRequest({
                url: url,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                data: {
                    group_id: groupId,
                    subgroup_id: subGroupId ? subGroupId : 0,
                    sub_group_name: subGroupName,
                    active_status: activeStatus
                }
            });
            if (data.result) {
                textContent === 'add' ? showToast('success', 'Success', 'Sub Group added successfully!') : showToast('success', 'Success', 'Sub Group updated successfully!');
                subGroupForm.reset();
                subGroupForm.classList.remove('was-validated');
                if (textContent === 'update') {
                    let dropdown = document.getElementById("bs_validation_group_inSubGroup");
                    if (dropdown) {
                        $(dropdown).prop("disabled", false);
                        $(dropdown).select2();
                    }
                }
                fetchAndPopulateSubGroups();
            } else {
                showToast('error', 'Error', data.message || 'Failed to add Sub Group');
            }
        } catch (error) {
            showToast('error', 'Error', 'An error occurred while adding the Sub Group');
        }
    });
    const subSubGroupForm = document.getElementById('testSubSubGroupForm');
    subSubGroupForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }
        const subSubGroupName = document.getElementById('subSubGrpName').value.trim();
        const groupId = document.getElementById('bs_validation_group_inSubSubGroup').value;
        const subGroupId = document.getElementById('bs_validation_subGroup_inSubSubGroup').value;
        const activeStatus = document.getElementById('subSubGroupActiveStatus').checked ? 1 : 0;
        if (!groupId || !subGroupId) {
            showToast('error', 'Error', 'Please select both Group and Sub Group.');
            return;
        }
        try {
            var textContent = document.getElementById("submitButtonSubSubGroup").textContent.toLowerCase().trim();
            if (textContent === "update") {
                var subSubGroupId = document.getElementById('editSubSubGroupItemId').value;
            }
            var url = textContent == 'add' ? '/test-group/addSubSubGroup' : '/test-group/updateSubSubGroup'
            const data = await apiRequest({
                url: url,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                data: {
                    test_group_id: groupId,
                    test_sub_group_id: subGroupId,
                    test_sub_sub_group_id: subSubGroupId,
                    sub_sub_group_name: subSubGroupName,
                    active_status: activeStatus,
                }
            });
            if (data.result) {
                textContent === 'add' ? showToast('success', 'Success', 'Sub Sub Group added successfully!') : showToast('success', 'Success', 'Sub Sub Group updated successfully!');
                subSubGroupForm.reset();
                subSubGroupForm.classList.remove('was-validated');
                if (textContent === 'update') {
                    let dropdown = document.getElementById("bs_validation_subGroup_inSubSubGroup");
                    if (dropdown) {
                        $(dropdown).prop("disabled", false);
                        $(dropdown).select2();
                    }
                    let subGroupDropdown = document.getElementById("bs_validation_group_inSubSubGroup");
                    if (subGroupDropdown) {
                        $(subGroupDropdown).prop("disabled", false);
                        $(subGroupDropdown).select2();
                    }
                }
                fetchAndPopulateSubSubGroups();
            } else {
                showToast('error', 'Error', data.message || 'Failed to add Sub Sub Group');
            }
        } catch (error) {
            showToast('error', 'Error', 'An error occurred while adding the Sub Sub Group');
        }
    });
    groupForm.addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            groupForm.querySelector('button[type="submit"]').click();
        }
    });
    async function handleStepTrigger(event) {
        const stepTitle = event.currentTarget.querySelector('.bs-stepper-title').textContent;
        showLoadingState();
        clearTable();
        setTimeout(() => {
            switch (stepTitle) {
                case 'Group':
                    fetchAndPopulateGroups();
                    break;
                case 'Sub Group':
                    fetchAndPopulateSubGroups();
                    break;
                case 'Sub Sub Group':
                    fetchAndPopulateSubSubGroups();
                    break;
            }
        }, 1000);
    }

    function showLoadingState() {
        spinner.style.display = 'block';
        groupDatas.style.display = 'none';
    }

    function hideLoadingState() {
        spinner.style.display = 'none';
        groupDatas.style.display = 'block';
    }

    function clearTable() {
        while (tableBody.firstChild) {
            tableBody.removeChild(tableBody.firstChild);
        }
    }
    async function fetchAndPopulateGroups() {
        try {
            const data = await apiRequest({
                url: `${apiBaseUrl}getAllGroups`,
                method: 'GET'
            });
            if (data.result) {
                populateTable(data.message, 'group');
                updateDropdown(document.getElementById('bs_validation_group_inSubGroup'), data.message, "test_group_id", "test_group_name");
                updateDropdown(document.getElementById('bs_validation_group_inSubSubGroup'), data.message, "test_group_id", "test_group_name");
            } else {
                showToast('error', 'Error', data.message || 'Error fetching groups');
            }
            document.getElementById("testGroupForm").reset();
            const submitButton = document.getElementById('submitButtonGroup');
            if (submitButton) {
                submitButton.textContent = '';
                submitButton.classList.add('btn-primary');
                const icon = document.createElement('i');
                icon.classList.add('fa-solid', 'fa-plus');
                icon.style.marginRight = '5px';
                const textNode = document.createTextNode('Add');
                submitButton.appendChild(icon);
                submitButton.appendChild(textNode);
            }
        } catch (error) {
            showToast('error', 'Error', error);
        }
    }
    async function fetchAndPopulateSubGroups() {
        try {
            document.getElementById("testSubGroupForm").reset();
            const groupDropdown = document.getElementById('bs_validation_group_inSubGroup');
            groupDropdown.selectedIndex = -1;
            const event = new Event('change', {
                bubbles: true
            });
            groupDropdown.dispatchEvent(event);
            const submitButton = document.getElementById('submitButtonSubGroup');
            if (submitButton) {
                submitButton.textContent = '';
                submitButton.classList.add('btn-primary');
                const icon = document.createElement('i');
                icon.classList.add('fa-solid', 'fa-plus');
                icon.style.marginRight = '5px';
                const textNode = document.createTextNode('Add');
                submitButton.appendChild(icon);
                submitButton.appendChild(textNode);
            }
            const data = await apiRequest({
                url: `${apiBaseUrl}getAllSubGroups`,
                method: 'GET'
            });
            if (data.result) {
                populateTable(data.message, 'subGroup');
            } else {
                showToast('error', 'Error', data.message || 'Error fetching subgroups');
            }
        } catch (error) {
            showToast('error', 'Error', error);
        }
    }
    async function fetchAndPopulateSubGroupsinDropdown(groupId = null) {
        try {
            document.getElementById("testSubGroupForm").reset();
            const groupDropdown = document.getElementById('bs_validation_group_inSubGroup');
            groupDropdown.selectedIndex = -1;
            const event = new Event('change', {
                bubbles: true
            });
            var url = groupId ? `${apiBaseUrl}getSubGroupOfGroup/${groupId}` : `${apiBaseUrl}getAllSubGroups`
            groupDropdown.dispatchEvent(event);
            const data = await apiRequest({
                url: url,
                method: 'GET'
            });
            if (data.result) {
                updateDropdown(document.getElementById('bs_validation_subGroup_inSubSubGroup'), data.message, "test_group_id", "test_group_name");
            } else {
                showToast('error', 'Error', data.message || 'Error fetching subgroups');
            }
        } catch (error) {
            showToast('error', 'Error', error);
        }
    }
    async function fetchAndPopulateSubSubGroups() {
        try {
            document.getElementById("testSubSubGroupForm").reset();
            const groupDropdown = document.getElementById('bs_validation_group_inSubSubGroup');
            groupDropdown.selectedIndex = -1;
            const event = new Event('change', {
                bubbles: true
            });
            groupDropdown.dispatchEvent(event);
            const subGroupDropdown = document.getElementById('bs_validation_subGroup_inSubSubGroup');
            subGroupDropdown.selectedIndex = -1;
            const eventSubSubGroup = new Event('change', {
                bubbles: true
            });
            subGroupDropdown.dispatchEvent(eventSubSubGroup);
            const submitButton = document.getElementById('submitButtonSubSubGroup');
            if (submitButton) {
                submitButton.textContent = '';
                submitButton.classList.add('btn-primary');
                const icon = document.createElement('i');
                icon.classList.add('fa-solid', 'fa-plus');
                icon.style.marginRight = '5px';
                const textNode = document.createTextNode('Add');
                submitButton.appendChild(icon);
                submitButton.appendChild(textNode);
            }
            const data = await apiRequest({
                url: `${apiBaseUrl}getAllSubSubGroups`,
                method: 'GET'
            });
            if (data.result) {
                fetchAndPopulateSubGroupsinDropdown();
                populateTable(data.message, 'subSubGroup');
            } else {
                showToast('error', 'Error', data.message || 'Error fetching subsubgroups');
            }
        } catch (error) {
            showToast('error', 'Error', error);
        }
    }

    function updateDropdown(dropdown, data, valueKey, textKey) {
        if (!dropdown) return;
        while (dropdown.options.length > 1) {
            dropdown.remove(1);
        }
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[textKey];
            dropdown.appendChild(option);
        });
        $(dropdown).trigger('change.select2');
    }

    function createTableRow(item, type, index) {
        const row = document.createElement('tr');
        const serialCell = document.createElement('td');
        serialCell.textContent = index + 1;
        const nameCell = document.createElement('td');
        const nameSpan = document.createElement('span');
        nameSpan.className = 'fw-medium';
        nameSpan.textContent = item.test_group_name || '';
        nameCell.appendChild(nameSpan);
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        const isActive = item.active_status === 1;
        statusBadge.className = `badge ${isActive ? 'bg-label-success' : 'bg-label-danger'}`;
        statusBadge.textContent = isActive ? 'Active' : 'Inactive';
        statusCell.appendChild(statusBadge);
        const actionsCell = document.createElement('td');
        actionsCell.appendChild(createEditButton(item, type));
        actionsCell.appendChild(createDeleteButton(item, type));
        row.append(serialCell, nameCell, statusCell, actionsCell);
        return row;
    }

    function populateTable(data, type) {
        clearTable();
        updateTableHeader(type);
        if (!data || data.length === 0) {
            const alertRow = document.createElement('tr');
            const alertCell = document.createElement('td');
            alertCell.setAttribute('colspan', '6');
            alertCell.style.textAlign = 'center';
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-info';
            alertDiv.setAttribute('role', 'alert');
            alertDiv.style.display = 'inline-block';
            alertDiv.style.margin = '0 auto';
            const icon = document.createElement('i');
            icon.className = 'ti ti-info-circle me-2';
            const textNode = document.createTextNode(
                `No ${type === 'group' ? 'group' : type === 'subGroup' ? 'sub group' : 'subSubGroup'} available.`
            );
            alertDiv.appendChild(icon);
            alertDiv.appendChild(textNode);
            alertCell.appendChild(alertDiv);
            alertRow.appendChild(alertCell);
            tableBody.appendChild(alertRow);
        } else {
            data.forEach((item, index) => {
                if (type === 'subGroup') {
                    const row = createSubGroupRow(item, type, index);
                    tableBody.appendChild(row);
                } else if (type === 'subSubGroup') {
                    const row = createSubSubGroupRow(item, type, index);
                    tableBody.appendChild(row);
                } else {
                    const row = createTableRow(item, type, index);
                    tableBody.appendChild(row);
                }
            });
        }
        hideLoadingState();
    }

    function updateTableHeader(type) {
        const tableHead = document.querySelector(".table thead");
        tableHead.innerHTML = "";
        const headerRow = document.createElement("tr");
        headerRow.appendChild(createHeaderCell("Serial No."));
        headerRow.appendChild(createHeaderCell("Group Name"));
        if (type === "subGroup" || type === "subSubGroup") {
            headerRow.appendChild(createHeaderCell("Sub Group Name"));
        }
        if (type === "subSubGroup") {
            headerRow.appendChild(createHeaderCell("Sub Sub Group Name"));
        }
        headerRow.appendChild(createHeaderCell("Status"));
        headerRow.appendChild(createHeaderCell("Actions"));
        tableHead.appendChild(headerRow);
    }

    function createHeaderCell(text) {
        const th = document.createElement("th");
        th.textContent = text;
        return th;
    }

    function createSubGroupRow(item, type, index) {
        const row = document.createElement('tr');
        const serialCell = document.createElement('td');
        serialCell.textContent = index + 1;
        row.appendChild(serialCell);
        const nameCell = document.createElement('td');
        const subgroupName = document.createElement('span');
        subgroupName.textContent = item.test_group_name;
        subgroupName.classList.add('fw-medium');
        nameCell.appendChild(subgroupName);
        const groupCell = document.createElement('td');
        groupCell.textContent = item.mother_group;
        groupCell.style.fontSize = '15px';
        row.appendChild(groupCell);
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        statusBadge.textContent = item.active_status ? 'Active' : 'Inactive';
        statusBadge.className = item.active_status ? 'badge bg-label-success' : 'badge bg-label-danger';
        statusCell.appendChild(statusBadge);
        const actionsCell = document.createElement('td');
        const editButton = document.createElement('button');
        editButton.textContent = 'Edit';
        editButton.className = 'btn btn-sm btn-warning';
        editButton.dataset.id = item.test_group_id;
        editButton.addEventListener('click', () => {
            populateFormForEdit(item, type);
        });
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.className = 'btn btn-sm btn-danger';
        deleteButton.dataset.id = item.test_group_id;
        deleteButton.addEventListener('click', () => handleDelete(item.test_group_id, 'subGroup'));
        editButton.style.marginRight = '5px';
        actionsCell.appendChild(editButton);
        actionsCell.appendChild(deleteButton);
        row.appendChild(nameCell);
        row.appendChild(statusCell);
        row.appendChild(actionsCell);
        return row;
    }

    function createSubSubGroupRow(item, type, index) {
        const row = document.createElement('tr');
        const serialCell = document.createElement('td');
        serialCell.textContent = index + 1;
        row.appendChild(serialCell);
        const nameCell = document.createElement('td');
        const subSubGroupName = document.createElement('span');
        subSubGroupName.textContent = item.test_group_name;
        subSubGroupName.classList.add('fw-medium');
        nameCell.appendChild(subSubGroupName);
        const groupCell = document.createElement('td');
        groupCell.textContent = item.mother_group;
        groupCell.style.fontSize = '15px';
        row.appendChild(groupCell);
        const subGroupCell = document.createElement('td');
        subGroupCell.textContent = item.mother_subgroup;
        subGroupCell.style.fontSize = '15px';
        row.appendChild(subGroupCell);
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        statusBadge.textContent = item.active_status ? 'Active' : 'Inactive';
        statusBadge.className = item.active_status ? 'badge bg-label-success' : 'badge bg-label-danger';
        statusCell.appendChild(statusBadge);
        const actionsCell = document.createElement('td');
        const editButton = document.createElement('button');
        editButton.textContent = 'Edit';
        editButton.className = 'btn btn-sm btn-warning';
        editButton.dataset.id = item.test_group_id;
        editButton.addEventListener('click', () => {
            populateFormForEdit(item, type);
        });
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.className = 'btn btn-sm btn-danger';
        deleteButton.dataset.id = item.test_group_id;
        deleteButton.addEventListener('click', () => handleDelete(item.test_group_id, 'subSubGroup'));
        editButton.style.marginRight = '5px';
        actionsCell.appendChild(editButton);
        actionsCell.appendChild(deleteButton);
        row.appendChild(nameCell);
        row.appendChild(statusCell);
        row.appendChild(actionsCell);
        return row;
    }

    function createEditButton(item, type) {
        const button = document.createElement('button');
        button.className = 'btn btn-warning btn-sm me-1';
        button.textContent = 'Edit';
        button.addEventListener('click', () => {
            populateFormForEdit(item, type);
        });
        return button;
    }

    function populateFormForEdit(item, type) {
        if (type === 'group') {
            document.getElementById('grpName').value = item.test_group_name || '';
            document.getElementById('groupActiveStatus').checked = item.active_status ? true : false;
            document.getElementById('editGroupItemId').value = item.test_group_id;
            const submitButton = document.getElementById('submitButtonGroup');
            submitButton.textContent = '';
            const icon = document.createElement('i');
            icon.classList.add('fa-regular', 'fa-pen-to-square');
            submitButton.appendChild(icon);
            submitButton.appendChild(document.createTextNode('\u00A0'));
            submitButton.appendChild(document.createTextNode('Update'));
            submitButton.classList.add('btn-warning');
            submitButton.classList.remove('btn-primary');
            document.getElementById('formTypeGroup').value = type;
        } else if (type === 'subGroup') {
            let dropdown = document.getElementById("bs_validation_group_inSubGroup");
            if (dropdown) {
                $(dropdown).prop("disabled", true);
                $(dropdown).select2();
            }
            document.getElementById('subGrpName').value = item.test_group_name || '';
            document.getElementById('subGroupActiveStatus').checked = item.active_status ? true : false;
            document.getElementById('editSubGroupItemId').value = item.test_group_id;
            const submitButton = document.getElementById('submitButtonSubGroup');
            submitButton.textContent = '';
            const icon = document.createElement('i');
            icon.classList.add('fa-regular', 'fa-pen-to-square');
            submitButton.appendChild(icon);
            submitButton.appendChild(document.createTextNode('\u00A0'));
            submitButton.appendChild(document.createTextNode('Update'));
            const groupDropdown = document.getElementById('bs_validation_group_inSubGroup');
            groupDropdown.value = item.group_id;
            const event = new Event('change', {
                bubbles: true
            });
            groupDropdown.dispatchEvent(event);
            submitButton.classList.add('btn-warning');
            submitButton.classList.remove('btn-primary');
            document.getElementById('formTypeSubGroup').value = type;
        } else {
            let dropdown = document.getElementById("bs_validation_group_inSubSubGroup");
            if (dropdown) {
                $(dropdown).prop("disabled", true);
                $(dropdown).select2();
            }
            let subdropdown = document.getElementById("bs_validation_subGroup_inSubSubGroup");
            if (subdropdown) {
                $(subdropdown).prop("disabled", true);
                $(subdropdown).select2();
            }
            document.getElementById('subSubGrpName').value = item.test_group_name || '';
            document.getElementById('subSubGroupActiveStatus').checked = item.active_status ? true : false;
            document.getElementById('editSubSubGroupItemId').value = item.test_group_id;
            const submitButton = document.getElementById('submitButtonSubSubGroup');
            submitButton.textContent = '';
            const icon = document.createElement('i');
            icon.classList.add('fa-regular', 'fa-pen-to-square');
            submitButton.appendChild(icon);
            submitButton.appendChild(document.createTextNode('\u00A0'));
            submitButton.appendChild(document.createTextNode('Update'));
            const groupDropdown = document.getElementById('bs_validation_group_inSubSubGroup');
            groupDropdown.value = item.group_id;
            const eventGroup = new Event('change', {
                bubbles: true
            });
            groupDropdown.dispatchEvent(eventGroup);
            const subGroupDropdown = document.getElementById('bs_validation_subGroup_inSubSubGroup');
            subGroupDropdown.value = item.subgroup_id;
            const event = new Event('change', {
                bubbles: true
            });
            subGroupDropdown.dispatchEvent(event);
            submitButton.classList.add('btn-warning');
            submitButton.classList.remove('btn-primary');
            document.getElementById('formTypeSubSubGroup').value = type;
        }
    }

    function createDeleteButton(item, type) {
        const button = document.createElement('button');
        button.className = 'btn btn-danger btn-sm';
        button.textContent = 'Delete';
        button.addEventListener('click', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action will delete the item permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const id = item.test_group_id;
                    deleteItem(id, type);
                }
            });
        });
        return button;
    }

    function handleDelete(test_group_id, type) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action will delete the item permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const id = test_group_id;
                deleteItem(id, type);
            }
        });
    }
    async function deleteItem(id, type) {
        try {
            let url = "";
            switch (type) {
                case 'group':
                    url = "/test-group/deleteGroup";
                    break;
                case 'subGroup':
                    url = "/test-group/deleteSubGroup";
                    break;
                case 'subSubGroup':
                    url = "/test-group/deleteSubSubGroup";
                    break;
                default:
                    showToast('error', 'Unknown type', 'Unknown type for delete operation');
                    return;
            }
            const response = await apiRequest({
                url: url,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                data: {
                    test_group_id: id,
                },
                onSuccess: () => {
                    showToast('success', `${type} deleted successfully.`);
                    refreshData(type);
                },
                onError: (error) => {
                    showToast('error', 'Error occurred: ' + error);
                }
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Something went wrong. Please try again later.',
                confirmButtonText: 'Okay'
            });
        }
    }

    function refreshData(type) {
        switch (type) {
            case 'group':
                fetchAndPopulateGroups();
                break;
            case 'subGroup':
                fetchAndPopulateSubGroups();
                break;
            case 'subSubGroup':
                fetchAndPopulateSubSubGroups();
                break;
        }
    }

    $('#bs_validation_group_inSubSubGroup').on('change', function () {
        var selectedValue = $(this).val();
        var selectedText = $(this).find(':selected').text();
        fetchAndPopulateSubGroupsinDropdown(groupId = selectedValue)
    });
});

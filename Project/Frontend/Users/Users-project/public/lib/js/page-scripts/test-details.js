class TestResultsManager {
    constructor() {
        this.testData = {};
        this.csrfToken = '';
        this.saveButtonId = 'saveResultsBtn';
        this.apiEndpoint = '/ohc/test-details/save-results';
        this.testCodeValue = '';
    }
    init(testData, testCode) {
        this.testData = testData || {};
        this.testCodeValue = testCode || '';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.setupEventListeners();
        this.detectPatientGender();
    }
    setupEventListeners() {
        const saveButton = document.getElementById(this.saveButtonId);
        if (saveButton) {
            saveButton.addEventListener('click', () => this.handleSaveResults());
        }
    }
    detectPatientGender() {
        const genderElement = document.getElementById('patient-gender');
        if (genderElement) {
            const gender = genderElement.textContent.trim().toLowerCase();
            if (gender === 'male' || gender === 'female') {
                console.log('Patient gender is:', gender);
            }
        }
    }
    findMasterTestIdByName(testName) {
        for (const groupName in this.testData) {
            const group = this.testData[groupName];
            for (const subgroupKey in group) {
                const subgroup = group[subgroupKey];
                if (typeof subgroup === 'object' && subgroup !== null && subgroup.name === testName) {
                    return subgroup.master_test_id;
                }
                if (typeof subgroup === 'object' && subgroup !== null && !Array.isArray(subgroup) && !subgroup.name) {
                    for (const testKey in subgroup) {
                        const test = subgroup[testKey];
                        if (typeof test === 'object' && test !== null && test.name === testName) {
                            return test.master_test_id;
                        }
                    }
                }
                if (Array.isArray(subgroup)) {
                    for (const test of subgroup) {
                        if (typeof test === 'object' && test !== null && test.name === testName) {
                            return test.master_test_id;
                        }
                    }
                }
                if (typeof subgroup === 'object' && subgroup !== null && !Array.isArray(subgroup) && !subgroup.name) {
                    for (const subSubGroupName in subgroup) {
                        const subSubGroup = subgroup[subSubGroupName];
                        if (Array.isArray(subSubGroup)) {
                            for (const test of subSubGroup) {
                                if (typeof test === 'object' && test !== null && test.name === testName) {
                                    return test.master_test_id;
                                }
                            }
                        }
                    }
                }
            }
        }
        return null;
    }
    collectTestResults() {
        const testResults = [];
        const testInputs = document.querySelectorAll('table input.form-control');
        testInputs.forEach(input => {
            const testRow = input.closest('tr');
            if (!testRow) {
                console.warn('Could not find parent row for input:', input);
                return;
            }

            const testNameElement = testRow.querySelector('.drug-name');
            if (testNameElement) {
                const testName = testNameElement.textContent.trim();
                const masterTestId = this.findMasterTestIdByName(testName);

                if (masterTestId) {
                    const testValue = input.value.trim();
                    testResults.push({
                        master_test_id: masterTestId,
                        test_result: testValue !== '' ? testValue : null,
                        test_code: this.testCodeValue
                    });
                } else {
                    console.warn(`Could not find master_test_id for test: ${testName}`);
                }
            }
        });
        return testResults;
    } handleSaveResults() {
        const testResults = this.collectTestResults();
        if (testResults.length === 0) {
            this.showAlert({
                title: 'No Results',
                text: 'Please enter at least one test result',
                icon: 'warning'
            });
            return;
        }
        Swal.fire({
            title: 'Confirm Submission',
            text: 'Are you sure you want to save these test results?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save results',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submitResults(testResults);
            }
        });
    }
    submitResults(testResults) {
        const employeeId = testDetailsData['employee_id'] || '';
        apiRequest({
            url: this.apiEndpoint,
            method: 'POST',
            data: {
                test_results: testResults,
                employee_id: employeeId
            },
            onSuccess: (data) => {
                if (data.result) {
                    this.showAlert({
                        title: 'Success',
                        text: 'Test results saved successfully',
                        icon: 'success'
                    });
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    this.showAlert({
                        title: 'Error',
                        text: data.message || 'Failed to save test results',
                        icon: 'error'
                    });
                }
            },
            onError: (error) => {
                console.error('Error:', error);
                this.showAlert({
                    title: 'Error',
                    text: 'An unexpected error occurred',
                    icon: 'error'
                });
            }
        });
    }
    showAlert(options) {
        if (typeof Swal !== 'undefined') {
            Swal.fire(options);
        } else {
            console.warn('SweetAlert not loaded. Message:', options.text);
            alert(options.text);
        }
    }
}
document.addEventListener('DOMContentLoaded', function () {
    if (typeof testDetailsData === 'undefined') {
        console.error('Test data not found. Make sure testDetailsData is defined before this script runs.');
        return;
    }
    const manager = new TestResultsManager();
    const testCode = testDetailsData.test_code || '';
    manager.init(testDetailsData.tests, testCode);
});
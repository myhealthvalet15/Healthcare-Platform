document.addEventListener("DOMContentLoaded", function () {
    const testedOnInput = document.getElementById("tested-on-date");
    const reportedOnInput = document.getElementById("reported-on-date");
    const testedOnValue = testedOnInput.value;
    const reportedOnValue = reportedOnInput.value;
    if (!testedOnValue && !reportedOnValue) {
        testedOnInput.disabled = false;
        reportedOnInput.disabled = true;
    } else {
        testedOnInput.disabled = false;
        reportedOnInput.disabled = false;
    }
    testedOnInput.addEventListener("change", function () {
        if (testedOnInput.value) {
            reportedOnInput.disabled = false;
        } else {
            reportedOnInput.disabled = true;
        }
    });
});
class TestResultsManager {
    constructor() {
        this.testData = {};
        this.csrfToken = '';
        this.saveButtonId = 'saveResultsBtn';
        this.apiEndpoint = '/ohc/test-details/save-results';
        this.testCodeValue = '';
        this.uploadedFileBase64 = null;
        this.uploadedFileName = null;
        this.allowedFileTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        this.maxFileSize = 10 * 1024 * 1024;
    }
    init(testData, testCode) {
        this.testData = testData || {};
        this.testCodeValue = testCode || '';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.setupEventListeners();
        this.detectPatientGender();
        this.setupFileUpload();
    }
    setupEventListeners() {
        const saveButton = document.getElementById(this.saveButtonId);
        if (saveButton) {
            saveButton.addEventListener('click', () => this.handleSaveResults());
        }
    }
    setupFileUpload() {
        const fileInput = document.getElementById('file-upload');
        if (fileInput) {
            fileInput.addEventListener('change', (event) => this.handleFileUpload(event));
        }
    }
    handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) {
            this.resetFileData();
            return;
        }
        if (!this.isValidFileType(file)) {
            this.showAlert({
                title: 'Invalid File Type',
                text: 'Please upload only PDF, Word documents (.doc, .docx), or Excel files (.xls, .xlsx)',
                icon: 'error'
            });
            this.clearFileInput();
            return;
        }
        if (file.size > this.maxFileSize) {
            this.showAlert({
                title: 'File Too Large',
                text: `File size must be less than ${this.maxFileSize / (1024 * 1024)}MB`,
                icon: 'error'
            });
            this.clearFileInput();
            return;
        }
        this.convertToBase64(file)
            .then(base64String => {
                this.uploadedFileBase64 = base64String;
                this.uploadedFileName = file.name;
                console.log('File uploaded successfully:', file.name);
                this.showAlert({
                    title: 'File Uploaded',
                    text: `File "${file.name}" uploaded successfully`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Error converting file to base64:', error);
                this.showAlert({
                    title: 'Upload Error',
                    text: 'Failed to process the uploaded file',
                    icon: 'error'
                });
                this.clearFileInput();
            });
    }
    isValidFileType(file) {
        if (this.allowedFileTypes.includes(file.type)) {
            return true;
        }
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx'];
        return allowedExtensions.some(ext => fileName.endsWith(ext));
    }
    convertToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => {
                const base64String = reader.result.split(',')[1];
                resolve(base64String);
            };
            reader.onerror = () => {
                reject(new Error('Failed to read file'));
            };
            reader.readAsDataURL(file);
        });
    }
    clearFileInput() {
        const fileInput = document.getElementById('file-upload');
        if (fileInput) {
            fileInput.value = '';
        }
        this.resetFileData();
    }
    resetFileData() {
        this.uploadedFileBase64 = null;
        this.uploadedFileName = null;
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
    }
    handleSaveResults() {
        const testResults = this.collectTestResults();
        const tested_on_date = document.getElementById('tested-on-date').value;
        const reported_on_date = document.getElementById('reported-on-date').value;
        Swal.fire({
            title: 'Confirm Submission',
            text: 'Are you sure you want to save these test results?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save results',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submitResults(testResults, tested_on_date, reported_on_date);
            }
        });
    }
    submitResults(testResults, tested_on_date, reported_on_date) {
        const employeeId = testDetailsData['employee_id'] || '';
        const requestData = {
            test_results: testResults,
            employee_id: employeeId,
            tested_on: tested_on_date,
            reported_on: reported_on_date,
        };
        if (this.uploadedFileBase64 && this.uploadedFileName) {
            requestData.document_file = this.uploadedFileBase64;
            requestData.document_filename = this.uploadedFileName;
        }
        apiRequest({
            url: this.apiEndpoint,
            method: 'POST',
            data: requestData,
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
                    text: error,
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
} document.addEventListener('DOMContentLoaded', function () {
    if (typeof testDetailsData === 'undefined') {
        console.error('Test data not found. Make sure testDetailsData is defined before this script runs.');
        return;
    }
    const manager = new TestResultsManager();
    const testCode = testDetailsData.test_code || '';
    manager.init(testDetailsData.tests, testCode);
});
// resources/js/formValidator.js

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-add-new-record');
    
    if (form) {
        form.addEventListener('submit', function (e) {
            const departmentName = document.getElementById('hl1_name').value;
            const departmentCode = document.getElementById('hl1_code').value;

            // Validate Department Name
            if (!departmentName.trim()) {
                alert('Department name is required');
                e.preventDefault(); // Prevent form submission
                return false;
            }

            // Validate Department Code
            if (!departmentCode.trim()) {
                alert('Department code is required');
                e.preventDefault(); // Prevent form submission
                return false;
            }

            // Optionally: Add more validation logic for other fields if needed

            // If everything is valid, allow form submission
            return true;
        });
    }
});

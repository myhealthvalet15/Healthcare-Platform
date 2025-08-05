document.getElementById('editProfileBtn').addEventListener('click', function () {
    document.getElementById('editProfileCard').style.display = 'block';
    fetch(employeeDetailsUrl)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editFirstName').value = data.employee_firstname || '';
            document.getElementById('editLastName').value = data.employee_lastname || '';
            document.getElementById('editDob').value = data.employee_dob || '';
            document.getElementById('editGender').value = (data.employee_gender || '').toLowerCase();
            document.getElementById('editPhone').value = data.employee_contact_number || '';
            document.getElementById('editAltEmail').value = data.alternative_email || '';
            document.getElementById('editAadharId').value = data.aadhar_id || '';
            document.getElementById('editAbhaId').value = data.abha_id || '';
            document.getElementById('editArea').value = data.area || '';
            document.getElementById('editZipcode').value = data.zipcode || '';
        });
}); document.addEventListener('DOMContentLoaded', function () {
    fetchEmployeeDetails(employeeId);
});
function fetchEmployeeDetails(employeeId) {
    apiRequest({
        url: employeeDetailsUrl,
        method: "GET",
        onSuccess: (data) => {
            if (data && data.employee_id) {
                document.getElementById("empName").textContent = `${data.employee_firstname} ${data.employee_lastname}`;
                document.getElementById("empType").textContent = capitalizeFirstLetter(data.employee_type_name || '-');
                document.getElementById("empDepartment").textContent = capitalizeFirstLetter(data.employee_department || '-');
                document.getElementById("empDesignation").textContent = capitalizeFirstLetter(data.employee_designation || '-');
                document.getElementById("empLocation").textContent = data.employee_location_name || '-';
                document.getElementById("empDateOfJoining").textContent = data.dateOfJoining || '-';
                document.getElementById("profileImage").src = data.profile_pic || "{{ asset('assets/img/avatars/1.png') }}";
                document.getElementById("bannerImage").src = data.banner || "{{ asset('assets/img/pages/profile-banner.png') }}";
                const infoList = document.getElementById("employeeInfoList");
                infoList.innerHTML = '';
                const firstLine = document.createElement('li');
                firstLine.className = 'list-inline-item d-flex gap-3 align-items-center flex-wrap';
                firstLine.innerHTML = `
            <span><i class="ti ti-mail ti-lg"></i> ${data.employee_email || 'N/A'}</span>
            <span><i class="ti ti-phone ti-lg"></i> ${data.employee_contact_number || 'N/A'}</span>
            <span><i class="ti ti-user ti-lg"></i> ${data.employee_age || '?'} yrs • ${capitalizeFirstLetter(data.employee_gender || '')}</span>
          `;
                infoList.appendChild(firstLine);
                const connectedBtn = document.getElementById("connectedBtn");
                if (connectedBtn) {
                    connectedBtn.innerHTML = `<i class='ti ti-user-check ti-xs me-2'></i>Connected to ${data.employee_corporate_name || 'Corporate'}`;
                }
            }
        },
        onError: (error) => console.error('Error fetching employee details:', error)
    });
}
function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
document.getElementById('editProfileForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = document.getElementById('editProfileForm');
    const firstName = form.first_name.value.trim();
    const lastName = form.last_name.value.trim();
    const dob = form.date_of_birth.value;
    const gender = form.gender.value;
    const phone = form.contact_number.value.trim();
    const alternativeEmail = form.alternative_email.value.trim();
    const aadharId = form.aadhar_id.value.trim();
    const abhaId = form.abha_id.value.trim();
    const area = form.area.value.trim();
    const zipcode = form.zipcode.value.trim();
    const profilePicInput = document.getElementById('editProfilePic');
    const bannerInput = document.getElementById('editBanner');
    const profilePic = profilePicInput.files[0];
    const banner = bannerInput.files[0];
    const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/tiff', 'image/webp'];
    if (!firstName) return showToast('error', 'Validation Error', 'First name is required.');
    if (!lastName) return showToast('error', 'Validation Error', 'Last name is required.');
    if (!dob) return showToast('error', 'Validation Error', 'Date of birth is required.');
    if (!gender) return showToast('error', 'Validation Error', 'Gender is required.');
    if (!phone) return showToast('error', 'Validation Error', 'Phone number is required.');
    if (profilePic) {
        if (!validImageTypes.includes(profilePic.type)) {
            return showToast('error', 'Invalid Image', 'Profile picture must be a valid image type.');
        }
        if (profilePic.size > 200 * 1024) {
            return showToast('error', 'File Too Large', 'Profile picture must be under 200KB.');
        }
    }
    if (banner) {
        if (!validImageTypes.includes(banner.type)) {
            return showToast('error', 'Invalid Image', 'Banner image must be a valid image type.');
        }
        if (banner.size > 1024 * 1024) {
            return showToast('error', 'File Too Large', 'Banner image must be under 1MB.');
        }
    }
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            if (!file) resolve(null);
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
            reader.readAsDataURL(file);
        });
    }
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update your profile?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!"
    }).then(async (result) => {
        if (!result.isConfirmed) return;
        // const employeeId = "{{ session('employee_id') }}";
        const updateUrl = `/UserEmployee/updateProfileDetails/${employeeId}`;
        try {
            const profilePicBase64 = await fileToBase64(profilePic);
            const bannerBase64 = await fileToBase64(banner);
            const payload = {
                first_name: firstName,
                last_name: lastName,
                date_of_birth: dob,
                gender: gender,
                contact_number: phone,
                alternative_email: alternativeEmail,
                aadhar_id: aadharId,
                abha_id: abhaId,
                area: area,
                zipcode: zipcode,
                profile_pic: profilePicBase64,
                banner: bannerBase64
            };
            const response = await fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });
            if (!response.ok) throw new Error('Server error');
            const data = await response.json();
            showToast('success', 'Success', 'Profile updated successfully!');
            fetchEmployeeDetails(employeeId);
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                if (modal) modal.hide();
                document.getElementById('editProfileCard').style.display = 'none';
            }, 400);
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Failed to update profile.');
        }
    });
});
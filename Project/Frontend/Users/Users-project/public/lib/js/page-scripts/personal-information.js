document.getElementById('editProfileBtn').addEventListener('click', () => {
  fetch(employeeDetailsUrl)
    .then(res => res.json())
    .then(data => {
      document.getElementById('editFirstName').value = data.employee_firstname || '';
      document.getElementById('editLastName').value = data.employee_lastname || '';
      document.getElementById('editPhone').value = data.employee_contact_number || '';
    });
});
document.getElementById('editProfileForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = document.getElementById('editProfileForm');
  const firstName = form.first_name.value.trim();
  const lastName = form.last_name.value.trim();
  const phone = form.contact_number.value.trim();
  const profilePicInput = document.getElementById('editProfilePic');
  const bannerInput = document.getElementById('editBanner');
  const profilePic = profilePicInput.files[0];
  const banner = bannerInput.files[0];
  const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/tiff', 'image/webp'];
  if (!firstName) return showToast('error', 'Validation Error', 'First name is required.');
  if (!lastName) return showToast('error', 'Validation Error', 'Last name is required.');
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
    const employeeId = "{{ session('employee_id') }}";
    const updateUrl = `{{ route('update-profile-details', ['id' => ':id']) }}`.replace(':id', employeeId);
    try {
      const profilePicBase64 = await fileToBase64(profilePic);
      const bannerBase64 = await fileToBase64(banner);
      const payload = {
        first_name: firstName,
        last_name: lastName,
        contact_number: phone,
        profile_pic: profilePicBase64,
        banner: bannerBase64
      };
      const response = await apiRequest({
        url: updateUrl,
        method: 'POST',
        data: payload
      });
      if (!response.ok) throw new Error('Server error');
      const data = await response.json();
      showToast('success', 'Success', 'Profile updated successfully!');
      fetchEmployeeDetails(employeeId);
      setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
        modal.hide();
      }, 400);
    } catch (error) {
      console.error('Error:', error);
      showToast('error', 'Error', 'Failed to update profile.');
    }
  });
});

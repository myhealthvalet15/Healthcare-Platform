async function get2FAStatus() {
  await apiRequest({
    url: "/getUserDetails",
    method: "GET",
    onSuccess: (response) => {
      const isEnabled = response.data.two_factor_enabled === 1;
      const adminName = response.data.admin_name;
      const email = response.data.email;
      const checkbox = document.getElementById("2faToggle");
      if (checkbox) {
        checkbox.checked = isEnabled;
      }
      const nameElement = document.querySelector(".dropdown-item h6");
      const emailElement = document.querySelector(".dropdown-item .text-muted");
      if (nameElement) nameElement.textContent = adminName;
      if (emailElement) emailElement.textContent = email;
    },
    onError: (error) => {
    },
  });
}
async function toggle2FA(element) {
  const isEnable = element.checked ? 1 : 0;
  const url = `/toggle/2fa/${isEnable}`;
  const response = await apiRequest({
    url,
    method: "GET",
    onSuccess: (response) => {
      if (response.result) {
        const message = response.data.message;
        showToast("success", message);
      } else {
        showToast("error", "Failed to update 2FA: Internal server error");
      }
    },
    onError: (error) => {
      showToast("error", `Failed to update 2FA: ${error}`);
      element.checked = !isEnable;
    },
  });
}
document.addEventListener("DOMContentLoaded", function () {
  get2FAStatus();
});

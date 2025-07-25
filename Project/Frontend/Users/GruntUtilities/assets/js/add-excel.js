$(document).ready(function () {
  function fetchMasterUserCount() {
    apiRequest({
      url: "/corporate/getMasterUserDetails",
      method: "GET",
      onSuccess: (data) => {
        if (data.result) {
          document.getElementById("preloader_mc").style.display = "none";
          document.getElementById("masterUserDetailsContent").style.display =
            "block";
          document.getElementById("masterUserCount").innerText =
            data.data.count.toLocaleString() + " Users";
        } else {
          document.getElementById("preloader_mc").style.display = "none";
          document.getElementById("masterUserDetailsContent").style.display =
            "block";
          // console.error("Failed to fetch master user count.");
        }
      },
      onError: (error) => {
        document.getElementById("preloader_mc").style.display = "none";
        document.getElementById("masterUserDetailsContent").style.display =
          "block";
        // console.error("Error fetching master user count:", error);
      },
    });
  }
  const previewTemplate = `
    <div class="dz-preview dz-file-preview">
    <div class="dz-details">
    <div class="dz-thumbnail">
        <img data-dz-thumbnail>
        <span class="dz-nopreview">No preview</span>
        <div class="dz-success-mark"></div>
        <div class="dz-error-mark"></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
        <div class="progress">
        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
        </div>
    </div>
    <div class="dz-filename" data-dz-name></div>
    <div class="dz-size" data-dz-size></div>
    </div>
    </div>`;
  const dropzoneBasic = document.querySelector("#dropzone-basic");
  let myDropzone;
  if (dropzoneBasic) {
    myDropzone = new Dropzone(dropzoneBasic, {
      autoProcessQueue: false,
      previewTemplate: previewTemplate,
      parallelUploads: 1,
      maxFilesize: 5,
      addRemoveLinks: true,
      maxFiles: 1,
      acceptedFiles: ".xls,.xlsx",
      dictInvalidFileType: "You can only upload Excel files!",
      paramName: "file",
    });
    document
      .querySelector("#final-upload-btn")
      .addEventListener("click", function () {
        document.getElementById("preloader").style.display = "block";
        if (myDropzone.getQueuedFiles().length > 0) {
          let formData = new FormData();
          let file = myDropzone.getQueuedFiles()[0];
          formData.append("file", file);
          const corporateDropdown = document.getElementById("corporateDropdown");
          const locationDropdown = document.getElementById("locationDropdown");
          const corporateValue = corporateDropdown.options[
            corporateDropdown.selectedIndex
          ].disabled
            ? ""
            : corporateDropdown.value;
          const locationValue = locationDropdown.options[
            locationDropdown.selectedIndex
          ].disabled
            ? ""
            : locationDropdown.value;
          if (!corporateValue) {
            document.getElementById("preloader").style.display = "none";
            showToast("error", "Please select a corporate before uploading.");
            return;
          }
          if (!locationValue) {
            document.getElementById("preloader").style.display = "none";
            showToast("error", "Please select a location before uploading.");
            return;
          }
          showToast("info", "Uploading file, please wait...");
          formData.append("corporate_id", corporateValue);
          formData.append("location_id", locationValue);
          $.ajax({
            url: "/corporate/upload/add-corporate-users",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
              Accept: "application/json",
              "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            },
            success: function (response) {
              document.getElementById("preloader").style.display = "none";
              myDropzone.removeAllFiles(true);
              if (response.result === 'success') {
                const successMessage = `${response.message} ${response.valid_rows} rows accepted, ${response.errors} rows will be rejected.`;
                showToast("success", successMessage);
                window.open(
                  "/corporate/view-uploaded-users/" +
                  corporateValue +
                  "/" +
                  locationValue +
                  "/" +
                  response.file_name,
                  "_blank"
                );
                corporateDropdown.selectedIndex = 0;
                locationDropdown.selectedIndex = 0;
              } else {
                showToast("error", response.message);
              }
            },
            error: function (xhr, status, error) {
              document.getElementById("preloader").style.display = "none";
              const response = JSON.parse(xhr.responseText);
              showToast("error", response.message);
            },
          });
        } else {
          showToast("error", "Please add a file before uploading.");
          document.getElementById("preloader").style.display = "none";
        }
      });
  }
  const apiUrl =
    "https://mhv-admin.hygeiaes.com/corporate/getUploadedExcelStatus";
  const fileContentUrl =
    "https://mhv-admin.hygeiaes.com/corporate/getUploadedExcelFileContent";
  function fetchAndPopulateTable() {
    document.getElementById("preloader_history").style.display = "block";
    apiRequest({
      url: apiUrl,
      method: "GET",
      onSuccess: (result) => {
        document.getElementById("preloader_history").style.display = "none";
        if (
          result.result === "success" &&
          result.data.data &&
          result.data.data.files
        ) {
          const files = result.data.data.files;
          const tableBody = document.getElementById("fileTableBody");
          tableBody.innerHTML = "";
          if (files.length === 0) {
            const row = document.createElement("tr");
            const noDataCell = document.createElement("td");
            noDataCell.colSpan = 3;
            noDataCell.className = "text-center";
            noDataCell.textContent = "No History Available.";
            row.appendChild(noDataCell);
            tableBody.appendChild(row);
          } else {
            files.forEach((file) => {
              const row = document.createElement("tr");
              const fileNameCell = document.createElement("td");
              fileNameCell.innerHTML = `${file.file_name} &nbsp;
    <a href="" onclick="downloadFile(${file.id}, this)" id="download-link-${file.id}">
        <i class="fa fa-download" aria-hidden="true"></i>
    </a>
    <div id="spinner-${file.id}" class="spinner-border spinner-border-sm text-primary d-none" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>`;
              const statusCell = document.createElement("td");
              const statusClass =
                file.status === "accepted"
                  ? "bg-success"
                  : file.status === "partial"
                    ? "bg-warning"
                    : "bg-danger";
              statusCell.innerHTML = `<span class="badge ${statusClass}">${file.status.charAt(0).toUpperCase() + file.status.slice(1)
                }</span>`;
              const reasonCell = document.createElement("td");
              reasonCell.textContent = file.denied_reason || "-";
              row.appendChild(fileNameCell);
              row.appendChild(statusCell);
              row.appendChild(reasonCell);
              tableBody.appendChild(row);
            });
          }
        } else {
          document.getElementById("preloader_history").style.display = "none";
          showToast("error", "Unexpected API response format");
          // console.error("Unexpected API response format", result);
        }
      },
      onError: (error) => {
        document.getElementById("preloader_history").style.display = "none";
        showToast("error", error);
        // console.error("Error fetching data from API", error);
      },
    });
  }
  window.downloadFile = function (fileId, linkElement) {
    event.preventDefault();
    const downloadLink = document.getElementById(`download-link-${fileId}`);
    const spinner = document.getElementById(`spinner-${fileId}`);
    downloadLink.classList.add("d-none");
    spinner.classList.remove("d-none");
    setTimeout(() => {
      spinner.classList.add("d-none");
      downloadLink.classList.remove("d-none");
      apiRequest({
        url: `${fileContentUrl}/${fileId}`,
        method: "GET",
        onSuccess: (result) => {
          if (result.result === "success" && result.data && result.data.file_base64) {
            const base64Data = result.data.file_base64;
            const fileName = result.data.file_name;
            const binaryData = atob(base64Data);
            const arrayBuffer = new Uint8Array(binaryData.length);
            for (let i = 0; i < binaryData.length; i++) {
              arrayBuffer[i] = binaryData.charCodeAt(i);
            }
            const blob = new Blob([arrayBuffer], {
              type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = fileName;
            link.click();
            showToast("success", `File ${fileName} downloaded successfully.`);
          } else {
            showToast("error", "Failed to retrieve file content.");
            // console.error("Error in file content response", result);
          }
        },
        onError: (error) => {
          showToast("error", error);
          // console.error("Error downloading file", error);
        }
      });
    }, 2000);
  }
  const corporateDropdown = document.getElementById("corporateDropdown");
  const locationDropdown = document.getElementById("locationDropdown");
  function loadCorporates() {
    apiRequest({
      url: "https://mhv-admin.hygeiaes.com/corporate/getAllCorporates",
      method: "GET",
      onSuccess: (data) => {
        if (data.result) {
          if (data.data.length === 0) {
            showToast("error", "No corporates found");
            return;
          }
          corporateDropdown.innerHTML =
            "<option selected disabled>Select Corporate</option>";
          data.data.forEach((item) => {
            const option = document.createElement("option");
            option.value = item.corporate_id;
            option.textContent = item.corporate_name;
            corporateDropdown.appendChild(option);
          });
          corporateDropdown.disabled = false;
        }
      },
      onError: (error) => {
        corporateDropdown.innerHTML =
          "<option selected disabled>Error loading data</option>";
        showToast("error", error);
      },
    });
  }
  function loadLocations(corporateId) {
    apiRequest({
      url: `https://mhv-admin.hygeiaes.com/corporate/getAllLocations/${corporateId}`,
      method: "GET",
      onSuccess: (data) => {
        if (data.result) {
          if (data.data.length === 0) {
            locationDropdown.innerHTML =
              "<option selected disabled>No locations found</option>";
            locationDropdown.disabled = true;
            showToast("error", "No locations found");
            return;
          }
          locationDropdown.innerHTML =
            "<option selected disabled>Select Location</option>";
          data.data.forEach((item) => {
            const option = document.createElement("option");
            option.value = item.location_id;
            option.textContent = item.display_name;
            locationDropdown.appendChild(option);
          });
          locationDropdown.disabled = false;
        }
      },
      onError: (error) => {
        locationDropdown.innerHTML =
          "<option selected disabled>Error loading data</option>";
        locationDropdown.disabled = true;
        showToast("error", error);
      },
    });
  }
  corporateDropdown.addEventListener("change", (event) => {
    const selectedCorporateId = event.target.value;
    locationDropdown.innerHTML =
      "<option selected disabled>Loading...</option>";
    locationDropdown.disabled = true;
    loadLocations(selectedCorporateId);
  });
  loadCorporates();
  fetchAndPopulateTable();
  fetchMasterUserCount();
});

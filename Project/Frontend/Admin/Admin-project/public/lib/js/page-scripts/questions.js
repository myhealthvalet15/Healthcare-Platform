$(document).ready(function () {
    $("#masterTests").change(function () {
        if ($("#masterTests").val()) {
            $("#test-header").prop("selected", false);
        } else {
            $("#test-header").prop("selected", true);
        }
    });
    fetchTestNames();
    let masterTestResponse = {};
    async function fetchTestNames() {
        try {
            const data = await apiRequest({
                url: "/hra/master-test-names",
                method: "GET",
                onSuccess: (data) => {
                    masterTestResponse = data;
                    const selectElement = document.getElementById("masterTests");
                    if (data && typeof data === "object") {
                        for (const testId in data) {
                            if (data.hasOwnProperty(testId)) {
                                const testName = data[testId];
                                const option = document.createElement("option");
                                option.value = testId;
                                option.textContent = testName;
                                selectElement.appendChild(option);
                            }
                        }
                    } else {
                        showToast("error", "Invalid data format, " + data);
                    }
                },
                onError: (error) => {
                    showToast("error", "Error Fetching Test Records, " + error);
                },
            });
        } catch (error) {
            showToast("error", "Error Fetching Test Records " + error);
        }
    }
    var num = 1;
    let borderColor, bodyBg, headingColor;
    if (isDarkStyle) {
        borderColor = config.colors_dark.borderColor;
        bodyBg = config.colors_dark.bodyBg;
        headingColor = config.colors_dark.headingColor;
    } else {
        borderColor = config.colors.borderColor;
        bodyBg = config.colors.bodyBg;
        headingColor = config.colors.headingColor;
    }
    var preloaderHtml =
        '<div class="preloader-container" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p>Fetching Questions...</p></div>';
    $("body").append(preloaderHtml);
    var dt_questions_list_table = $(".datatables-category-list");
    dt_questions_list_table.hide();
    var preloaderTimeout;
    preloaderTimeout = setTimeout(function () {
        $(".preloader-container").fadeIn();
    }, 300);
    let getAllQuestionsResponse = {};
    if (dt_questions_list_table.length) {
        var dt_questions = dt_questions_list_table.DataTable({
            ajax: {
                url: "https://mhv-admin.hygeiaes.com/hra/get-all-questions",
                type: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                dataSrc: function (json) {
                    getAllQuestionsResponse = json;
                    return json.data;
                },
                error: function (xhr, status, error) {
                    showToast("error", "An error occurred while fetching questions.");
                },
            },
            columns: [
                { data: "question" },
                { data: "gender" },
                { data: "answer" },
                { data: "" },
            ],
            order: [0, "asc"],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 4,
                    render: function (data, type, full) {
                        let testData = full["test_names"] ? full["test_names"] : {};
                        let testInfo = Object.values(testData).join(", ");
                        return (
                            '<div class="d-flex flex-column justify-content-center">' +
                            '<span class="text-heading text-wrap fw-medium">' +
                            `${num++}` +
                            ". " +
                            data +
                            "</span>" +
                            '<small class="text-muted">' +
                            "Option Type: " +
                            full["types"] +
                            "<br>" +
                            `Tests Included: ${testInfo ? testInfo : "N/A"}` +
                            "</small>" +
                            "</div>"
                        );
                    },
                },
                {
                    targets: 1,
                    render: function (data) {
                        let genderArray = [];
                        try {
                            genderArray = JSON.parse(data);
                        } catch (e) {
                            genderArray = [];
                        }

                        const genderClasses = {
                            male: "bg-label-primary",
                            female: "bg-label-danger",
                            third_gender: "bg-label-warning"
                        };

                        let genderBadges = genderArray.map(gender => {
                            let genderClass = genderClasses[gender] || "bg-label-secondary";
                            return `<span class="badge ${genderClass}">${gender}</span>`;
                        });

                        return genderBadges.join(" ");
                    }
                },
                {
                    targets: -1,
                    title: "Actions",
                    render: function (data, type, full) {
                        return (
                            '<div class="d-flex align-items-sm-center justify-content-sm-center">' +
                            '<button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light edit-question" data-question-id="' +
                            full.question_id +
                            '" data-question="' +
                            full.question +
                            '" data-types="' +
                            full.types +
                            '" data-status="' +
                            full.active_status +
                            '"><i class="ti ti-edit"></i></button>' +
                            '<button class="btn btn-icon btn-text-secondary rounded-pill waves-effect waves-light delete-question" data-question-id="' +
                            full.question_id +
                            '"><i class="ti ti-trash"></i></button>' +
                            "</div>"
                        );
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full) {
                        let dataObj = data ? JSON.parse(data) : {};
                        let pointsObj = full["points"] ? JSON.parse(full["points"]) : {};
                        let compObj = full["comp_value"]
                            ? JSON.parse(full["comp_value"])
                            : {};
                        let result = Object.keys(dataObj).map((key, index) => {
                            let dataValue = dataObj[key] || "-";
                            let pointsValue = Object.values(pointsObj)[index] || "-";
                            let compValue = Object.values(compObj)[index] || "-";
                            return `${dataValue} / ${pointsValue} / ${compValue}`;
                        });
                        return (
                            '<div class="d-flex flex-column justify-content-center">' +
                            '<span class="text-heading text-wrap fw-medium">' +
                            result.join("<br>") +
                            "</span>" +
                            "</div>"
                        );
                    },
                },
            ],
            dom:
                '<"card-header d-flex flex-wrap py-0 flex-column flex-sm-row"' +
                "<f>" +
                '<"d-flex justify-content-center justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row align-items-baseline"lB>>' +
                ">t" +
                '<"row mx-1"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                ">",
            lengthMenu: [7, 10, 20, 50, 70, 100],
            language: {
                sLengthMenu: "_MENU_",
                search: "",
                searchPlaceholder: "Search Questions",
                paginate: {
                    next: '<i class="ti ti-chevron-right ti-sm"></i>',
                    previous: '<i class="ti ti-chevron-left ti-sm"></i>',
                },
            },
            buttons: [
                {
                    text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add Question</span>',
                    className: "add-new btn btn-primary ms-2 waves-effect waves-light",
                    action: function () {
                        window.location.href = "/hra/add-questions";
                    },
                },
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of Question: " + data["question"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== ""
                                ? '<tr data-dt-row="' +
                                col.rowIndex +
                                '" data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                "<td> " +
                                col.title +
                                ":" +
                                "</td> " +
                                '<td class="ps-0">' +
                                col.data +
                                "</td>" +
                                "</tr>"
                                : "";
                        }).join("");
                        return data
                            ? $('<table class="table"/><tbody />').append(data)
                            : false;
                    },
                },
            },
            initComplete: function () {
                setTimeout(function () {
                    $(".preloader-container").fadeOut("slow");
                    dt_questions_list_table.fadeIn("slow");
                }, 300);
            },
            searchCols: [{ search: "" }, null, null, null],
        });
    }
    var selectedQuestionId = 0;
    $(document).on("click", ".edit-question", function () {
        selectedQuestionId = $(this).data("question-id");
        const questionId = $(this).data("question-id");
        if (!questionId) {
            return;
        }
        const questionData = getAllQuestionsResponse.data.find(
            (q) => q.question_id === questionId
        );
        if (!questionData) {
            return;
        }
        document.querySelector("#Question_text").value = questionData.question;
        document.querySelector("#Question_text_old").value = questionData.question;
        document.querySelector("#Question_formula").value =
            questionData.formula || "";
        document.querySelector("#Dashboard_title").value =
            questionData.dashboard_title || "";

        // Clear all gender checkboxes first
        document.querySelectorAll('input[name="hra_gender"]').forEach(input => {
            input.checked = false;
        });

        // Ensure gender is an array
        let selectedGenders = Array.isArray(questionData.gender)
            ? questionData.gender
            : JSON.parse(questionData.gender || "[]");

        selectedGenders.forEach((gender) => {
            let genderInput = document.querySelector(`input[name="hra_gender"][value="${gender}"]`);
            if (genderInput) {
                genderInput.checked = true;
            }
        });

        var selectedValue = questionData.types;
        if (selectedValue) {
            document.getElementById("option_type").value = selectedValue;
        } else {
            document.getElementById("option_type").value = "";
        }
        document.querySelector("#hra_input_box").checked =
            questionData.input_box === 1;
        const imagePreview = document.getElementById("imagePreview");
        const imageText = document.getElementById("image-text");
        const noImageText = document.getElementById("no-image-text");
        if (questionData.image) {
            imagePreview.src = `/getContent/images/hra/question/${questionData.image}`;
            imagePreview.style.display = "block";
            imageText.style.display = "block";
            noImageText.style.display = "none";
        } else {
            imagePreview.style.display = "none";
            imageText.style.display = "none";
            noImageText.style.display = "block";
        }
        const answerContainer = document.querySelector("#answer-points-container");
        answerContainer.innerHTML = "";
        const labelsRow = document.createElement("div");
        labelsRow.className = "row mb-3 font-weight-bold";
        labelsRow.innerHTML = `
            <div class="col-5">Answer</div>
            <div class="col-3">Points</div>
            <div class="col-2">Compare Values</div>
            <div class="col-2"></div>`;
        answerContainer.appendChild(labelsRow);
        const answers = JSON.parse(questionData.answer || "{}");
        const points = JSON.parse(questionData.points || "{}");
        const compareValues = JSON.parse(questionData.comp_value || "{}");
        Object.entries(answers).forEach(([key, answer], index) => {
            const point = Object.values(points)[index] || "";
            const compareValue = Object.values(compareValues)[index] || "";
            const newRow = document.createElement("div");
            newRow.className = "row mb-3 align-items-center";
            newRow.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control" placeholder="Type Answers Here" name="hra_answers[]" value="${answer}">
                </div>
                <div class="col-3">
                    <input type="text" class="form-control" placeholder="Type Points Here" name="hra_points[]" value="${point}">
                </div>
                <div class="col-2">
                    <input type="text" class="form-control" placeholder="Type Compare Values Here" name="compare_values[]" value="${compareValue}">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger w-100 remove-row">
                        <i class="fas fa-minus fa-md"></i>
                    </button>
                </div>`;
            answerContainer.appendChild(newRow);
        });
        const addRowButton = document.createElement("div");
        addRowButton.className = "row mb-3 align-items-center";
        addRowButton.innerHTML = `
            <div class="col-1">
                <button type="button" class="btn btn-success w-100" id="add-answer-row">
                    <i class="fas fa-plus fa-md"></i>
                </button>
            </div>`;
        answerContainer.appendChild(addRowButton);
        const hasCompValue = compareValues && Object.keys(compareValues).length > 0;
        document.querySelector("#hra_compare_value").checked = hasCompValue;
        const compareValueInputs = document.querySelectorAll(
            'input[name="compare_values[]"]'
        );
        compareValueInputs.forEach((input) => (input.disabled = !hasCompValue));
        document.querySelector("#hra_comments").value = questionData.comments || "";
        const testSelect = document.querySelector(".master_tests");
        if (!testSelect) {
            return;
        }
        testSelect.innerHTML = "";
        try {
            const testIdsObject = JSON.parse(questionData.test_id || "{}");
            const selectedTestIds = Object.values(testIdsObject).map((id) =>
                id.toString()
            );
            for (const [testId, testName] of Object.entries(masterTestResponse)) {
                const option = document.createElement("option");
                option.value = testId;
                option.textContent = testName;
                option.selected = selectedTestIds.includes(testId);
                testSelect.appendChild(option);
            }
            if ($.fn.select2) {
                $(".master_tests").select2({
                    dropdownParent: $("#offcanvasEcommerceCategoryList"),
                    width: "100%",
                    multiple: true,
                    placeholder: "Select Tests",
                });
            }
        } catch (error) {
            console.error("Error handling test selection:", error);
        }
        const offcanvas = new bootstrap.Offcanvas(
            document.querySelector("#offcanvasEcommerceCategoryList")
        );
        offcanvas.show();
    });

    // Function to show loading animation
    function showLoadingAnimation() {
        const loadingHtml = `
            <div id="modifyLoadingOverlay" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            ">
                <div style="
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    text-align: center;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                ">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">Modifying Question...</p>
                </div>
            </div>
        `;
        $("body").append(loadingHtml);
    }

    // Function to hide loading animation
    function hideLoadingAnimation() {
        $("#modifyLoadingOverlay").remove();
    }

    $(document).on("click", "#save-modifications", function (event) {
        event.preventDefault();
        let isValid = true;
        const showError = (input, message) => {
            if (!$(input).next("small.text-danger").length) {
                $(input).after(`<small class="text-danger">${message}</small>`);
            }
            $(input).addClass("is-invalid");
        };
        const clearErrors = () => {
            $(".text-danger").remove();
            $(".is-invalid").removeClass("is-invalid");
        };
        const removeErrorOnInput = (input) => {
            $(input).on("input change", function () {
                $(this).removeClass("is-invalid");
                $(this).next("small.text-danger").remove();
            });
        };
        clearErrors();
        if (!$("#Question_text").val().trim()) {
            showError("#Question_text", "Question is required.");
            removeErrorOnInput("#Question_text");
            isValid = false;
        }
        if (!$("#Question_text_old").val().trim()) {
            showError("#Question_text_old", "Existing Question is required.");
            removeErrorOnInput("#Question_text_old");
            isValid = false;
        }
        let hasValidAnswer = false;
        $('input[name="hra_answers[]"]').each(function () {
            if ($(this).val().trim()) {
                hasValidAnswer = true;
            }
            removeErrorOnInput(this);
        });
        if (!hasValidAnswer) {
            showError(
                $('input[name="hra_answers[]"]').first(),
                "At least one answer is required."
            );
            isValid = false;
        }

        // Fixed gender validation - check for at least one checked gender
        if (!$('input[name="hra_gender"]:checked').length) {
            showError(
                $('input[name="hra_gender"]').first().parent(),
                "Gender selection is required."
            );
            $('input[name="hra_gender"]').on("change", function () {
                $(this).parent().find(".text-danger").remove();
            });
            isValid = false;
        }

        if (!$("#masterTests").val()) {
            showError("#masterTests", "Please select at least one test.");
            $("#masterTests").on("change", function () {
                $(this).removeClass("is-invalid");
                $(this).next("small.text-danger").remove();
            });
            isValid = false;
        }
        if ($("#hra_compare_value").is(":checked")) {
            let compareValues = Array.from(
                document.querySelectorAll('input[name="compare_values[]"]')
            ).map((input) => input.value);
            let answers = Array.from(
                document.querySelectorAll('input[name="hra_answers[]"]')
            ).map((input) => input.value);
            let points = Array.from(
                document.querySelectorAll('input[name="hra_points[]"]')
            ).map((input) => input.value);
            if (compareValues.length === 0) {
                showError(
                    'input[name="compare_values[]"]',
                    "Compare values are required when Compare Values is enabled."
                );
                isValid = false;
            }
            if (
                compareValues.length !== answers.length ||
                compareValues.length !== points.length
            ) {
                showError(
                    'input[name="compare_values[]"]',
                    "The number of compare values must match the number of answers and points."
                );
                isValid = false;
            }
        }
        if (!$("#option_type").val()) {
            const dropdownParent = $("#option_type").parent();
            if (!dropdownParent.find("small.text-danger").length) {
                dropdownParent.append(
                    '<small class="text-danger">Please select an option type.</small>'
                );
            }
            $("#option_type").on("change", function () {
                $(this).removeClass("is-invalid");
                dropdownParent.find("small.text-danger").remove();
            });
            $("#option_type").addClass("is-invalid");
            isValid = false;
        }
        if (!isValid) {
            return;
        }

        // Show loading animation
        showLoadingAnimation();

        const formData = new FormData();
        formData.append("question", $("#Question_text").val());
        formData.append("question_old", $("#Question_text_old").val());
        formData.append("formula", $("#Question_formula").val());
        formData.append("dashboard_text", $("#Dashboard_title").val());
        
        // FIXED: Append each selected gender as separate array items
        $('input[name="hra_gender"]:checked').each(function() {
            formData.append("gender[]", $(this).val());
        });

        formData.append("input_box", $("#hra_input_box").is(":checked"));
        formData.append(
            "is_compare_values",
            $("#hra_compare_value").is(":checked")
        );
        formData.append("comments", $("#hra_comments").val());
        formData.append("option_type", $("#option_type").val());
        const file = $("#hra_question_image")[0]?.files[0] || null;
        formData.append("hra_question_image", file);
        const answers = [];
        const points = [];
        const compareValues = [];
        const rows = Array.from(document.querySelectorAll('#answer-points-container .row')).slice(1, -1);
        rows.forEach(row => {
            const answerValue = row.querySelector('input[name="hra_answers[]"]').value.trim();
            const pointValue = row.querySelector('input[name="hra_points[]"]').value.trim();
            const compareValue = row.querySelector('input[name="compare_values[]"]').value.trim();
            answers.push(answerValue || null);
            points.push(pointValue || null);
            compareValues.push(compareValue || null);
        });
        answers.forEach((answer) => formData.append("answers[]", answer || ""));
        points.forEach((point) => formData.append("points[]", point || ""));
        compareValues.forEach((compareValue) => formData.append("compare_values[]", compareValue || ""));
        $("#masterTests")
            .find("option:selected")
            .each(function () {
                const value = $(this).val();
                if (value) formData.append("tests[]", value);
            });
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: `/hra/edit-question/${selectedQuestionId}`,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                // Hide loading animation
                hideLoadingAnimation();
                
                if (response.result) {
                    showToast("success", response.message || "Operation was successful!");
                    Swal.fire({
                        icon: "success",
                        title: "Question Updated Successfully!",
                        text: response.message,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    showToast("error", response.message || "An error occurred!");
                    Swal.fire({
                        icon: "error",
                        title: "Failed to update question, try again.",
                        text: response.message,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-danger",
                        },
                    });
                }
            },
            error: function (xhr, status, error) {
                // Hide loading animation
                hideLoadingAnimation();
                
                const errorMessage =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "An error occurred!";
                showToast("error", errorMessage);
                Swal.fire({
                    icon: "error",
                    title: "Failed to update question, try again.",
                    text: errorMessage,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            },
        });
    });
    $(document).on("change", "#hra_compare_value", function () {
        const isChecked = $(this).is(":checked");
        $('input[name="compare_values[]"]').each(function () {
            $(this).prop("disabled", !isChecked);
        });
    });
    $(document).on("click", "#add-answer-row", function () {
        const isChecked = $("#hra_compare_value").is(":checked");
        const newRow = document.createElement("div");
        newRow.className = "row mb-3 align-items-center";
        newRow.innerHTML = `
      <div class="col-5">
          <input type="text" class="form-control" placeholder="Type Answers Here" name="hra_answers[]" value="">
      </div>
      <div class="col-3">
          <input type="text" class="form-control" placeholder="Type Points Here" name="hra_points[]" value="">
      </div>
      <div class="col-2">
          <input type="text" class="form-control" placeholder="Type Compare Values Here" name="compare_values[]" value="" ${!isChecked ? "disabled" : ""}>
      </div>
      <div class="col-1">
          <button type="button" class="btn btn-danger w-100 remove-row">
              <i class="fas fa-minus fa-md"></i>
          </button>
      </div>`;
        document.querySelector("#answer-points-container").appendChild(newRow);
    });
    $(document).on("click", ".remove-row", function () {
        $(this).closest(".row").remove();
    });
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $(document).on("click", ".delete-question", function () {
        var questionId = $(this).data("question-id");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await apiRequest({
                        url: "/hra/delete-question/" + questionId,
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        onSuccess: (response) => {
                            showToast("success", response.message);
                            var row = dt_questions_list_table
                                .DataTable()
                                .row($(`[data-question-id="${questionId}"]`).closest("tr"));
                            row.remove().draw();
                        },
                        onError: (error) => {
                            showToast("error", error);
                        },
                    });
                } catch (error) {
                    showToast("error", "An error occurred while deleting the question.");
                }
            } else {
                Swal.fire("Cancelled", "The question was not deleted.", "info");
            }
        });
    });
});
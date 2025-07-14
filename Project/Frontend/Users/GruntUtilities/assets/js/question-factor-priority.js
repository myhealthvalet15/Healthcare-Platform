document.addEventListener("DOMContentLoaded", function () {
  const factorDropdown = document.querySelector("#factors");
  const existingFactorsContainer = document.querySelector(
    "#existing-questions-container"
  );
  const questionsDropdown = document.querySelector("#questionsDropdown");
  const noQuestions = document.getElementById("no-questions-container");
  const saveChangesButton = document.getElementById("save-changes");
  saveChangesButton.addEventListener("click", async function () {
    const factorId = factorDropdown.value;
    const priority = getQuestionPriority();

    const data = {
      template_id: templateId,
      factor_id: factorId,
      priority: priority,
    };
    await apiRequest({
      url: "/hra/templates/set-question-factor-priority/",
      method: "PUT",
      data: data,
      onSuccess: (responseData) => {
        if (responseData.result === "success") {
          showToast("success", "Changes saved successfully!");
          // location.reload();
        } else {
          showToast("error", "Failed to save changes.");
        }
      },
      onError: (error) => {
        showToast("error", `Error: ${error}`);
      },
    });
  });
  // Function to collect priority data
  function getQuestionPriority() {
    const listItems = document.querySelectorAll(
      "#existing-questions-container .list-group-item"
    );
    return Array.from(listItems).map((item) => item.getAttribute("data-id"));
  }
  // Fetch and display questions
  async function fetchQuestions() {
    await apiRequest({
      url: "https://mhv-admin.hygeiaes.com/hra/get-all-questions",
      method: "GET",
      onSuccess: (data) => {
        document.getElementById("preloader").style.display = "none";
        document.getElementById("contents-container").style.display = "block";
        if (data.data && Array.isArray(data.data)) {
          showToast("success", "Questions fetched successfully");
          questionsDropdown.innerHTML =
            '<option value="" disabled selected>Select a question</option>';
          data.data.forEach((question) => {
            const option = document.createElement("option");
            option.value = question.question;
            // Add the gender information to the question text
            option.textContent = `${question.question} (${question.gender})`; // Assuming 'gender' is a property in the API response
            option.setAttribute("data-id", question.question_id);
            questionsDropdown.appendChild(option);
          });

          hideExistingQuestionsInDropdown();
        } else {
          showToast("error", "Unexpected response format: " + data);
        }
      },
      onError: (error) => {
        showToast("error", "error: " + error);
        document.getElementById("preloader").style.display = "none";
        document.getElementById("contents-container").style.display = "block";
      },
    });
  }
  // Create the Sortable instance ONCE, outside of the createQuestionListItem function
  let existingQuestionsSortable;
  function initializeSortable() {
    const existingQuestionsContainer = document.getElementById("existing-questions-container");

    // Destroy existing Sortable instance if it exists
    if (existingQuestionsSortable) {
      existingQuestionsSortable.destroy();
    }

    // Create new Sortable instance
    existingQuestionsSortable = new Sortable(existingQuestionsContainer, {
      animation: 150,
      ghostClass: "sortable-ghost",
      // Optional: Handle drag end to update priorities
      onEnd: function (evt) {
        updatePriorities();
      }
    });
  }
  function createQuestionListItem(question, priority) {
    // Create the list item element
    const listItem = document.createElement("li");
    listItem.classList.add(
      "list-group-item",
      "drag-item",
      "cursor-move",
      "d-flex",
      "justify-content-between",
      "align-items-center"
    );
    listItem.setAttribute("data-id", question.question_id);

    // Create the priority badge
    const priorityBadge = document.createElement("span");
    priorityBadge.textContent = priority;
    priorityBadge.classList.add("badge", "bg-primary", "priority-badge");

    // Create the question name span
    const questionName = document.createElement("span");
    questionName.textContent = question.question_name;
    questionName.classList.add("flex-grow-1");

    // Create the remove button
    const removeButton = document.createElement("button");
    removeButton.classList.add("btn", "btn-sm", "btn-danger", "ms-3");
    removeButton.textContent = "Remove";

    // Add event listener for removing the item
    removeButton.addEventListener("click", () => {
      listItem.remove(); // Remove the item from the DOM
      updatePriorities(); // Update priorities after removal
      showToast("success", `Question "${question.question_name}" removed`);
    });

    // Append child elements to the list item
    listItem.appendChild(priorityBadge);
    listItem.appendChild(questionName);
    listItem.appendChild(removeButton);

    return listItem; // Return the constructed list item
  }
  async function fetchQuestionPriorities(templateId, factorId) {
    await apiRequest({
      url: `https://mhv-admin.hygeiaes.com/hra/templates/${templateId}/factor-priority/${factorId}/get-question-factor-priority`,
      method: "GET",
      onSuccess: (data) => {
        existingFactorsContainer.innerHTML = ""; // Clear existing content
        showToast("success", "Question priority fetched successfully");

        if (data.result === "success" &&
          data.message &&
          data.message.questions &&
          data.message.questions.length > 0) {

          // Create a fragment to improve performance
          const fragment = document.createDocumentFragment();

          data.message.questions.forEach((question, index) => {
            const listItem = createQuestionListItem(question, index + 1);
            fragment.appendChild(listItem);
          });

          // Append the fragment to the container
          existingFactorsContainer.appendChild(fragment);

          // Initialize or reinitialize Sortable after adding items
          initializeSortable();

          hideExistingQuestionsInDropdown();
        } else {
          noQuestions.textContent = "No questions available for this factor.";
        }
      },
      onError: (error) => {
        showToast("error", error);
        noQuestions.textContent = "Error fetching question priorities";
      }
    });
  }
  // Function to hide existing questions in the dropdown
  function hideExistingQuestionsInDropdown() {
    const existingQuestions =
      existingFactorsContainer.querySelectorAll(".list-group-item");
    existingQuestions.forEach(function (item) {
      const questionId = item.getAttribute("data-id");
      $(`#questionsDropdown option[data-id='${questionId}']`).remove();
    });
  }
  // Function to create text elements (used for creating spans with text)
  function createTextElement(tag, text, classes = []) {
    const element = document.createElement(tag);
    element.textContent = text;
    classes.forEach((className) => element.classList.add(className));
    return element;
  }

  // Update question priorities after adding/removing questions
  function updatePriorities() {
    const listItems = document.querySelectorAll(
      "#existing-questions-container .list-group-item"
    );
    listItems.forEach((item, index) => {
      const priorityBadge = item.querySelector(".priority-badge");
      if (priorityBadge) {
        priorityBadge.textContent = index + 1;
      }
    });
  }

  // Event listener for adding selected questions to the list
  $("#questionsDropdown").on("select2:select", function (e) {
    const selectedQuestion = e.params.data;
    const questionName = selectedQuestion.text;
    const questionId = selectedQuestion.element.dataset.id;
    const existingItems =
      existingFactorsContainer.querySelectorAll(".list-group-item");
    const nextPriority = existingItems.length + 1;
    const question = {
      question_id: questionId,
      question_name: questionName,
    };
    const listItem = createQuestionListItem(question, nextPriority);
    document.getElementById('no-questions-container').style.display = 'none';
    existingFactorsContainer.appendChild(listItem);
    const optionToRemove = $(
      `#questionsDropdown option[data-id='${questionId}']`
    );
    optionToRemove.remove();
    $("#questionsDropdown").val(null).trigger("change");
    $("#questionsDropdown").trigger("change");
    const remainingOptions = $("#questionsDropdown option").length;
    if (remainingOptions === 0) {
      $("#questionsDropdown").select2("placeholder", "All questions added");
    } else {
      $("#questionsDropdown").select2("placeholder", "Select a question");
    }
    updatePriorities();
    showToast("success", `Question "${questionName}" added to the list`);
  });

  // Initialize select2 dropdown
  $("#questionsDropdown").select2();

  // Event listener for factor selection change
  factorDropdown.addEventListener("change", function () {
    const factorId = this.value;
    if (templateId && factorId) {
      const newUrl = `https://mhv-admin.hygeiaes.com/hra/templates/${templateId}/factor-priority/${factorId}/question-priority`;
      window.location.href = newUrl;
    }
  });

  // Fetch question priorities for the selected factor on page load
  if (factorDropdown.value) {
    fetchQuestionPriorities(templateId, factorDropdown.value);
  }

  // Fetch questions on page load
  fetchQuestions();
  initializeSortable();

});

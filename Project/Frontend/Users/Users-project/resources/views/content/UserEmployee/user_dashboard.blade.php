@extends('layouts/layoutMaster')
@section('title', 'User Profile - Profile')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'
])
@endsection
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/pages-profile.js'])
@endsection
@section('content')
<div class="card">
  <div class="card-body">
    <h2>Welcome to Employee Login</h2>
    <div id="hra-templates-wrapper" class="mt-3">
      <h5>Assigned HRA Templates</h5>
      <div id="hra-templates">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 100px;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <span class="ms-2">Loading HRA Templates...</span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="eventModalLabel" style="color:#fff;margin-bottom:15px;">🎉 You're Cordially
          Invited</h5>
      </div>
      <div class="modal-body" id="eventModalBody">
        <p class="text-center text-muted">Fetching royal invitations...</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success" onclick="handleEventResponse('yes')">Yes, I will attend</button>
        <button type="button" class="btn btn-outline-secondary" onclick="handleEventResponse('no')">No, maybe
          later</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="factorScoresModal" tabindex="-1" aria-labelledby="factorScoresModalLabel" aria-hidden="true"
  data-bs-backdrop="true" data-bs-keyboard="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded">
      <div class="modal-header bg-primary text-white border-0 pb-4">
        <h5 class="modal-title" id="factorScoresModalLabel" style="color:#fff;">📊 Factor Scores Analysis</h5>
      </div>
      <div class="modal-body pt-4" id="factorScoresModalBody">
        <p class="text-center text-muted">Loading factor scores...</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
  function createSafeTextElement(tagName, text, className = '') {
    const element = document.createElement(tagName);
    element.textContent = text;
    if (className) {
      element.className = className;
    }
    return element;
  }
  function clearContainer(container) {
    while (container.firstChild) {
      container.removeChild(container.firstChild);
    }
  }
  function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
      case 'completed':
        return 'badge bg-success';
      case 'not attended':
        return 'badge bg-danger';
      case 'in progress':
      case 'attended':
        return 'badge bg-warning';
      default:
        return 'badge bg-warning';
    }
  }
  function showFactorScores(templateName, factorPoints) {
    const modalBody = document.getElementById('factorScoresModalBody');
    const modalTitle = document.getElementById('factorScoresModalLabel');
    modalTitle.textContent = `📊 ${templateName} - Factor Scores`;
    clearContainer(modalBody);
    if (factorPoints && Object.keys(factorPoints).length > 0) {
      const container = document.createElement('div');
      container.className = 'row g-3';
      Object.entries(factorPoints).forEach(([factorName, score]) => {
        const colDiv = document.createElement('div');
        colDiv.className = 'col-md-6 col-12';
        const factorCard = document.createElement('div');
        factorCard.className = 'card border-0 shadow-sm';
        factorCard.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)';
        const cardBody = document.createElement('div');
        cardBody.className = 'card-body text-center p-3';
        const factorTitle = createSafeTextElement('h6', factorName, 'mb-2 text-dark');
        const scoreValue = createSafeTextElement('h4', `${score}%`, 'mb-2 fw-bold');
        const numericScore = parseFloat(score);
        if (numericScore >= 80) {
          scoreValue.className += ' text-success';
        } else if (numericScore >= 60) {
          scoreValue.className += ' text-warning';
        } else {
          scoreValue.className += ' text-danger';
        }
        const progressContainer = document.createElement('div');
        progressContainer.className = 'progress mb-2';
        progressContainer.style.height = '8px';
        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar';
        progressBar.style.width = `${numericScore}%`;
        if (numericScore >= 80) {
          progressBar.className += ' bg-success';
        } else if (numericScore >= 60) {
          progressBar.className += ' bg-warning';
        } else {
          progressBar.className += ' bg-danger';
        }
        progressContainer.appendChild(progressBar);
        cardBody.appendChild(factorTitle);
        cardBody.appendChild(scoreValue);
        cardBody.appendChild(progressContainer);
        factorCard.appendChild(cardBody);
        colDiv.appendChild(factorCard);
        container.appendChild(colDiv);
      });
      modalBody.appendChild(container);
    } else {
      const noDataMsg = createSafeTextElement('p', 'No factor scores available for this template.', 'text-muted text-center');
      modalBody.appendChild(noDataMsg);
    }
    const factorModal = new bootstrap.Modal(document.getElementById('factorScoresModal'));
    factorModal.show();
  }
  function getAllAssignedHraTemplates() {
    apiRequest({
      url: 'https://login-users.hygeiaes.com/UserEmployee/dashboard/templates/getAllAssignedTemplates',
      method: 'GET',
      dataType: 'json',
      onSuccess: function (response) {
        const wrapper = document.getElementById('hra-templates-wrapper');
        const container = document.getElementById('hra-templates');
        clearContainer(container);
        if (response.result && Array.isArray(response.data) && response.data.length > 0) {
          const gridContainer = document.createElement('div');
          gridContainer.className = 'row g-3';
          response.data.forEach(template => {
            const colDiv = document.createElement('div');
            colDiv.className = 'col-lg-4 col-md-4 col-sm-12';
            const card = document.createElement('div');
            card.className = 'card h-100 shadow-sm border-0';
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body d-flex flex-column';
            const titleContainer = document.createElement('div');
            titleContainer.className = 'd-flex justify-content-between align-items-center mb-3';
            const templateName = createSafeTextElement('h6', template.template_name, 'card-title text-primary mb-0');
            titleContainer.appendChild(templateName);
            if (template.status.toLowerCase() === 'completed' && template.factor_points && Object.keys(template.factor_points).length > 0) {
              const factorIcon = document.createElement('button');
              factorIcon.className = 'btn btn-sm btn-outline-info rounded-circle p-2';
              factorIcon.style.width = '35px';
              factorIcon.style.height = '35px';
              factorIcon.title = 'View Factor Scores';
              const iconElement = document.createElement('i');
              iconElement.className = 'fas fa-chart-pie';
              factorIcon.appendChild(iconElement);
              factorIcon.onclick = function () {
                showFactorScores(template.template_name, template.factor_points);
              };
              titleContainer.appendChild(factorIcon);
            }
            const statusScoreContainer = document.createElement('div');
            statusScoreContainer.className = 'd-flex justify-content-between align-items-center mb-3';
            const statusBadge = createSafeTextElement('span', template.status, getStatusBadgeClass(template.status));
            const scoreContainer = document.createElement('div');
            scoreContainer.className = 'text-end';
            if (template.score && template.score.trim() !== '') {
              const scoreLabel = createSafeTextElement('small', 'Score: ', 'text-muted');
              const scoreValue = createSafeTextElement('strong', template.score + '%', 'text-success');
              scoreContainer.appendChild(scoreLabel);
              scoreContainer.appendChild(scoreValue);
            } else {
              const noScore = createSafeTextElement('small', 'No score', 'text-muted');
              scoreContainer.appendChild(noScore);
            }
            statusScoreContainer.appendChild(statusBadge);
            statusScoreContainer.appendChild(scoreContainer);
            const actionContainer = document.createElement('div');
            actionContainer.className = 'mt-auto';
            const actionButton = document.createElement('a');
            actionButton.href = `https://login-users.hygeiaes.com/UserEmployee/dashboard/templates/hra-questionaire/template/${encodeURIComponent(template.template_id)}`;
            actionButton.target = '_blank';
            actionButton.rel = 'noopener noreferrer';
            if (template.status.toLowerCase() === 'completed') {
              actionButton.className = 'btn btn-primary btn-sm w-100 disabled';
              actionButton.textContent = 'Assessment Completed';
              actionButton.style.pointerEvents = 'none';
              actionButton.removeAttribute('href');
            } else {
              actionButton.className = 'btn btn-primary btn-sm w-100';
              const buttonText = template.status.toLowerCase() === 'not attended'
                ? 'Start Assessment'
                : 'Continue Assessment';
              actionButton.textContent = buttonText;
            }
            actionContainer.appendChild(actionButton);
            cardBody.appendChild(titleContainer);
            cardBody.appendChild(statusScoreContainer);
            cardBody.appendChild(actionContainer);
            card.appendChild(cardBody);
            colDiv.appendChild(card);
            gridContainer.appendChild(colDiv);
          });
          container.appendChild(gridContainer);
        } else {
          wrapper.querySelector('h5').style.display = 'none';
          const emptyState = document.createElement('div');
          emptyState.className = 'text-center py-5';
          const emptyIcon = document.createElement('div');
          emptyIcon.className = 'mb-3';
          const iconElement = document.createElement('i');
          iconElement.className = 'fas fa-clipboard-list fa-3x text-muted';
          emptyIcon.appendChild(iconElement);
          const emptyTitle = createSafeTextElement('h6', 'No HRA Templates Assigned', 'text-muted mb-2');
          const emptyDesc = createSafeTextElement('p', 'You currently have no Health Risk Assessment templates assigned to you.', 'text-muted small');
          emptyState.appendChild(emptyIcon);
          emptyState.appendChild(emptyTitle);
          emptyState.appendChild(emptyDesc);
          container.appendChild(emptyState);
        }
      },
      onError: function () {
        showToast('error', 'Failed to load HRA templates');
        const container = document.getElementById('hra-templates');
        clearContainer(container);
        const errorState = document.createElement('div');
        errorState.className = 'text-center py-5';
        const errorIcon = document.createElement('div');
        errorIcon.className = 'mb-3';
        const iconElement = document.createElement('i');
        iconElement.className = 'fas fa-exclamation-triangle fa-3x text-danger';
        errorIcon.appendChild(iconElement);
        const errorTitle = createSafeTextElement('h6', 'Error Loading Templates', 'text-danger mb-2');
        const errorDesc = createSafeTextElement('p', 'Unable to load HRA templates. Please try again later.', 'text-muted small');
        const retryButton = document.createElement('button');
        retryButton.className = 'btn btn-outline-primary btn-sm mt-2';
        retryButton.textContent = 'Retry';
        retryButton.onclick = getAllAssignedHraTemplates;
        errorState.appendChild(errorIcon);
        errorState.appendChild(errorTitle);
        errorState.appendChild(errorDesc);
        errorState.appendChild(retryButton);
        container.appendChild(errorState);
      }
    });
  }
  function handleEventResponse(choice) {
    if (choice === 'yes') {
      showToast('success', "Great! You've accepted the invitation.");
    } else {
      showToast('info', 'Maybe next time. Invitation dismissed.');
    }
    const modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
    modal.hide();
  }
  function getEventDetails() {
    apiRequest({
      url: 'https://login-users.hygeiaes.com/UserEmployee/events/getEventDetails',
      method: 'GET',
      dataType: 'json',
      onSuccess: function (response) {
        const modalBody = document.getElementById('eventModalBody');
        if (response.result && Array.isArray(response.data) && response.data.length > 0) {
          const sortedEvents = response.data.sort((a, b) => b.event_id - a.event_id);
          const event = sortedEvents[0];
          const from = new Date(event.from_datetime).toLocaleString();
          const to = new Date(event.to_datetime).toLocaleString();
          clearContainer(modalBody);
          const eventBlock = document.createElement('div');
          eventBlock.classList.add('mb-4', 'pb-3', 'border-bottom');
          const headerDiv = document.createElement('div');
          headerDiv.classList.add('d-flex', 'justify-content-between', 'align-items-start');
          const eventTitle = createSafeTextElement('h5', event.event_name, 'fw-bold mb-0');
          const dateDiv = document.createElement('div');
          dateDiv.classList.add('text-end', 'ms-3');
          const dateInfo = document.createElement('p');
          dateInfo.classList.add('mb-1', 'text-muted', 'small');
          const calendarIcon = document.createElement('i');
          calendarIcon.className = 'fa fa-calendar';
          const fromStrong = createSafeTextElement('strong', 'From: ');
          const fromText = document.createTextNode(from);
          const br1 = document.createElement('br');
          const toStrong = createSafeTextElement('strong', 'To: ');
          const toText = document.createTextNode(to);
          dateInfo.appendChild(calendarIcon);
          dateInfo.appendChild(document.createTextNode(' '));
          dateInfo.appendChild(fromStrong);
          dateInfo.appendChild(fromText);
          dateInfo.appendChild(br1);
          dateInfo.appendChild(toStrong);
          dateInfo.appendChild(toText);
          dateDiv.appendChild(dateInfo);
          headerDiv.appendChild(eventTitle);
          headerDiv.appendChild(dateDiv);
          const guestPara = document.createElement('p');
          guestPara.classList.add('mt-2');
          const userIcon = document.createElement('i');
          userIcon.className = 'fa fa-user';
          const guestStrong = createSafeTextElement('strong', 'Guest: ');
          const guestText = document.createTextNode(event.guest_name || 'TBA');
          guestPara.appendChild(userIcon);
          guestPara.appendChild(document.createTextNode(' '));
          guestPara.appendChild(guestStrong);
          guestPara.appendChild(guestText);
          const descPara = document.createElement('p');
          const descStrong = createSafeTextElement('strong', 'Description:');
          const descBr = document.createElement('br');
          const descText = document.createTextNode(event.event_description || 'No description available.');
          descPara.appendChild(descStrong);
          descPara.appendChild(descBr);
          descPara.appendChild(descText);
          const testsPara = document.createElement('p');
          const testsStrong = createSafeTextElement('strong', 'Tests Linked: ');
          const testsText = document.createTextNode(
            event.test_names && typeof event.test_names === 'object'
              ? Object.values(event.test_names).join(', ')
              : 'No tests linked.'
          );
          testsPara.appendChild(testsStrong);
          testsPara.appendChild(testsText);
          eventBlock.appendChild(headerDiv);
          eventBlock.appendChild(guestPara);
          eventBlock.appendChild(descPara);
          eventBlock.appendChild(testsPara);
          modalBody.appendChild(eventBlock);
          const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
          eventModal.show();
        } else {
          clearContainer(modalBody);
          const noEventsMsg = createSafeTextElement('p', 'No royal invitations await at the moment.', 'text-muted text-center');
          modalBody.appendChild(noEventsMsg);
        }
      },
      onError: function () {
        const modalBody = document.getElementById('eventModalBody');
        clearContainer(modalBody);
        const errorMsg = createSafeTextElement('p', 'Failed to fetch the event scrolls.', 'text-danger text-center');
        modalBody.appendChild(errorMsg);
      }
    });
  }
  $(document).ready(function () {
    getAllAssignedHraTemplates();
    getEventDetails();
  });
</script>
@endsection
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

<div class="row">
  <div class="col-xl-8 col-lg-7 col-md-7">
    <div class="card mb-6">
      <div class="card-body">
        <h2>Welcome to Employee Login</h2>
        <div id="hra-templates-wrapper" class="mt-3">
          <h5>HRA Templates</h5>
          <div id="hra-templates">
            <p>Loading HRA Templates...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 🎉 Modal: Event Invitation Popup -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="eventModalLabel" style="color:#fff;margin-bottom:15px;">🎉 You're Cordially Invited</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="eventModalBody">
        <p class="text-center text-muted">Fetching royal invitations...</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success" onclick="handleEventResponse('yes')">Yes, I will attend</button>
        <button type="button" class="btn btn-outline-secondary" onclick="handleEventResponse('no')">No, maybe later</button>
      </div>
    </div>
  </div>
</div>

<script>
  // ✨ Loads HRA templates
  function getAllAssignedHraTemplates() {
    apiRequest({
      url: 'https://login-users.hygeiaes.com/UserEmployee/dashboard/templates/getAllAssignedTemplates',
      method: 'GET',
      dataType: 'json',
      onSuccess: function (response) {
        const wrapper = document.getElementById('hra-templates-wrapper');
        const container = document.getElementById('hra-templates');
        container.innerHTML = '';

        if (response.result && Array.isArray(response.data) && response.data.length > 0) {
          const list = document.createElement('ul');
          list.classList.add('list-group');
          response.data.forEach(template => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item';
            const link = document.createElement('a');
            link.href = `https://login-users.hygeiaes.com/UserEmployee/dashboard/templates/hra-questionaire/template/${template.template_id}`;
            link.textContent = template.template_name;
            link.target = '_blank';
            listItem.appendChild(link);
            list.appendChild(listItem);
          });
          container.appendChild(list);
        } else {
          wrapper.querySelector('h5').style.display = 'none';
          container.innerHTML = '<p class="text-muted">No HRA templates assigned.</p>';
        }
      },
      onError: function () {
        showToast('error', 'Failed to load HRA templates');
        document.getElementById('hra-templates').innerHTML = '<p class="text-danger">Error loading templates.</p>';
      }
    });
  }

  // 🌟 Handle Event Response (accept/decline)
  function handleEventResponse(choice) {
    if (choice === 'yes') {
      showToast('success', 'Great! You’ve accepted the invitation.');
    } else {
      showToast('info', 'Maybe next time. Invitation dismissed.');
    }
    const modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
    modal.hide();
  }

  // 👑 Load and display event invitations
 function getEventDetails() {
  apiRequest({
    url: 'https://login-users.hygeiaes.com/UserEmployee/dashboard/events/getEventDetails',
    method: 'GET',
    dataType: 'json',
    onSuccess: function (response) {
      const modalBody = document.getElementById('eventModalBody');

      if (response.result && Array.isArray(response.data) && response.data.length > 0) {
        // ✅ Sort by event_id DESC
        const sortedEvents = response.data.sort((a, b) => b.event_id - a.event_id);

        // ✅ Get the latest event
        const event = sortedEvents[0];

        const from = new Date(event.from_datetime).toLocaleString();
        const to = new Date(event.to_datetime).toLocaleString();

        const eventBlock = document.createElement('div');
        eventBlock.classList.add('mb-4', 'pb-3', 'border-bottom');

        eventBlock.innerHTML = `
          <div class="d-flex justify-content-between align-items-start">
            <h5 class="fw-bold mb-0">${event.event_name}</h5>
            <div class="text-end ms-3">
              <p class="mb-1 text-muted small">
                <i class="fa fa-calendar"></i> <strong>From:</strong> ${from}<br>
                <strong>To:</strong> ${to}
              </p>
            </div>
          </div>
          <p class="mt-2"><i class="fa fa-user"></i> <strong>Guest:</strong> ${event.guest_name || 'TBA'}</p>
          <p><strong>Description:</strong><br>${event.event_description || 'No description available.'}</p>
          <p><strong>Tests Linked:</strong> ${
            event.test_names
              ? Object.values(event.test_names).join(', ')
              : 'No tests linked.'
          }</p>
        `;

        modalBody.innerHTML = '';
        modalBody.appendChild(eventBlock);

        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        eventModal.show();
      } else {
        modalBody.innerHTML = '<p class="text-muted text-center">No royal invitations await at the moment.</p>';
      }
    },
    onError: function () {
      const modalBody = document.getElementById('eventModalBody');
      modalBody.innerHTML = '<p class="text-danger text-center">Failed to fetch the event scrolls.</p>';
    }
  });
}

  // 🚀 Init on page load
  $(document).ready(function () {
    getAllAssignedHraTemplates();
    getEventDetails(); // Show fantasy modal popup
  });
</script>
@endsection

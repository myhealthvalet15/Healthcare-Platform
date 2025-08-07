function handleEventResponse(choice, eventName, eventId) {
  const status = choice === 'yes' ? 'yes' : 'no';
  apiRequest({
    url: submitResponseUrl,
    method: 'POST',
    data: {
      event_id: eventId,
      user_id: userId,
      response: status
    },
    onSuccess: function (data) {
      if (data.result) {
        showToast('success', `Your response to "${eventName}" has been recorded.`);
      } else {
        showToast('warning', `Failed to save your response.`);
      }
    },
    onError: function () {
      showToast('danger', `Error sending response for "${eventName}".`);
    }
  });
}
function getEventDetails() {
  apiRequest({
    url: 'https://login-users.hygeiaes.com/UserEmployee/events/getEventDetails',
    method: 'GET',
    onSuccess: function (response) {
      const eventList = document.getElementById('eventList');
      if (response.result && Array.isArray(response.data) && response.data.length > 0) {
        eventList.innerHTML = '';
        response.data.forEach((event, index) => {
          const from = new Date(event.from_datetime).toLocaleString();
          const to = new Date(event.to_datetime).toLocaleString();
          const tests = event.test_names
            ? Object.values(event.test_names).join(', ')
            : 'No tests linked.';
          let responseButtons = '';
          if (event.response_status === 'yes') {
            responseButtons = `
                            <div class="mt-3 d-flex align-items-center gap-2">
                                <span class="text-success fw-semibold">✅ Already accepted</span>
                                <button class="btn btn-outline-secondary btn-sm"
                                    onclick="handleEventResponse('no', '${event.event_name}', ${event.event_id})">
                                    Cancel / No, maybe later
                                </button>
                            </div>`;
          } else if (event.response_status === 'no') {
            responseButtons = `
                            <div class="mt-3 d-flex align-items-center gap-2">
                                <span class="text-danger fw-semibold">Declined</span>
                                <button class="btn btn-success btn-sm"
                                    onclick="handleEventResponse('yes', '${event.event_name}', ${event.event_id})">
                                    Yes, I will attend
                                </button>
                            </div>`;
          } else {
            responseButtons = `
                            <div class="mt-3">
                                <button class="btn btn-success btn-sm me-2"
                                    onclick="handleEventResponse('yes', '${event.event_name}', ${event.event_id})">
                                    Yes, I will attend
                                </button>
                                <button class="btn btn-outline-secondary btn-sm"
                                    onclick="handleEventResponse('no', '${event.event_name}', ${event.event_id})">
                                    No, maybe later
                                </button>
                            </div>`;
          }
          const block = document.createElement('div');
          block.className = `card mb-4 shadow-sm event-card ${index % 2 === 0 ? 'even' : 'odd'}`;
          block.innerHTML = `
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold mb-0">${event.event_name}</h5>
                                <small class="text-muted text-end">
                                    <i class="fa fa-calendar"></i> <strong>From:</strong> ${from}<br>
                                    <strong>To:</strong> ${to}
                                </small>
                            </div>
                            <p><i class="fa fa-user"></i> <strong>Guest:</strong> ${event.guest_name || 'TBA'}</p>
                            <p><strong>Description:</strong><br>${event.event_description || 'No description available.'}</p>
                            <p><strong>Tests Linked:</strong> ${tests}</p>
                            ${responseButtons}
                        </div>
                    `;
          eventList.appendChild(block);
        });
      } else {
        eventList.innerHTML = '<p class="text-muted">No royal invitations await at the moment.</p>';
      }
    },
    onError: function () {
      document.getElementById('eventList').innerHTML =
        '<p class="text-danger">Failed to fetch the event scrolls.</p>';
    }
  });
}
document.addEventListener('DOMContentLoaded', getEventDetails);

// 🌟 Show toast message
function showToast(type, message) {
  const toast = document.createElement('div');
  toast.className = `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
  toast.style.zIndex = 1080;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;
  document.body.appendChild(toast);
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();
  setTimeout(() => toast.remove(), 5000);
}

// 🌟 Handle Event Response
function handleEventResponse(choice, eventName) {
  const msg = choice === 'yes'
    ? `✅ You've accepted "${eventName}".`
    : `❌ You declined "${eventName}".`;
  showToast(choice === 'yes' ? 'success' : 'info', msg);
}

// 🧠 Fetch & display event data
function getEventDetails() {
  fetch('https://login-users.hygeiaes.com/UserEmployee/dashboard/events/getEventDetails')
    .then(res => res.json())
    .then(response => {
      const eventList = document.getElementById('eventList');
      if (response.result && Array.isArray(response.data) && response.data.length > 0) {
        eventList.innerHTML = '';

        response.data.forEach(event => {
          const from = new Date(event.from_datetime).toLocaleString();
          const to = new Date(event.to_datetime).toLocaleString();
          const tests = event.test_names
            ? Object.values(event.test_names).join(', ')
            : 'No tests linked.';

          const block = document.createElement('div');
          block.className = 'card mb-4 shadow-sm';
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
              <div class="mt-3">
                <button class="btn btn-success btn-sm me-2" onclick="handleEventResponse('yes', '${event.event_name}')">Yes, I will attend</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="handleEventResponse('no', '${event.event_name}')">No, maybe later</button>
              </div>
            </div>
          `;
          eventList.appendChild(block);
        });
      } else {
        eventList.innerHTML = '<p class="text-muted">No royal invitations await at the moment.</p>';
      }
    })
    .catch(() => {
      document.getElementById('eventList').innerHTML =
        '<p class="text-danger">⚠️ Failed to fetch the event scrolls.</p>';
    });
}

// 🚀 Initialize on page load
document.addEventListener('DOMContentLoaded', getEventDetails);

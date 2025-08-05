<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>You're Invited</title>
 
  <script>
    const eventId = '{{ $eventId }}';
    const userId = '{{ $userId }}';
  </script>
</head>

<body style="background: #f7f7f9; padding: 2rem;">
  <div id="eventContainer" class="container"></div>

  <script>
    function handleEventResponse(choice, eventName, eventId) {
      fetch('https://login-users.hygeiaes.com/UserEmployee/events/submitResponse', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          event_id: eventId,
          user_id: userId,
          response: choice
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.result) {
            alert(`✅ Your response "${choice}" for "${eventName}" has been recorded.`);
            loadEvent(); // Reload to reflect changes
          } else {
            alert('⚠️ Failed to record your response.');
          }
        })
        .catch(() => {
          alert('❌ Error sending your response.');
        });
    }

    function loadEvent() {
      fetch(`https://login-users.hygeiaes.com/UserEmployee/events/getEventDetails?event_id=${eventId}&user_id=${userId}`)
        .then(res => res.json())
        .then(response => {
          const container = document.getElementById('eventContainer');
          container.innerHTML = '';

          if (response.result && response.data && response.data.length > 0) {
            const event = response.data[0];
            const from = new Date(event.from_datetime).toLocaleString();
            const to = new Date(event.to_datetime).toLocaleString();
            const tests = event.test_names
              ? Object.values(event.test_names).join(', ')
              : 'No tests linked.';

            let responseButtons = '';
            if (event.response_status === 'yes') {
              responseButtons = `
                <div class="mt-3 d-flex gap-2 align-items-center">
                  <span class="text-success fw-semibold">✅ Already accepted</span>
                  <button class="btn btn-outline-secondary btn-sm" onclick="handleEventResponse('no', '${event.event_name}', ${event.event_id})">Cancel / No, maybe later</button>
                </div>`;
            } else if (event.response_status === 'no') {
              responseButtons = `
                <div class="mt-3 d-flex gap-2 align-items-center">
                  <span class="text-danger fw-semibold">❌ Declined</span>
                  <button class="btn btn-success btn-sm" onclick="handleEventResponse('yes', '${event.event_name}', ${event.event_id})">Yes, I will attend</button>
                </div>`;
            } else {
              responseButtons = `
                <div class="mt-3">
                  <button class="btn btn-success btn-sm me-2" onclick="handleEventResponse('yes', '${event.event_name}', ${event.event_id})">Yes, I will attend</button>
                  <button class="btn btn-outline-secondary btn-sm" onclick="handleEventResponse('no', '${event.event_name}', ${event.event_id})">No, maybe later</button>
                </div>`;
            }

            const block = document.createElement('div');
            block.className = 'card shadow-sm mb-4';
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

            container.appendChild(block);
          } else {
            container.innerHTML = '<p class="text-muted text-center">No invitation found for this event or user.</p>';
          }
        })
        .catch(() => {
          document.getElementById('eventContainer').innerHTML =
            '<p class="text-danger text-center">⚠️ Could not fetch invitation details.</p>';
        });
    }

    // Init
    window.onload = loadEvent;
  </script>
</body>

</html>
 function loadEvents() {
        $('#healthPlanTableBody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        apiRequest({
            url: 'https://login-users.hygeiaes.com/UserEmployee/getEventsforEmployees',
            method: 'GET',
            dataType: 'json',         
            onSuccess: function (response) {  
                console.log(response);            
                if (response.result && Array.isArray(response.data)) {
                    allHealthPlansData = response.data;
                    filteredData = [...allHealthPlansData];
                    populateEventTable(filteredData);
                } else {
                    showToast('info', 'Notice', response.message || 'No events found.');
                    $('#eventTableBody').html('<tr><td colspan="5" class="text-center text-muted">No events found</td></tr>');
                }
            },
            onError: function (error) {
                showToast('error', 'Error', 'Failed to load Events');
                $('#eventTableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }
 $(document).ready(function () {
        loadEvents();              

});
function populateEventTable(events) {
    const tableBody = $('#eventTableBody');
    tableBody.empty();

    if (!events || events.length === 0) {
        tableBody.html('<tr><td colspan="6" class="text-center text-muted">No events found for this employee.</td></tr>');
        return;
    }

    events.forEach(event => {
        const eventName = event.event_name || '-';
        const guestName = event.guest_name || '-';
        const fromDate = formatDateToDDMMYYYY(event.from_datetime);
        const toDate = formatDateToDDMMYYYY(event.to_datetime);
        const status = event.response_status ? capitalizeWords(event.response_status) : 'Not Responded';
        const statusClass = event.response_status === 'yes' 
            ? 'badge bg-success' 
            : event.response_status === 'no' 
                ? 'badge bg-danger' 
                : 'badge bg-secondary';

        const testNames = event.test_names 
            ? Object.values(event.test_names).join(', ') 
            : 'No Tests Assigned';

        const row = `
            <tr>
                <td>
                    <div class="fw-medium">${eventName}</div>
                </td>
                <td>
                    <div>${guestName}</div>
                </td>
                <td>
                    <div>${fromDate}</div>
                </td>
                <td>
                    <div>${toDate}</div>
                </td>
                <td>
                    <span class="${statusClass}">${status}</span>
                </td>
                <td>
                    <div class="text-wrap" style="max-width: 250px;">${testNames}</div>
                </td>
            </tr>
        `;

        tableBody.append(row);
    });
}

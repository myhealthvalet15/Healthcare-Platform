document.addEventListener('DOMContentLoaded', function () {
    console.log('hello');
    fetchincidentTypes();
});
function fetchincidentTypes() {
    console.log("checking the cards");

    const container = document.getElementById('incidentCardContainer');
    const preloader = document.getElementById('preloader'); // ← Add this line

    container.innerHTML = '';
    preloader.style.display = 'block';

    apiRequest({
        url: "/corporate/getAllIncidentTypes",
        method: 'GET',
        onSuccess: (response) => {
            preloader.style.display = 'none';

            const incidents = response.data;

            if (!Array.isArray(incidents) || incidents.length === 0) {
                const noDataMessage = document.createElement('div');
                noDataMessage.className = 'col-12 text-center';
                noDataMessage.innerHTML = `<div class="alert alert-info">No incidents available.</div>`;
                container.appendChild(noDataMessage);
                return;
            }

            incidents.forEach(incident => {
                const cardWrapper = document.createElement('div');
                cardWrapper.className = 'col-md-4 mb-3'; // responsive grid

                const card = document.createElement('div');
                card.className = 'card p-3 shadow-sm h-100';

                // Header with Checkbox
                const header = document.createElement('div');
                header.className = 'd-flex align-items-center justify-content-between';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.classList.add('form-check-input');
                checkbox.value = incident.incident_type_id;

                const title = document.createElement('h5');
                title.className = 'mb-0 ms-2';
                title.textContent = incident.incident_type_name;

                const headerGroup = document.createElement('div');
                headerGroup.className = 'd-flex align-items-center';
                headerGroup.appendChild(checkbox);
                headerGroup.appendChild(title);

                header.appendChild(headerGroup);

                // Actions
                const actions = document.createElement('div');
                actions.className = 'mt-3';

                const editIcon = document.createElement('i');
                editIcon.classList.add('ti', 'ti-pencil', 'me-3', 'cursor-pointer');
                editIcon.setAttribute('title', 'Edit');
                editIcon.addEventListener('click', () => {
                    editincident(incident.incident_type_name, incident.incident_type_id);
                });

                const deleteIcon = document.createElement('i');
                deleteIcon.classList.add('ti', 'ti-trash', 'cursor-pointer');
                deleteIcon.setAttribute('title', 'Delete');
                deleteIcon.addEventListener('click', () => {
                    deleteincident(incident.incident_type_id);
                });

                actions.appendChild(editIcon);
                actions.appendChild(deleteIcon);

                card.appendChild(header);
                card.appendChild(actions);

                cardWrapper.appendChild(card);
                container.appendChild(cardWrapper);
            });
        },
        onError: (error) => {
            preloader.innerHTML = `<span>Error fetching data. <br>Status: ${error}.</span>`;
        }
    });
}

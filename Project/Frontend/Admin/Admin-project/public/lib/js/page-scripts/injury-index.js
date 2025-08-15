$(document).ready(function () {
    loadTabContent(1);
    $('button[data-bs-toggle="tab"]').on('click', function () {
        var injuryKey = $(this).data('injury-key');
        loadTabContent(injuryKey);
    });
    function loadTabContent(injuryKey, page = 1) {
        var tabContent = $('#tab-content-' + injuryKey);
        tabContent.html(`
                <div class="loader-container">
                    <div class="custom-loader">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <p>Loading, please wait...</p>
                </div>
            `);
        $.ajax({
            url: '/outpatient/injury',
            type: 'GET',
            data: {
                injury_key: injuryKey,
                page: page
            },
            success: function (response) {
                tabContent.html(response.data);
                tabContent.find('.pagination a').on('click', function (e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    var newPage = new URLSearchParams(url.split('?')[1]).get('page');
                    loadTabContent(injuryKey, newPage);
                });
            },
            error: function (xhr) {
                console.error("Error loading content:", xhr.responseText);
                tabContent.html('<p>Error loading content. Please try again.</p>');
            }
        });
    }
});

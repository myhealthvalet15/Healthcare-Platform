document.addEventListener('DOMContentLoaded', function () {
    function loadExcelContent() {
        const preloader = document.getElementById('preloader');
        const excelTableContainer = document.getElementById('excelTableContainer');
        setTimeout(function () {
            preloader.style.display = 'none';
            excelTableContainer.style.display = 'block';
            addDataBtn.classList.remove('d-none');
            revokeBtn.classList.remove('d-none');
            excelTableContainer.addEventListener('scroll', function (event) {
                const scrollLeft = event.target.scrollLeft;
                const scrollWidth = event.target.scrollWidth;
                const clientWidth = event.target.clientWidth;
                if (scrollLeft + clientWidth >= scrollWidth) {
                    showToast('info', 'You have reached the end of the table.');
                }
            });
        }, 5000);
    }
    function sendAjaxRequest(form, method, url) {
        const formData = new FormData(form);
        return fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
    }
    document.getElementById('addDataForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const url = new URL(window.location.href);
        const pathSegments = url.pathname.split('/');
        const corporateid = pathSegments[3];
        const locationid = pathSegments[4];
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to add the data from the Excel file.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Add!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('preloader').style.display = 'block';
                const formData = new FormData(this);
                formData.append('corporateid', corporateid);
                formData.append('locationid', locationid);
                fetch(this.dataset.route, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.result) {
                            toastr.success('Action completed successfully!');
                            document.getElementById('preloader').style.display = 'none';
                            window.open('https://login-users.hygeiaes.com/employees/corporate/add-corporate-users/', '_blank');
                            window.close();
                        } else {
                            document.getElementById('preloader').style.display = 'none';
                            toastr.error('Something went wrong!');
                        }
                    })
                    .catch(error => {
                        document.getElementById('preloader').style.display = 'none';
                        // console.error('Error:', error);
                        toastr.error('An error occurred. Check the console for more details.');
                    });
            }
        });
    });
    document.getElementById('revokeDataForm').addEventListener('submit', function (event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to revoke the data from the Excel file.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Revoke!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('preloader').style.display = 'block';
                sendAjaxRequest(this, 'POST', this.dataset.route)
                    .then(response => response.json())
                    .then(data => {
                        if (data.result) {
                            toastr.success('Data revoked successfully!');
                            document.getElementById('preloader').style.display = 'none';
                            setTimeout(function () {
                                window.open('https://login-users.hygeiaes.com/employees/corporate/add-corporate-users/', '_blank');
                                window.close();
                            }, 2000);
                        } else {
                            document.getElementById('preloader').style.display = 'none';
                            toastr.error('Failed to revoke data!');
                        }
                    })
                    .catch(error => {
                        document.getElementById('preloader').style.display = 'none';
                        toastr.error('An error occurred. Check the console for more details.');
                    });
            }
        });
    });
    loadExcelContent();
});

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
                        toastr.error('An error occurred. Check the console for more details.');
                    });
            }
        });
    });
    document.getElementById('revokeDataForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const route = form.dataset.route;
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
                apiRequest({
                    url: route,
                    method: 'POST',
                    data: formData,
                    onSuccess: function (data) {
                        document.getElementById('preloader').style.display = 'none';
                        if (data.result) {
                            toastr.success('Data revoked successfully!');
                            setTimeout(function () {
                                window.open('https://login-users.hygeiaes.com/employees/corporate/add-corporate-users/', '_blank');
                                window.close();
                            }, 2000);
                        } else {
                            toastr.error('Failed to revoke data!');
                        }
                    },
                    onError: function (error) {
                        document.getElementById('preloader').style.display = 'none';
                        toastr.error('An error occurred. Check the console for more details.');
                        console.error(error);
                    }
                });
            }
        });
    });
    loadExcelContent();
});

@extends('layouts/layoutMaster')
@section('title', 'Diagnostic Assesment')
@section('page-script')
<style>
   
    #employeeSummary i {
        color: #0d6efd;
        margin-right: 6px;
    }

    #employeeSummary .text-end p {
        text-align: right;
    }

    .card-body {
        padding: 1.5rem;
        background: linear-gradient(to right, #fdfdff, #f2f6fc);
        border-radius: 12px;
    }

    .datatables-basic td {
        vertical-align: top !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const employeeId = @json(session('employee_id'));
    const employeeDetailsUrl = @json(route('employee-user-details'));
</script>
<script src="{{ asset('Bhava/JS/employee-details.js') }}?v={{ time() }}"></script>
<script src="{{ asset('Bhava/JS/events.js') }}"></script>



@endsection
@section('content')
<div class="card shadow-sm border-0 mb-4" id="employeeSummaryCard">
    <div class="card-body bg-light rounded" id="employeeSummary">
        <div class="row align-items-start justify-content-between">           
            <div class="col-md-6">              
                <p><i class="fa-solid fa-user"></i> <span id="empName"></span> - <span id="empId"></span></p>
                <p><i class="fa-solid fa-hourglass-half"></i> <span id="empAge"></span> / <span id="empGender"></span>
                </p>
            </div>
            <div class="col-md-6 text-end">
                <p><i class="fa-solid fa-sitemap"></i> <span id="empDepartment"></span> - <span
                        id="empDesignation"></span></p>
                <p><i class="fa-solid fa-user-tag"></i> <span id="empType"></span> / <span id="empdateOfJoining"></span>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Events List</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Event Date</th>
                    <th>Event Name</th>
                    <th>Guest Name</th>
                    <th>Test Taken</th> 
                    <th>Status</th>

                </tr>
            </thead>
            <tbody id="eventTableBody">
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>



@endsection
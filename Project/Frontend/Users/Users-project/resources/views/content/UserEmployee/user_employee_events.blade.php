@extends('layouts/layoutMaster')
@section('title', 'Diagnostic Assessment')
@section('page-script')
    <link rel="stylesheet" href="/lib/css/page-styles/user_employee_events.css">
    <script>
        const employeeId = @json(session('employee_id'));
        const employeeDetailsUrl = @json(route('employee-user-details'));
        const userId = @json(session('master_user_user_id'));
        const submitResponseUrl = @json(route('submitResponse'));
    </script>
@endsection
@section('content')
    <div class="py-5" style="background-color: #ffffff;">
        <div class="container">
            <div class="card mb-4 shadow-sm"
                style="background-color: #fff9e6; border: 1px solid #f3e5c3; border-radius: 12px;">
                <div class="card-body py-3">
                    <h3 class="mb-0 text-center">🎉 Upcoming Event Invitations</h3>
                </div>
            </div>
            <div id="eventList">
                <p class="text-muted">Loading royal invitations...</p>
            </div>

        </div>
    </div>
    <script src="/lib/js/page-scripts/events.js"></script>
@endsection

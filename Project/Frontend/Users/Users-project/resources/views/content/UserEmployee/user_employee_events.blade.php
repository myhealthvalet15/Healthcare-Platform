@extends('layouts/layoutMaster')
@section('title', 'Diagnostic Assessment')

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

<!-- Bootstrap Icons and JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const employeeId = @json(session('employee_id'));
    const employeeDetailsUrl = @json(route('employee-user-details'));
</script>
<script src="{{ asset('Bhava/JS/events.js') }}?v={{ time() }}"></script>
@endsection
<style>
  .card-body {
    background: linear-gradient(to right, #fdfdff, #f2f6fc);
    border-radius: 12px;
  }

  .card .btn {
    font-weight: 500;
  }

  .toast {
    min-width: 280px;
  }
</style>

@section('content')
<div class="container my-4">
  <h3 class="mb-4">🎉 Upcoming Event Invitations</h3>
  <div id="eventList">
    <p class="text-muted">Loading royal invitations...</p>
  </div>
</div>
@endsection


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
    const userId = @json(session('master_user_user_id'));
    const submitResponseUrl = @json(route('submitResponse')); 
</script>


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
  }#eventList .card {
    background-color: #f2f6fc/* Light cream */
    border: 1px solid #f3e5c3;
    border-radius: 12px;
  }

  #eventList .card-body {
    background: transparent;
    padding: 1.5rem;
  }

  #eventList h5 {
    color: #333;
  }

  #eventList .btn {
    font-weight: 500;
  }

  body {
    background-color: #f8f9fa; /* optional: light page background outside content section */
  }
  .event-card.even {
    background-color: #f9fdfe; /* Light cream */
    border: 1px solid #f9fdfe;
    border-radius: 12px;
  }

  .event-card.odd {
    background-color: #eaf6ff; /* Light blue tint */
    border: 1px solid #c9e6f3;
    border-radius: 12px;
  }

  .event-card .card-body {
    background: transparent;
    padding: 1.5rem;
  }

  .event-card h5 {
    color: #333;
  }

  .event-card .btn {
    font-weight: 500;
  }
</style>

@section('content')
<div class="py-5" style="background-color: #ffffff;">
  <div class="container">

    <!-- Heading Card -->
    <div class="card mb-4 shadow-sm" style="background-color: #fff9e6; border: 1px solid #f3e5c3; border-radius: 12px;">
      <div class="card-body py-3">
        <h3 class="mb-0 text-center">🎉 Upcoming Event Invitations</h3>
      </div>
    </div>

    <!-- Event Cards Will Load Here -->
    <div id="eventList">
      <p class="text-muted">Loading royal invitations...</p>
    </div>

  </div>
</div><meta name="csrf-token" content="{{ csrf_token() }}">


@endsection



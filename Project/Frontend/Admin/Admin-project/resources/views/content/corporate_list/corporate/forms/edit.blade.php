@extends('layouts/layoutMaster')
@section('title', 'Assign Forms')
@section('description', 'Manage Employee Types and Active Status')
@section('content')
<div class="container">
    <h2>Assign Forms</h2>
   <form id="assignFormsForm" method="POST">
    @csrf
     <input type="hidden" name="corporate_id" value="{{ $corporate_id }}">
    <input type="hidden" name="location_id" value="{{ $location_id }}">
    <div id="form-sections">
    </div>
    <button type="submit" class="btn btn-primary mt-3">Submit</button>
</form>
</div>
<script>
$(document).ready(function() {
    const corporateId = '{{ $corporate_id }}';
    const locationId = '{{ $location_id }}';
});
</script>
<script src="/lib/js/page-scripts/edit-forms.js"></script>
@endsection
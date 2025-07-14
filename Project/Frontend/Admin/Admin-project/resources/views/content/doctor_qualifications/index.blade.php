@extends('layouts/layoutMaster')

@section('title', 'Doctor Qualifications')
@section('description', 'Manage doctor qualifications and specializations.')

@section('content')
<div class="container mt-5">
  
    @if (session('success'))
    hello
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif




@endsection
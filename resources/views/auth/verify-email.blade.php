@extends('layout.layout2') <!-- Extend layout2.blade.php -->

@section('title', 'Email Verification Required') <!-- Set page title -->

@section('content')
<div class="text-center mb-3">
    <a href="#!">
        <img src="{{ asset('images/logo.jpg') }}" alt="Your Logo" class="img-fluid rounded-circle" style="max-width: 150px; height: auto;">
    </a>
</div>

<h2 class="fs-6 fw-normal text-center text-secondary mb-4">Email Verification Required</h2>

<p class="text-center text-muted mb-4">
    Thank you for signing up! Please check your email to complete the verification process.
</p>

@if (session('message'))
    <div class="alert alert-success text-center">
        {{ session('message') }}
    </div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <div class="d-grid my-3">
        <button type="submit" class="btn btn-primary btn-lg">{{ __('Resend Verification Email') }}</button>
    </div>
</form>
@endsection
l
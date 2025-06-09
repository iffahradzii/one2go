@extends('layout.layout2') <!-- Extend layout2.blade.php -->

@section('title', 'Forgot Password') <!-- Set page title -->

@section('content')
<div class="text-center mb-3">
    <a href="#!">
        <img src="{{ asset('images/logo.jpg') }}" alt="Your Logo" class="img-fluid rounded-circle" style="max-width: 150px; height: auto;">
    </a>
</div>

<h2 class="fs-6 fw-normal text-center text-secondary mb-4">Forgot Password</h2>

<!-- Display Success Message after sending reset link -->
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="row gy-2 overflow-hidden">
        <div class="col-12">
            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="name@example.com" required>
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12">
            <div class="d-grid my-3">
                <button class="btn btn-primary btn-lg" type="submit">{{ __('Send Password Reset Link') }}</button>
            </div>
        </div>

        <div class="col-12">
            <p class="m-0 text-secondary text-center">
                Remembered your password? <a href="{{ route('login') }}" class="link-primary text-decoration-none">Sign in</a>
            </p>
        </div>
    </div>
</form>
@endsection

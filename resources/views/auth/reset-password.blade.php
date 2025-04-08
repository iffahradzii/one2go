@extends('layout.layout2') <!-- Extend layout2.blade.php -->

@section('title', 'Reset Password') <!-- Set page title -->

@section('content')
<div class="text-center mb-3">
    <a href="#!">
        <img src="{{ asset('images/logo.jpg') }}" alt="Your Logo" class="img-fluid rounded-circle" style="max-width: 150px; height: auto;">
    </a>
</div>

<h2 class="fs-6 fw-normal text-center text-secondary mb-4">Reset Password</h2>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="row gy-2 overflow-hidden">
        <div class="col-12">
            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" required>
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12">
            <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required>
                <label for="password" class="form-label">{{ __('New Password') }}</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12">
            <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" required>
                <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12">
            <div class="d-grid my-3">
                <button class="btn btn-primary btn-lg" type="submit">{{ __('Reset Password') }}</button>
            </div>
        </div>
    </div>
</form>
@endsection

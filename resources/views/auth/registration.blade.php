@extends('layout.layout2') <!-- Extend layout2.blade.php -->

@section('title', 'Registration') <!-- Set page title -->

@section('content')
<div class="text-center mb-3">
<h2 class="fs-6 fw-normal text-center text-secondary mb-4">  _</h2>
<h2 class="fs-6 fw-normal text-center text-secondary mb-4">_</h2>

  <a href="#!">
    <img src="{{ asset('images/logo.jpg') }}" alt="Your Logo" class="img-fluid rounded-circle" style="max-width: 150px; height: auto;">
  </a>
</div>


<h2 class="fs-6 fw-normal text-center text-secondary mb-4">Sign up to your account</h2>

<form method="POST" action="{{ route('register.post') }}">
  @csrf

  @if (session('error'))
    <div class="alert alert-danger" role="alert">
      {{ session('error') }}
    </div>
  @endif

  <div class="row gy-2 overflow-hidden">
    <div class="col-12">
      <div class="form-floating mb-3">
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="Your Name" required>
        <label for="name" class="form-label">{{ __('Name') }}</label>
        @error('name')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
      </div>
    </div>

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
    <div class="form-floating mb-3">
      <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" placeholder="Your Phone Number" required>
      <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
      @error('phone')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
    </div>
  </div>

    <div class="col-12">
      <div class="form-floating mb-3">
        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Password" required>
        <label for="password" class="form-label">{{ __('Password') }}</label>
        @error('password')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
      </div>
    </div>

    <div class="col-12">
      <div class="form-floating mb-3">
        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required>
        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
        @error('password_confirmation')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
      </div>
    </div>

    <div class="col-12">
      <div class="d-grid my-3">
        <button class="btn btn-primary btn-lg" type="submit">{{ __('Register') }}</button>
      </div>
    </div>

    <div class="col-12">
      <p class="m-0 text-secondary text-center">
        Have an account? <a href="{{ route('login') }}" class="link-primary text-decoration-none">Sign in</a>
      </p>
    </div>
  </div>
</form>
@endsection

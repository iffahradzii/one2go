@extends('layout.layout2') <!-- Extend the layout -->

@section('title', 'Login') <!-- Page-specific title -->

@section('content')
<div class="text-center mb-3">
  <a href="{{ url('/') }}">
    <img src="{{ asset('images/logo.jpg') }}" alt="Your Logo" class="logo">
  </a>
</div>
<h2 class="fs-6 fw-normal text-center text-secondary mb-4">Sign in to your account</h2>

<form method="POST" action="{{ route('login.post') }}">
  @csrf

  @if (session('error'))
    <div class="alert alert-danger" role="alert">
      {{ session('error') }}
    </div>
  @endif

  <div class="row gy-2 overflow-hidden">
    <div class="col-12">
      <div class="form-floating mb-3">
        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="name@example.com" required>
        <label for="email" class="form-label">Email Address</label>
        @error('email')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
      </div>
    </div>
    <div class="col-12">
      <div class="form-floating mb-3">
        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Password" required>
        <label for="password" class="form-label">Password</label>
        @error('password')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
      </div>
    </div>
    <div class="col-12">
      <div class="d-flex gap-2 justify-content-between">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember">
          <label class="form-check-label" for="remember">Keep me logged in</label>
        </div>
        <a href="{{ route('password.request') }}" class="link-primary text-decoration-none">Forgot password?</a>
      </div>
    </div>
    <div class="col-12">
      <div class="d-grid my-3">
        <button class="btn btn-login" type="submit">Login</button>
      </div>
    </div>
    <div class="col-12 text-center">
      <p class="m-0 text-secondary">Don't have an account? <a href="{{ route('register') }}" class="link-primary text-decoration-none">Sign up</a></p>
    </div>
  </div>
</form>
@endsection

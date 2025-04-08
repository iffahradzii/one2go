@extends('layout.layout2') <!-- Extend the layout -->

@section('title', 'Admin Login') <!-- Page-specific title -->

@section('content')
<div class="text-center mb-3">
  <a href="{{ url('/') }}">
    <img src="{{ asset('images/logo.jpg') }}" alt="Your Logo" class="logo">
  </a>
</div>
<h2 class="fs-6 fw-normal text-center text-secondary mb-4">Admin Login</h2>

<form method="POST" action="{{ route('admin.login') }}">
  @csrf

  @if (session('error'))
    <div class="alert alert-danger" role="alert">
      {{ session('error') }}
    </div>
  @endif

  <div class="row gy-2 overflow-hidden">
    <div class="col-12">
      <div class="form-floating mb-3">
        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" placeholder="Admin Username" required>
        <label for="username" class="form-label">Username</label>
        @error('username')
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
    
    </div>
    <div class="col-12">
      <div class="d-grid my-3">
        <button class="btn btn-login btn-primary" type="submit">Login</button>
      </div>
    </div>
    
  </div>
</form>
@endsection

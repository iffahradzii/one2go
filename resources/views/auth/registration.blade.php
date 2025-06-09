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
        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" 
               placeholder="name@example.com" required 
               pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
               title="Please enter a valid email address in the format: name@example.com">
        <label for="email" class="form-label">{{ __('Email Address') }}</label>
        <div class="invalid-feedback" id="emailFeedback">
          @error('email')
            {{ $message }}
          @else
            Please enter a valid email address in the format: name@example.com
          @enderror
        </div>
      </div>
    </div>

    <div class="col-12">
    <div class="form-floating mb-3">
      <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" 
             placeholder="Your Phone Number" required 
             pattern="[0-9]{10}" 
             title="Phone number must be exactly 10 digits and contain only numbers" 
             maxlength="10">
      <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
      <div class="invalid-feedback" id="phoneFeedback">
        @error('phone')
          {{ $message }}
        @else
          Phone number must be exactly 10 digits and contain only numbers
        @enderror
      </div>
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

<script>
  // Email validation
  const emailInput = document.getElementById('email');
  const emailFeedback = document.getElementById('emailFeedback');
  
  emailInput.addEventListener('input', function() {
    const isValid = this.checkValidity();
    if (!isValid) {
      this.classList.add('is-invalid');
    } else {
      this.classList.remove('is-invalid');
    }
  });
  
  // Phone validation
  const phoneInput = document.getElementById('phone');
  const phoneFeedback = document.getElementById('phoneFeedback');
  
  phoneInput.addEventListener('input', function() {
    // Remove any non-numeric characters
    this.value = this.value.replace(/[^0-9]/g, '');
    
    const isValid = this.value.length === 10;
    if (!isValid) {
      this.classList.add('is-invalid');
    } else {
      this.classList.remove('is-invalid');
    }
  });
</script>
@endsection

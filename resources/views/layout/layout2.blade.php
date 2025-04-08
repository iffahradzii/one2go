<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Page')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>
    body {
      background: url('{{ asset('images/bg3.png') }}') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      background: #ffffff;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      max-width: 700px;
      width: 100%;
      display: flex;
      height: 90vh; /* Fixed height for container */
      overflow: hidden;
    }

    .form-section {
      flex: 1;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100%; /* Ensures full use of container height */
      overflow-y: auto; /* Enables vertical scrolling */
    }

    .promo-section {
      flex: 1;
      position: relative;
    }

    .promo-section img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .form-floating input {
      border-radius: 0.5rem;
    }

    .btn-login {
      background: #0047AB;
      color: #ffffff;
      border-radius: 0.5rem;
      font-size: 1rem;
      padding: 0.75rem;
    }

    .btn-social {
      border-radius: 0.5rem;
    }

    .logo {
      max-width: 150px;
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- Left: Form Section -->
    <div class="form-section">
      @yield('content')
    </div>

    <!-- Right: Promo Section -->
    <div class="promo-section">
      <img src="{{ asset('images/bg2.png') }}" alt="Promo Image">
    </div>
  </div>
</body>
</html>

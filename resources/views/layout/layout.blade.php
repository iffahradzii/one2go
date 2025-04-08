<!DOCTYPE html>
<html>
<head>
    <title>@yield("title")</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Add Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);
  
        body {
            margin: 0;
            font-size: .9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #212529;
            text-align: left;
            background-color: #f5f8fa;
        }
        .navbar-laravel {
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
        }
        .navbar-brand, .nav-link, .my-form, .login-form {
            font-family: Raleway, sans-serif;
        }
        .my-form {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .my-form .row {
            margin-left: 0;
            margin-right: 0;
        }
        .login-form {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .login-form .row {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
</head>
<body>
    
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <!-- Left Section: Logo -->
            <div class="nav-left">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Your Logo" class="logo" width="80">
                </a>
            </div>

            <!-- Toggle Button for Mobile View -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Content -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Center Section: Navigation Links -->
                <ul class="navbar-nav mx-auto nav-links">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.home') }}">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="travelPackageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Travel Packages
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="travelPackageDropdown">
                            <li><a class="dropdown-item" href="{{ route('packages.thailand') }}">Thailand</a></li>
                            <li><a class="dropdown-item" href="{{ route('packages.vietnam') }}">Vietnam</a></li>
                            <li><a class="dropdown-item" href="{{ route('packages.indonesia') }}">Indonesia</a></li>
                            <li><a class="dropdown-item" href="{{ route('packages.southkorea') }}">South Korea</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/ratings-reviews') }}">Ratings & Review</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="othersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Others
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="othersDropdown">
                            <li><a class="dropdown-item" href="{{ route('about.us') }}">About Us</a></li>
                            <li><a class="dropdown-item" href="{{ url('/faq') }}">FAQ</a></li>
                        </ul>
                    </li>

                    @auth
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('my-booking') }}">My Booking</a>
                        </li>
                    @endauth
                </ul>

                <!-- Right Section: User Details and Auth Links -->
                <ul class="navbar-nav nav-right">
                    @guest
                        <!-- Login/Register links for guests -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item dropdown user-section">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle user-name" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
        @yield('scripts')
    </main>
</div>
<!-- Add jQuery first (required for Bootstrap Datepicker) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<!-- Add Bootstrap Datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

@stack('scripts')
</body>
</html>

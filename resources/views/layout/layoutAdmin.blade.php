<!DOCTYPE html>
<html>
<head>
    <title>@yield("title")</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: #fff;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            width: 16.6667%; /* Matches the 2-column width of Bootstrap grid */
            top: 0;
            left: 0;

        }
        .col-md-10 {
            margin-left: 16.6667%; /* Adjust based on the sidebar width percentage */
            padding: 1rem; /* Optional padding for better content spacing */
         }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 1rem 0;
        }
        .sidebar a:hover {
            background-color: #495057;
            padding-left: 10px;
            transition: all 0.3s;
        }
        .sidebar-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .sidebar-header img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/logo.jpg') }}" alt="Agency Logo">
                <h5>Admin Dashboard</h5>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}">Home</a>
                <a href="{{ route('admin.travel-package.index') }}">Travel Package</a>
                <a href="{{ route('admin.booking.index') }}">Booking List</a>
                <a href="#">Payment</a>
                <a href="{{ route('admin.customers.index') }}">Customer</a> <!-- Updated Link -->
                <a href="#">Inquiries</a>
                <a href="#">Ratings</a>
                <a href="#">Generate Report</a>
            </div>
            <div class="mt-auto">
            <a href="{{ route('admin.login') }}" class="btn btn-danger w-100">Logout</a>
            </div>
        </div>

        <!-- Main Content Placeholder -->
        <div class="col-md-10">
            @yield('content')
            @yield('scripts')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>

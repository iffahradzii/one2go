<!DOCTYPE html>
<html>
<head>
    <title>@yield("title")</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        .sidebar {
            height: 100vh;
            background-color: #000052; /* Dark blue background */
            color: #fff;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            width: 16.6667%;
            top: 0;
            left: 0;
            overflow-y: auto;
        }

        .col-md-10 {
            margin-left: 16.6667%;
            padding: 1.5rem;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding: 1rem 0;
        }

        .sidebar-header img {
            width: 100px;
            height: 100px;
            margin-bottom: 1rem;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.2);
            padding: 0.5rem;
            background-color: white;
        }

        .sidebar-header h5 {
            color: white;
            font-size: 1.2rem;
            margin-top: 1rem;
            font-weight: 600;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 1rem;
            margin: 0.3rem 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 1rem;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .dropdown-toggle {
            width: 100%;
            text-align: left;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin: 0.3rem 0;
        }

        .dropdown-toggle::after {
            float: right;
            margin-top: 8px;
        }

        .dropdown-menu {
            background-color: #000052;
            border: 1px solid rgba(255,255,255,0.1);
            margin-top: 0;
            border-radius: 8px;
            padding: 0.5rem;
            width: 100%;
        }

        .dropdown-item {
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 6px;
            margin: 0.2rem 0;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 0.8rem;
            border-radius: 8px;
            margin-top: auto;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #bb2d3b;
            transform: translateY(-2px);
        }

        .btn-danger i {
            margin-right: 8px;
        }

        /* Stats cards styling */
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table styling */
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: #000052;
            border-bottom: 2px solid #dee2e6;
        }

        /* Search button styling */
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        /* Search input styling */
        .form-control {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        }

        
        .uniform-btn {
            min-width: 140px;
            padding: 8px 16px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Home</a>
                <a href="{{ route('admin.travel-package.index') }}"><i class="fas fa-suitcase"></i> Travel Package</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-book"></i> Booking List</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.booking.index', ['type' => 'general']) }}">General Booking</a>
                        <a class="dropdown-item" href="{{ route('admin.private-booking.index') }}">Private Booking</a>
                    </div>
                </div>
                <a href="{{ route('admin.customer.index') }}"><i class="fas fa-users"></i> Customer</a>
                <a href="{{ route('admin.faqs.index') }}"><i class="fas fa-question-circle"></i> FAQs</a>
                <a href="{{ route('admin.reviews.index') }}"><i class="fas fa-star"></i> Reviews & Ratings</a>
            </div>
            <a href="{{ route('admin.login') }}" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main Content -->
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

@extends('layout.layoutAdmin')

@section('title', 'Booking Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Booking Management</h1>
        <div class="d-flex gap-3">
            <!-- Search Box -->
            <form class="d-flex" action="{{ route('admin.booking.index') }}" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search bookings..." value="{{ request('search') }}">
                <button class="btn btn-primary ms-2" type="submit">Search</button>
            </form>
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ request('status', 'All Status') }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.booking.index') }}">All Status</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.booking.index', ['status' => 'pending']) }}">Pending</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.booking.index', ['status' => 'paid']) }}">Paid</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.booking.index', ['status' => 'cancelled']) }}">Cancelled</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Bookings</h6>
                            <h2 class="mb-0">{{ $totalBookings }}</h2>
                        </div>
                        <i class="fas fa-book-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Pending Payments</h6>
                            <h2 class="mb-0">{{ $pendingBookings }}</h2>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Completed Bookings</h6>
                            <h2 class="mb-0">{{ $paidBookings }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Cancelled Bookings</h6>
                            <h2 class="mb-0">{{ $cancelledBookings }}</h2>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Package</th>
                            <th>Travel Date</th>
                            <th>Participants</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold">{{ $booking->customer_name }}</div>
                                    <small class="text-muted">{{ $booking->customer_email }}</small>
                                </td>
                                <td>{{ $booking->travelPackage->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</td>
                                <td>
                                    <div class="small">
                                        @php
                                            $adultCount = $booking->travelers->where('category', 'Adult')->count();
                                            $childCount = $booking->travelers->where('category', 'Child')->count();
                                            $infantCount = $booking->travelers->where('category', 'Infant')->count();
                                        @endphp
                                        <div>Adults: {{ $adultCount }}</div>
                                        <div>Children: {{ $childCount }}</div>
                                        <div>Infants: {{ $infantCount }}</div>
                                    </div>
                                </td>
                                <td>RM {{ number_format($booking->total_price, 2) }}</td>
                                <td>
                                    <span class="badge rounded-pill 
                                        @if($booking->payment_status == 'paid') bg-success
                                        @elseif($booking->payment_status == 'pending') bg-warning
                                        @else bg-danger @endif">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </td>
                                <td>{{ $booking->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.booking.show', $booking->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatus{{ $booking->id }}"
                                                title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.booking.destroy', $booking->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this booking?')"
                                                    title="Delete Booking">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No bookings found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} results
                </div>
                <div>
                    {{ $bookings->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($bookings as $booking)
    @include('admin.booking-list.partials.status-modal', ['booking' => $booking])
@endforeach
@endsection

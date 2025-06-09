@extends('layout.layout')

@section('title', 'My Bookings')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="text-center fw-bold">My Bookings</h1>
            <div class="text-center text-muted">Manage your travel bookings</div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <!-- Tabs Navigation -->
            <ul class="nav nav-pills nav-fill p-3 bg-light border-bottom" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active px-4" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="fas fa-clock me-2"></i>Pending 
                        <span class="badge rounded-pill bg-warning text-dark ms-2">
                            {{ $bookings->where('payment_status', 'pending')->where('available_date', '>=', now()->toDateString())->count() + 
                               (isset($privateBookings) ? $privateBookings->where('payment_status', 'pending')->where('available_date', '>=', now()->toDateString())->count() : 0) }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="booked-tab" data-bs-toggle="tab" data-bs-target="#booked" type="button" role="tab">
                        <i class="fas fa-check-circle me-2"></i>Booked 
                        <span class="badge rounded-pill bg-success ms-2">
                            {{ $bookings->where('payment_status', 'paid')->where('available_date', '>=', now()->toDateString())->count() + 
                               (isset($privateBookings) ? $privateBookings->where('payment_status', 'paid')->where('available_date', '>=', now()->toDateString())->count() : 0) }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="done-tab" data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab">
                        <i class="fas fa-flag-checkered me-2"></i>Done 
                        <span class="badge rounded-pill bg-secondary ms-2">
                            {{ $bookings->where('payment_status', 'paid')->where('available_date', '<', now()->toDateString())->count() + 
                               (isset($privateBookings) ? $privateBookings->where('payment_status', 'paid')->where('available_date', '<', now()->toDateString())->count() : 0) }}
                        </span>
                    </button>
                </li>
                <!-- In the tabs navigation -->
                <li class="nav-item">
                    <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                        <i class="fas fa-times-circle me-2"></i>Cancelled 
                        <span class="badge rounded-pill bg-danger ms-2">
                            {{ $bookings->where('payment_status', 'cancelled')->count() + 
                               (isset($privateBookings) ? $privateBookings->where('payment_status', 'cancelled')->count() : 0) }}
                        </span>
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content p-4">
                <!-- Pending Tab -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <!-- In the Pending tab section -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Please note: Your booking will be automatically cancelled if payment is not made within 14 days of booking or if payment is not received 14 days before the trip date.
                    </div>
                    @if(($bookings->where('payment_status', 'pending')->where('available_date', '>=', now()->toDateString())->isEmpty()) && 
                        (empty($privateBookings) || $privateBookings->where('payment_status', 'pending')->where('available_date', '>=', now()->toDateString())->isEmpty()))
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No pending bookings at the moment.</p>
                        </div>
                    @else
                        <div class="row g-4">
                            <!-- Regular bookings -->
                            @foreach($bookings->where('payment_status', 'pending')->where('available_date', '>=', now()->toDateString()) as $booking)
                                <div class="col-md-6 col-lg-4">
                                    <a href="{{ route('payment.page', $booking->id) }}" class="text-decoration-none">
                                        <div class="card h-100 border-warning hover-shadow transition">
                                            <div class="card-body">
                                            <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title text-dark mb-0">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}</h5>
                                                    <span class="badge bg-warning text-dark">Pending Payment</span>
                                                </div>
                                                <div class="card-text text-dark">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="far fa-calendar-alt me-2"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-tag me-2"></i>
                                                        <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                            
                            <!-- Private bookings -->
                            @if(isset($privateBookings))
                                @foreach($privateBookings->where('payment_status', 'pending')->where('available_date', '>=', now()->toDateString()) as $booking)
                                    <div class="col-md-6 col-lg-4">
                                        <a href="{{ route('private-booking.payment', $booking->id) }}" class="text-decoration-none">
                                            <div class="card h-100 border-warning hover-shadow transition">
                                                <!-- Card content similar to regular bookings but with indication it's a private booking -->
                                                <div class="card-body">
                                                <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h5 class="card-title text-dark mb-0">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }} <span class="badge bg-primary">Private</span></h5>
                                                        <span class="badge bg-warning text-dark">Pending Payment</span>
                                                    </div>
                                                    <div class="card-text text-dark">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="far fa-calendar-alt me-2"></i>
                                                            <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-tag me-2"></i>
                                                            <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>


                <!-- Booked Tab -->
                <!-- Add this alert in the Booked tab section, after the tab-pane opening div -->
                <div class="tab-pane fade" id="booked" role="tabpanel" aria-labelledby="booked-tab">
                   
                    
                    @if(($bookings->where('payment_status', 'paid')->where('available_date', '>=', now()->toDateString())->isEmpty()) && 
                        (empty($privateBookings) || $privateBookings->where('payment_status', 'paid')->where('available_date', '>=', now()->toDateString())->isEmpty()))
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No booked trips at the moment.</p>
                        </div>
                    @else
                        <div class="row g-4">
                            <!-- Regular bookings -->
                            @foreach($bookings->where('payment_status', 'paid')->where('available_date', '>=', now()->toDateString()) as $booking)
                                <div class="col-md-6 col-lg-4">
                                    <a href="{{ route('customer.booking.detail', $booking->id) }}" class="text-decoration-none">
                                        <div class="card h-100 border-success hover-shadow transition">
                                            <div class="card-body">
                                            <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge bg-success">Payment Completed</span>
                                                </div>
                                                <h5 class="card-title text-dark mb-3">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}</h5>
                                                <div class="card-text text-dark">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="far fa-calendar-alt me-2"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-tag me-2"></i>
                                                        <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                            
                            <!-- Private bookings -->
                            @if(isset($privateBookings))
                                @foreach($privateBookings->where('payment_status', 'paid')->where('available_date', '>=', now()->toDateString()) as $booking)
                                    <div class="col-md-6 col-lg-4">
                                        <a href="{{ route('customer.booking.private-detail', $booking->id) }}" class="text-decoration-none">
                                            <div class="card h-100 border-success hover-shadow transition">
                                                <div class="card-body">
                                                <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h5 class="card-title text-dark mb-0">
                                                            {{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}
                                                        </h5>
                                                        <span class="badge bg-success">Payment Completed</span>
                                                    </div>
                                                    <div class="card-text text-dark">
                                                        <span class="badge bg-primary mb-2">Private</span>
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="far fa-calendar-alt me-2"></i>
                                                            <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-tag me-2"></i>
                                                            <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Done Tab -->
                <div class="tab-pane fade" id="done" role="tabpanel" aria-labelledby="done-tab">
                    @if(($bookings->where('payment_status', 'paid')->where('available_date', '<', now()->toDateString())->isEmpty()) && 
                        (empty($privateBookings) || $privateBookings->where('payment_status', 'paid')->where('available_date', '<', now()->toDateString())->isEmpty()))
                        <div class="text-center py-5">
                            <i class="fas fa-flag-checkered fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No completed trips yet.</p>
                        </div>
                    @else
                        <div class="row g-4">
                            <!-- Regular bookings -->
                            @foreach($bookings->where('payment_status', 'paid')->where('available_date', '<', now()->toDateString()) as $booking)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-secondary hover-shadow transition">
                                        <div class="card-body">
                                        <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}</h5>
                                                <span class="badge bg-secondary">Trip Completed</span>
                                            </div>
                                            <div class="card-text">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="far fa-calendar-alt me-2"></i>
                                                    <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-tag me-2"></i>
                                                    <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                </div>
                                                
                                                <!-- Check if user has already reviewed this booking -->
                                                @php
                                                    $hasReview = $booking->reviews()->where('user_id', auth()->id())->exists();
                                                @endphp
                                                
                                                @if($hasReview)
                                                    <div class="d-grid">
                                                        <a href="{{ route('customer.reviews.index') }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i> View Your Review
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="d-grid">
                                                        <a href="{{ route('customer.review.create', $booking->id) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-star me-1"></i> Leave a Review
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Private bookings -->
                            @if(isset($privateBookings))
                                @foreach($privateBookings->where('payment_status', 'paid')->where('available_date', '<', now()->toDateString()) as $booking)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border-secondary hover-shadow transition">
                                            <div class="card-body">
                                            <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title mb-0">
                                                        {{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}
                                                    </h5>
                                                    <span class="badge bg-secondary">Trip Completed</span>
                                                </div>
                                                <div class="card-text">
                                                    <span class="badge bg-primary mb-2">Private</span>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="far fa-calendar-alt me-2"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <i class="fas fa-tag me-2"></i>
                                                        <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                    </div>
                                                    
                                                    <!-- Check if user has already reviewed this booking -->
                                                    @php
                                                        $hasReview = $booking->reviews()->where('user_id', auth()->id())->exists();
                                                    @endphp
                                                    
                                                    @if($hasReview)
                                                        <div class="d-grid">
                                                            <a href="{{ route('customer.reviews.index') }}" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye me-1"></i> View Your Review
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="d-grid">
                                                            <a href="{{ route('customer.review.create.private', ['bookingId' => $booking->id, 'isPrivate' => true]) }}" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-star me-1"></i> Leave a Review
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Done Tab content here... -->
                
                <!-- Cancelled Tab -->
                <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                    @if(($bookings->where('payment_status', 'cancelled')->isEmpty()) && 
                        (empty($privateBookings) || $privateBookings->where('payment_status', 'cancelled')->isEmpty()))
                        <div class="text-center py-5">
                            <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No cancelled bookings.</p>
                        </div>
                    @else
                        <div class="row g-4">
                            <!-- Regular cancelled bookings -->
                            @foreach($bookings->where('payment_status', 'cancelled') as $booking)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-danger hover-shadow transition">
                                        <div class="card-body">
                                        <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}</h5>
                                                <span class="badge bg-danger">Booking Cancelled</span>
                                            </div>
                                            <div class="card-text">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="far fa-calendar-alt me-2"></i>
                                                    <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-tag me-2"></i>
                                                    <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Private cancelled bookings -->
                            @if(isset($privateBookings))
                                @foreach($privateBookings->where('payment_status', 'cancelled') as $booking)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border-danger hover-shadow transition">
                                            <div class="card-body">
                                            <span class="text-muted small">Booking ID: #{{ $booking->id }}</span>

                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title mb-0">
                                                        {{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}
                                                    </h5>
                                                    <span class="badge bg-danger">Booking Cancelled</span>
                                                </div>
                                                <div class="card-text">
                                                    <span class="badge bg-primary mb-2">Private</span>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="far fa-calendar-alt me-2"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-tag me-2"></i>
                                                        <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}
.transition {
    transition: all 0.3s ease;
}
.nav-pills .nav-link.active {
    background-color: #0d6efd;
    color: white;
}
.nav-pills .nav-link {
    color: #495057;
}
</style>

@push('scripts')
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
@endpush
@endsection




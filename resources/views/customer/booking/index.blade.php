@extends('layout.layout')

@section('title', 'My Bookings')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">My Bookings</h1>

    <ul class="nav nav-tabs mb-4 justify-content-center" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                Pending <span class="badge bg-warning text-dark">{{ $bookings->where('payment_status', 'pending')->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="booked-tab" data-bs-toggle="tab" data-bs-target="#booked" type="button" role="tab">
                Booked <span class="badge bg-success">{{ $bookings->where('payment_status', 'paid')->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="done-tab" data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab">
                Done <span class="badge bg-secondary">{{ $bookings->where('available_date', '<', now()->toDateString())->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Pending Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            @if($bookings->where('payment_status', 'pending')->isEmpty())
                <div class="d-flex flex-column justify-content-center align-items-center" style="height: 300px;">
                    <p class="text-center">No pending bookings at the moment.</p>
                </div>
            @else
                <div class="row g-4 justify-content-center">
                    @foreach($bookings->where('payment_status', 'pending') as $booking)
                        <div class="col-md-4">
                            <!-- Wrap the entire card with a link to the payment page -->
                            <a href="{{ route('payment.page', $booking->id) }}" class="text-decoration-none">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                    <h5 class="card-title text-dark">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}</h5>
                                    <p class="card-text text-dark">Date: {{ $booking->available_date }}</p>
                                    <p class="card-text text-dark">Price: RM {{ number_format($booking->total_price, 2) }}</p>

                                        <span class="badge bg-warning text-dark">Pending Payment</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>


        <!-- Booked Tab -->
        <div class="tab-pane fade" id="booked" role="tabpanel" aria-labelledby="booked-tab">
            @if($bookings->where('payment_status', 'paid')->isEmpty())
                <div class="d-flex flex-column justify-content-center align-items-center" style="height: 300px;">
                    <p class="text-center">No booked trips at the moment.</p>
                </div>
            @else
                <div class="row g-4 justify-content-center">
                @foreach($bookings->where('payment_status', 'paid')->where('available_date', '>=', now()->toDateString()) as $booking)
                <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}</h5>
                                    <p class="card-text">Date: {{ $booking->available_date }}</p>
                                    <p class="card-text">Price: RM {{ number_format($booking->total_price, 2) }}</p>
                                    <span class="badge bg-success">Payment Completed</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Done Tab -->
        <div class="tab-pane fade" id="done" role="tabpanel" aria-labelledby="done-tab">
            @if($bookings->where('available_date', '<', now()->toDateString())->isEmpty())
                <div class="d-flex flex-column justify-content-center align-items-center" style="height: 300px;">
                    <p class="text-center">No completed trips yet.</p>
                </div>
            @else
                <div class="row g-4 justify-content-center">
                    @foreach($bookings->where('available_date', '<', now()->toDateString()) as $booking)
                        <div class="col-md-4">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $booking->travelPackage ? $booking->travelPackage->name : 'Unknown Package' }}</h5>
                                    <p class="card-text">Date: {{ $booking->available_date }}</p>
                                    <p class="card-text">Price: RM {{ number_format($booking->total_price, 2) }}</p>
                                    <span class="badge bg-secondary">Trip Completed</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

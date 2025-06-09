@extends('layout.layout')

@section('title', 'Private Booking Payment Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="card-title mb-3">Private Booking Confirmed!</h2>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <p class="lead mb-4">
                        Your private travel package has been successfully booked and payment has been processed.
                        Thank you for choosing our service!
                    </p>
                    
                    
                    <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
                        <a href="{{ route('my-booking') }}" class="btn btn-primary">
                            <i class="fas fa-list-alt me-2"></i>View My Bookings
                        </a>
                        <a href="{{ route('homepage') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Return to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layout.layout')

@section('title', 'Payment Page')

@section('content')
<div class="container py-5">

    <h1 class="text-center mb-4">Payment Page</h1>

    <div class="alert alert-info mb-4">
        <p class="mb-0">Please review your payment details below:</p>
    </div>

    <div class="card shadow-sm p-4 mb-4">
        <h4 class="mb-3">Booking Details</h4>
        <p><strong>Package Name:</strong> {{ $package->name }}</p>
        <p><strong>Booking ID:</strong> {{ $bookingId }}</p>
        <p><strong>Total Price:</strong> RM{{ number_format($totalPrice, 2) }}</p>
        <p><strong>Adults:</strong> {{ $booking->adults }}</p>
        <p><strong>Children:</strong> {{ $booking->children }}</p>
        <p><strong>Infants:</strong> {{ $booking->infants }}</p>
    </div>

    <div class="card shadow-sm p-4">
        <h4 class="mb-3">Payment Method</h4>
        <form action="{{ route('payment.process', $bookingId) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="payment_method" class="form-label">Choose Payment Method:</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="pay_now">Pay Now</option>
                    <option value="pay_later">Pay Later</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success w-100">Confirm Payment</button>
        </form>
    </div>

</div>
@endsection

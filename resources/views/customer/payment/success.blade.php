@extends('layout.layout')

@section('title', 'Payment Successful')

@section('content')
<div class="container py-5 text-center">
    <h1>Payment Successful</h1>
    <p>Thank you for your payment. Your booking is confirmed!</p>
    <a href="{{ route('my-booking') }}" class="btn btn-primary">Go to My Bookings</a>
</div>
@endsection

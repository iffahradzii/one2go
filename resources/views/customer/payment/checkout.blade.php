@extends('layout.layout')
@section('title', 'Checkout')


@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Checkout for {{ $packageName }}</h1>
    
    <!-- Total Price Display -->
    <div class="mb-4">
        <h4>Total Amount: <strong>RM {{ number_format($totalPrice, 2) }}</strong></h4>
    </div>

    <!-- Display errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Pay Now Section -->
    <h3>Pay Now</h3>
    <form action="{{ route('payment.process', ['bookingId' => $bookingId]) }}" method="POST">
        @csrf

        <!-- Hidden Input to Send Total Price -->
        <input type="hidden" name="totalPrice" value="{{ $totalPrice }}">

        <!-- Stripe Elements -->
        <div class="mb-3">
            <label for="card-element" class="form-label">Credit/Debit Card</label>
            <div id="card-element">
                <!-- Stripe Card Element will be inserted here -->
            </div>
        </div>

        <div id="card-errors" class="text-danger" role="alert"></div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">Pay Now</button>
    </form>

    <!-- Divider -->
    <hr class="my-5">

    <!-- Pay Later Section -->
    <h3>Pay Later</h3>
    <form id="pay-later-form" method="POST" action="{{ route('payment.paylater') }}">
        @csrf
        <input type="hidden" name="totalPrice" value="{{ $totalPrice }}">
        <button type="submit" class="btn btn-secondary w-100">Pay Later</button>
    </form>
</div>

<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const { token, error } = await stripe.createToken(card);
        if (error) {
            document.getElementById('card-errors').textContent = error.message;
        } else {
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            form.submit();
        }
    });
</script>
@endsection

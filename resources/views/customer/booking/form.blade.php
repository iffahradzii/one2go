@extends('layout.layout')

@section('title', 'Booking Form')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Booking Form for {{ $package->name }}</h1>
    
    <form action="{{ route('booking.store', $package->id) }}" method="POST">
    @csrf
        
        <div class="mb-3">
            <label for="available_date" class="form-label">Available Date</label>
            <select name="available_date" id="available_date" class="form-control" required>
                @foreach (json_decode($package->available_dates, true) as $date)
                    <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="customer_email" class="form-label">Email</label>
            <input type="email" name="customer_email" id="customer_email" class="form-control" 
                   value="{{ $user->email }}" readonly>
        </div>

        <div class="mb-3">
            <label for="customer_phone" class="form-label">Phone Number</label>
            <input type="text" name="customer_phone" id="customer_phone" class="form-control" 
                   value="{{ $user->phone ?? '' }}" readonly>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="adults" class="form-label">Adults</label>
                <input type="number" name="adults" id="adults" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="children" class="form-label">Children</label>
                <input type="number" name="children" id="children" class="form-control" min="0" value="0" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="infants" class="form-label">Infants</label>
                <input type="number" name="infants" id="infants" class="form-control" min="0" value="0" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="total_price" class="form-label">Total Price</label>
            <input type="hidden" name="totalPrice" id="totalPriceInput">
            <input type="text" id="total_price" class="form-control" value="RM {{ number_format($package->price, 2) }}" disabled>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Add any special requests or notes here..."></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Confirm Booking</button>
    </form>
</div>

<script>
    function updateTotalPrice() {
        // Get the package price from the hidden input
        let packagePrice = parseFloat("{{ $package->price }}");

        // Get the number of adults, children, and infants
        let adults = parseInt(document.getElementById("adults").value) || 0;
        let children = parseInt(document.getElementById("children").value) || 0;
        let infants = parseInt(document.getElementById("infants").value) || 0;

        // Calculate the total price
        let totalPrice = (adults + children) * packagePrice; // Infants are free

        // Update the hidden input for total price
        document.getElementById("totalPriceInput").value = totalPrice.toFixed(2);

        // Update the visible total price field
        document.getElementById("total_price").value = `RM ${totalPrice.toFixed(2)}`;
    }

    // Add event listeners to update the total price when values change
    document.getElementById("adults").addEventListener("input", updateTotalPrice);
    document.getElementById("children").addEventListener("input", updateTotalPrice);
    document.getElementById("infants").addEventListener("input", updateTotalPrice);

    // Call the function once on page load to initialize the total price
    window.onload = updateTotalPrice;
</script>

@endsection

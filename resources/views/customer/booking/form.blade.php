@extends('layout.layout')

@section('title', 'Booking Form')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Booking Form for {{ $package->name }}</h1>
    
    <form action="{{ route('booking.store', $package->id) }}" method="POST" id="bookingForm">
    @csrf
        <!-- Existing date and contact fields remain the same -->
        <div class="mb-3">
            <label for="available_date" class="form-label">Available Date</label>
            <select name="available_date" id="available_date" class="form-control" required>
                @php
                    // Handle available dates properly
                    $availableDates = is_string($package->available_dates) 
                        ? json_decode($package->available_dates, true) 
                        : $package->available_dates;
                @endphp
                
                <!-- Use $availableDates instead of directly using json_decode on $package->available_dates -->
                @foreach ($availableDates as $date)
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

        <!-- New Travelers Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Travelers Information</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="addTraveler()">
                    Add Traveler
                </button>
            </div>
            <div class="card-body" id="travelersContainer">
                <!-- Travelers will be added here dynamically -->
            </div>
        </div>

        <!-- Price Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Price Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p>Adults (12+ years): <span id="adultsCount">0</span></p>
                        <p>Children (2-11 years): <span id="childrenCount">0</span></p>
                        <p>Infants (0-1 years): <span id="infantsCount">0</span></p>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="total_price" class="form-label">Total Price</label>
                            <input type="hidden" name="totalPrice" id="totalPriceInput">
                            <input type="text" id="total_price" class="form-control" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Add any special requests or notes here..."></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Confirm Booking</button>
    </form>
</div>

@endsection

@section('scripts')
<script>
let travelers = [];
const basePrice = parseFloat("{{ $package->price }}");
const childDiscount = 0.5; // 50% discount for children
const today = new Date();

function addTraveler() {
    const travelerId = Date.now();
    const travelerHtml = `
        <div class="traveler-entry border-bottom pb-3 mb-3" id="traveler-${travelerId}">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="travelers[${travelerId}][name]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">IC Number</label>
                    <input type="text" name="travelers[${travelerId}][ic]" 
                           class="form-control ic-number" 
                           pattern="[0-9]{12}"
                           maxlength="12"
                           placeholder="YYMMDDXXXXXX"
                           onchange="updateTravelerAge(${travelerId}, this.value)"
                           required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Age Category</label>
                    <input type="text" class="form-control" id="age-category-${travelerId}" readonly>
                    <input type="hidden" name="travelers[${travelerId}][category]" id="category-${travelerId}">
                </div>
                <div class="col-md-1 mb-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm d-block" 
                            onclick="removeTraveler(${travelerId})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    document.getElementById('travelersContainer').insertAdjacentHTML('beforeend', travelerHtml);
}

function updateTravelerAge(id, icNumber) {
    if (icNumber.length === 12) {
        const year = parseInt(icNumber.substring(0, 2));
        const month = parseInt(icNumber.substring(2, 4)) - 1;
        const day = parseInt(icNumber.substring(4, 6));
        
        // Calculate full year (assuming 20xx for years 00-23, 19xx for years 24-99)
        const fullYear = year + (year <= 23 ? 2000 : 1900);
        const birthDate = new Date(fullYear, month, day);
        const ageInYears = (today - birthDate) / (365.25 * 24 * 60 * 60 * 1000);

        let category;
        if (ageInYears >= 12) {
            category = 'Adult';
        } else if (ageInYears >= 2) {
            category = 'Child';
        } else {
            category = 'Infant';
        }

        document.getElementById(`age-category-${id}`).value = category;
        document.getElementById(`category-${id}`).value = category;
        updateTotalPrice();
    }
}

function removeTraveler(id) {
    document.getElementById(`traveler-${id}`).remove();
    updateTotalPrice();
}

function updateTotalPrice() {
    let adults = 0, children = 0, infants = 0;
    const categories = document.querySelectorAll('[id^="category-"]');
    
    categories.forEach(category => {
        switch(category.value) {
            case 'Adult':
                adults++;
                break;
            case 'Child':
                children++;
                break;
            case 'Infant':
                infants++;
                break;
        }
    });

    // Update counts
    document.getElementById('adultsCount').textContent = adults;
    document.getElementById('childrenCount').textContent = children;
    document.getElementById('infantsCount').textContent = infants;

    // Calculate total price
    const totalPrice = (adults * basePrice) + (children * basePrice * childDiscount);
    document.getElementById('totalPriceInput').value = totalPrice.toFixed(2);
    document.getElementById('total_price').value = `RM ${totalPrice.toFixed(2)}`;
}

// Add first traveler by default
document.addEventListener('DOMContentLoaded', function() {
    addTraveler();
});
</script>
@endsection

@extends('layout.layout')

@section('title', 'Booking Form')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Booking Form for {{ $package->name }}</h2>
                    <p class="text-muted mb-4">Complete the form below to book your travel experience.</p>
                    
                    <form action="{{ route('booking.store', $package->id) }}" method="POST" id="bookingForm">
                        @csrf
                        
                        <!-- Customer Details -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-user-circle me-2"></i>Contact Information</h4>
                            <div class="row g-3">
                                <!-- Email and Phone from database -->
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="customer_email" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="customer_phone" class="form-control" value="{{ $user->phone ?? '' }}" readonly>
                                </div>
                                
                                <!-- Travel Date -->
                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold">Travel Date</label>
                                    <select name="available_date" class="form-select" id="available_date" required>
                                        <option value="">Select an available date</option>
                                        @php
                                            // Handle available dates properly
                                            $availableDates = is_string($package->available_dates) 
                                                ? json_decode($package->available_dates, true) 
                                                : $package->available_dates;
                                        @endphp
                                        
                                        @if(is_array($availableDates) && !empty($availableDates))
                                            @foreach($availableDates as $date)
                                                <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('d M Y') }} ({{ \Carbon\Carbon::parse($date)->format('l') }})</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No available dates found</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Travelers Information -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-users me-2"></i>Travelers Information</h4>
                            <p class="text-muted small mb-3">Please provide details for all travelers</p>
                            <div id="travelersContainer">
                                <!-- Travelers will be added here dynamically -->
                            </div>
                            <button type="button" class="btn btn-outline-primary mt-2" onclick="addTraveler()">
                                <i class="fas fa-plus"></i> Add Traveler
                            </button>
                        </div>

                        <!-- Notes Section -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-sticky-note me-2"></i>Special Requests</h4>
                            <p class="text-muted small mb-3">Let us know if you have any special requests or requirements</p>
                            <textarea name="notes" id="notes" class="form-control" rows="4" 
                                    placeholder="Examples: Dietary requirements, accessibility needs, etc."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-credit-card me-2"></i>Continue to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Price Summary -->
        <div class="col-md-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Price Summary</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">{{ $package->name }}</h5>
                    <p class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $package->country ?? $package->location }}</p>
                    <p class="mb-3"><i class="fas fa-clock me-2 text-primary"></i>{{ $package->days ?? $package->duration }} Days</p>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Adults (12+ years):</span>
                            <span id="adultsCount" class="fw-bold">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Children (2-11 years):</span>
                            <span id="childrenCount" class="fw-bold">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Infants (0-1 years):</span>
                            <span id="infantsCount" class="fw-bold">0</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Base Price:</span>
                        <span class="fw-bold">RM {{ number_format($package->price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                        <span>Total Price:</span>
                        <span id="total_price" class="text-primary">RM 0.00</span>
                        <input type="hidden" name="totalPrice" id="totalPriceInput" value="0">
                    </div>
                    
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Children (2-11 years) receive a 50% discount. Infants (0-1 years) travel for free.
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        <div class="traveler-row mb-3 p-3 border rounded bg-white" id="traveler-${travelerId}">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold">Traveler #${document.querySelectorAll('.traveler-row').length + 1}</span>
                ${document.querySelectorAll('.traveler-row').length > 0 ? 
                    `<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTraveler(${travelerId})">
                        <i class="fas fa-times"></i> Remove
                    </button>` : ''}
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="travelers[${travelerId}][name]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">IC Number</label>
                    <input type="text" name="travelers[${travelerId}][ic]" 
                           class="form-control ic-number" 
                           pattern="[0-9]{12}"
                           maxlength="12"
                           onchange="updateTravelerAge(${travelerId}, this.value)"
                           required>
                    <small class="form-text text-muted">Format: 000000-00-0000</small>
                </div>
                <div class="col-md-6">
                    <input type="hidden" class="form-control" id="age-category-${travelerId}" readonly>
                    <input type="hidden" name="travelers[${travelerId}][category]" id="category-${travelerId}">
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
    
    // Renumber the remaining travelers
    const travelerRows = document.querySelectorAll('.traveler-row');
    travelerRows.forEach((row, index) => {
        const titleElement = row.querySelector('.fw-bold');
        if (titleElement) {
            titleElement.textContent = `Traveler #${index + 1}`;
        }
    });
    
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
    document.getElementById('total_price').textContent = `RM ${totalPrice.toFixed(2)}`;
}

// Add first traveler by default
document.addEventListener('DOMContentLoaded', function() {
    addTraveler();
});
</script>
@endsection

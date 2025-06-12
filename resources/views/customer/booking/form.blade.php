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
                        
                    <!-- Date Selection -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h4 class="mb-3"><i class="far fa-calendar-alt me-2"></i>Select Travel Date</h4>
                        <p class="text-muted small mb-3">Choose your preferred travel date</p>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="date_option" id="predefinedDate" value="predefined" checked>
                            <label class="form-check-label" for="predefinedDate">
                                Choose from available dates
                            </label>
                        </div>
                        
                        <!-- Date Selection section -->
                        <div id="predefined_dates_container" class="mb-3">
                            <select class="form-select" id="available_date_select" name="available_date" required>
                                <option value="">Select a date</option>
                                @php
                                    use Carbon\Carbon;

                                    $today = Carbon::today();
                                    $futureDate = $today->copy()->addDays(14); // Date 14 days from now
                                    $availableDates = is_string($package->available_dates) 
                                        ? json_decode($package->available_dates, true) 
                                        : $package->available_dates;
                                @endphp
                                
                                @if(is_array($availableDates) && !empty($availableDates))
                                    @foreach($availableDates as $date)
                                        @if(Carbon::parse($date)->greaterThanOrEqualTo($futureDate))
                                            <option value="{{ $date }}">{{ Carbon::parse($date)->format('d F Y') }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            
                            @if(!isset($availableDates) || !is_array($availableDates) || empty(array_filter($availableDates, fn($d) => Carbon::parse($d)->greaterThanOrEqualTo($futureDate))))
                                <div class="text-danger mt-2">No available dates found. Please select a custom date.</div>
                            @endif
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

                        <!-- Hidden input for total_price inside the form -->
                        <input type="hidden" name="total_price" id="totalPriceInput" value="0">

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
                        <!-- Remove this duplicate hidden input -->
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
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           onchange="updateTravelerAge(${travelerId}, this.value)"
                           required>
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
    console.log(`Updating age for traveler ${id} with IC: ${icNumber}`);
    
    if (icNumber.length === 12) {
        // Get current date
        const today = new Date();

        // Extract year, month, day from IC number
        const yearPrefix = parseInt(icNumber.substring(0, 2), 10);
        const month = parseInt(icNumber.substring(2, 4), 10) - 1; // JS months are 0-indexed
        const day = parseInt(icNumber.substring(4, 6), 10);

        const currentYear = today.getFullYear();
        const currentYearLastTwoDigits = currentYear % 100;

        // Determine full year
        const fullYear = yearPrefix > currentYearLastTwoDigits ? 1900 + yearPrefix : 2000 + yearPrefix;

        // Create birthdate
        const birthDate = new Date(fullYear, month, day);

        // Calculate age
        let age = currentYear - fullYear;
        if (
            today.getMonth() < month || 
            (today.getMonth() === month && today.getDate() < day)
        ) {
            age--;
        }

        // Determine age category
        let category;
        if (age >= 12) {
            category = 'Adult';
        } else if (age >= 2) {
            category = 'Child';
        } else {
            category = 'Infant';
        }

        // Add debug logging
        console.log(`Calculated age: ${age}, Category: ${category}`);
        
        // Update DOM elements
        document.getElementById(`age-category-${id}`).value = category;
        document.getElementById(`category-${id}`).value = category;
        
        // Update price
        updateTotalPrice();
    } else {
        console.log(`IC number length is not 12: ${icNumber.length}`);
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
    
    // Make sure we're updating the input inside the form
    document.getElementById('totalPriceInput').value = totalPrice.toFixed(2);
    document.getElementById('total_price').textContent = `RM ${totalPrice.toFixed(2)}`;
    
    // Set the form submission flag
    window.canSubmitForm = adults > 0;
    
    console.log("Total price updated to: " + totalPrice.toFixed(2)); // Debug log
}

// Add first traveler by default
document.addEventListener('DOMContentLoaded', function() {
    addTraveler();
});

// Add this to your script section
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    console.log('Form submission attempted');
    
    // Check if travel date is selected
    const travelDate = document.getElementById('available_date_select').value;
    if (!travelDate) {
        e.preventDefault();
        alert('Please select a travel date');
        console.log('Form submission prevented: No travel date selected');
        return;
    }
    
    // Check if there are any travelers
    const travelerRows = document.querySelectorAll('.traveler-row');
    if (travelerRows.length === 0) {
        e.preventDefault();
        alert('Please add at least one traveler');
        console.log('Form submission prevented: No travelers added');
        return;
    }
    
    // Check if all travelers have categories assigned
    const categories = document.querySelectorAll('[id^="category-"]');
    let allCategoriesSet = true;
    let hasAdult = false;
    
    categories.forEach(category => {
        if (!category.value) {
            allCategoriesSet = false;
            console.log(`Missing category for traveler with element ID: ${category.id}`);
        }
        if (category.value === 'Adult') {
            hasAdult = true;
        }
    });
    
    // Check if at least one adult is present
    if (!hasAdult) {
        e.preventDefault();
        alert('At least one adult (12+ years) must be included in the booking.');
        console.log('Form submission prevented: No adult traveler');
        return;
    }
    
    // Check total price
    const totalPrice = parseFloat(document.getElementById('totalPriceInput').value);
    if (isNaN(totalPrice) || totalPrice <= 0) {
        e.preventDefault();
        alert('Invalid total price. Please ensure all traveler information is complete.');
        console.log(`Form submission prevented: Invalid total price: ${totalPrice}`);
        return;
    }
    
    if (!allCategoriesSet) {
        e.preventDefault();
        alert('Please ensure all traveler information is complete. Make sure to enter valid IC numbers.');
        console.log('Form submission prevented: Missing traveler categories');
        return;
    }
    
    // Check if form can be submitted (at least one adult present)
    if (!window.canSubmitForm) {
        e.preventDefault();
        alert('At least one adult (12+ years) must be included in the booking.');
        console.log('Form submission prevented: No adult traveler');
        return;
    }
    
    console.log('Form validation passed, submitting form');
});
</script>
@endsection

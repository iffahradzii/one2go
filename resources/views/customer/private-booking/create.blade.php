@extends('layout.layout')

@section('title', 'Private Booking')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Private Booking for {{ $package->name }}</h2>
                    
                    <form action="{{ route('private-booking.store', $package) }}" method="POST" id="bookingForm">
                        @csrf
                        
                        <!-- Customer Details -->
                        <div class="mb-4">
                            <h4>Contact Information</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="customer_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="customer_email" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="customer_phone" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Travel Date</label>
                                    <input type="date" name="available_date" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Participants -->
                        <div class="mb-4">
                            <h4>Participants</h4>
                            <div id="participants-container">
                                <div class="participant-row mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="participants[0][name]" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">IC Number</label>
                                            <input type="text" name="participants[0][ic_number]" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary mt-2" id="addParticipant">
                                <i class="fas fa-plus"></i> Add Participant
                            </button>
                        </div>

                        <!-- Additional Activities -->
                        @if($activities->count() > 0)
                        <div class="mb-4">
                            <h4>Additional Activities</h4>
                            <div class="row g-3">
                                @foreach($activities as $activity)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" name="activities[]" value="{{ $activity->id }}" 
                                                       class="form-check-input activity-checkbox" 
                                                       id="activity{{ $activity->id }}">
                                                <label class="form-check-label" for="activity{{ $activity->id }}">
                                                    {{ $activity->name }}
                                                    <span class="text-primary d-block">RM {{ number_format($activity->price, 2) }}</span>
                                                </label>
                                            </div>
                                            @if($activity->description)
                                                <small class="text-muted d-block mt-2">{{ $activity->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Custom Itinerary -->
                        <div class="mb-4">
                            <h4>Custom Itinerary Requests</h4>
                            <textarea name="custom_itinerary" class="form-control" rows="4" 
                                    placeholder="Any specific requests or modifications to the itinerary..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Continue to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Price Summary -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Price Summary</h4>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Base Price:</span>
                            <span>RM {{ number_format($package->price, 2) }}</span>
                        </div>
                        <div id="activities-price" class="d-flex justify-content-between mb-2">
                            <span>Additional Activities:</span>
                            <span>RM 0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Price:</span>
                            <span id="total-price">RM {{ number_format($package->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let participantCount = 1;
    
    document.getElementById('addParticipant').addEventListener('click', function() {
        const container = document.getElementById('participants-container');
        const newRow = document.createElement('div');
        newRow.className = 'participant-row mb-3';
        newRow.innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="participants[${participantCount}][name]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">IC Number</label>
                    <input type="text" name="participants[${participantCount}][ic_number]" class="form-control" required>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger mt-2 remove-participant">Remove</button>
        `;
        container.appendChild(newRow);
        participantCount++;

        // Add remove functionality
        newRow.querySelector('.remove-participant').addEventListener('click', function() {
            this.parentElement.remove();
        });
    });

    // Calculate total price when activities are selected
    const activityCheckboxes = document.querySelectorAll('.activity-checkbox');
    const basePrice = {{ $package->price }};

    activityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotalPrice);
    });

    function updateTotalPrice() {
        let additionalPrice = 0;
        activityCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const price = parseFloat(checkbox.closest('.card-body').querySelector('.text-primary').textContent.replace('RM ', ''));
                additionalPrice += price;
            }
        });

        document.getElementById('activities-price').querySelector('span:last-child').textContent = 
            `RM ${additionalPrice.toFixed(2)}`;
        document.getElementById('total-price').textContent = 
            `RM ${(basePrice + additionalPrice).toFixed(2)}`;
    }
</script>
@endpush
@endsection
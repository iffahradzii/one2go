@extends('layout.layout')

@section('title', 'Payment - Private Booking')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title mb-4">Payment Details</h2>
                    
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="booking-summary mb-4">
                        <h4>Booking Summary</h4>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>{{ $privateBooking->travelPackage->name }}</h5>
                                <p class="text-muted">{{ $privateBooking->travelPackage->country }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Travel Date:</strong><br>
                                        {{ \Carbon\Carbon::parse($privateBooking->available_date)->format('d F Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Number of Travelers:</strong><br>
                                        {{ $privateBooking->participants->count() }}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total Amount:</h5>
                                    <h5>RM {{ number_format($privateBooking->total_price, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List of Participants -->
                    <div class="mb-4">
                        <h4>Participants</h4>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Name</th>
                                                <th>IC</th>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($privateBooking->participants as $index => $participant)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $participant->name }}</td>
                                                <td>{{ $participant->ic_number }}</td>
                                                <td>

                                                <span class="badge {{ $participant->type == 'adult' ? 'bg-primary' : ($participant->type == 'child' ? 'bg-info' : 'bg-warning') }}">
                                            {{ $participant->type }}
                                        </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Activities -->
                    <div class="mb-4">
                        <h4>Additional Activities</h4>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Activity</th>
                                                <th>Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($privateBooking->activities as $activity)
                                            <tr>
                                                <td>{{ $activity->activity_name }}</td>
                                                <td>RM {{ number_format($activity->activity_price, 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td class="fw-bold">Total Additional Activities</td>
                                                <td class="fw-bold">RM {{ number_format($privateBooking->additional_price ?? 0, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Itinerary -->
                    <div class="mb-4">
                        <h4>Itinerary</h4>
                        <div class="card bg-light">
                            <div class="card-body">
                                @php
                                    $hasCustomItinerary = $privateBooking->customDays && $privateBooking->customDays->count() > 0;
                                    $packageItinerary = is_string($privateBooking->travelPackage->itinerary) ? 
                                        json_decode($privateBooking->travelPackage->itinerary, true) : 
                                        $privateBooking->travelPackage->itinerary;
                                @endphp
                                
                                @if($hasCustomItinerary)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>This itinerary has been customized.
                                    </div>
                                    
                                    <div class="accordion" id="itineraryAccordion">
                                        @foreach($packageItinerary as $dayIndex => $dayActivities)
                                            @php
                                                $customDay = $privateBooking->customDays->where('day_number', $dayIndex + 1)->first();
                                                $customActivities = $customDay ? 
                                                    (is_string($customDay->custom_activities) ? 
                                                        json_decode($customDay->custom_activities, true) : 
                                                        $customDay->custom_activities) : 
                                                    null;
                                            @endphp
                                            
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading{{ $dayIndex }}">
                                                    <button class="accordion-button {{ $dayIndex > 0 ? 'collapsed' : '' }}" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#collapse{{ $dayIndex }}" 
                                                            aria-expanded="{{ $dayIndex === 0 ? 'true' : 'false' }}" 
                                                            aria-controls="collapse{{ $dayIndex }}">
                                                        Day {{ $dayIndex + 1 }}
                                                        @if($customDay)
                                                            <span class="badge bg-primary ms-2">Customized</span>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse{{ $dayIndex }}" 
                                                     class="accordion-collapse collapse {{ $dayIndex === 0 ? 'show' : '' }}" 
                                                     aria-labelledby="heading{{ $dayIndex }}" 
                                                     data-bs-parent="#itineraryAccordion">
                                                    <div class="accordion-body">
                                                        <!-- Inside the accordion-body where custom activities are displayed -->
                                                        @if($customDay && $customActivities)
                                                            <div class="timeline">
                                                                @foreach($customActivities as $time => $activity)
                                                                    <div class="timeline-item">
                                                                     <div class="timeline-content">{{ $time }}{{ $activity }}</div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            @if(is_array($dayActivities))
                                                                <div class="timeline">
                                                                    @foreach($dayActivities as $time => $activity)
                                                                        <div class="timeline-item">
                                                                            <div class="timeline-time">{{ $time }}</div>
                                                                            <div class="timeline-content">{{ $activity }}</div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <p>{{ $dayActivities }}</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">Using original package itinerary</p>
                                    
                                    <div class="accordion" id="itineraryAccordion">
                                        @if(is_array($packageItinerary))
                                            @foreach($packageItinerary as $dayIndex => $dayActivities)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading{{ $dayIndex }}">
                                                        <button class="accordion-button {{ $dayIndex > 0 ? 'collapsed' : '' }}" 
                                                                type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#collapse{{ $dayIndex }}" 
                                                                aria-expanded="{{ $dayIndex === 0 ? 'true' : 'false' }}" 
                                                                aria-controls="collapse{{ $dayIndex }}">
                                                            Day {{ $dayIndex + 1 }}
                                                        </button>
                                                    </h2>
                                                    <div id="collapse{{ $dayIndex }}" 
                                                         class="accordion-collapse collapse {{ $dayIndex === 0 ? 'show' : '' }}" 
                                                         aria-labelledby="heading{{ $dayIndex }}" 
                                                         data-bs-parent="#itineraryAccordion">
                                                        <div class="accordion-body">
                                                            @if(is_array($dayActivities))
                                                                <div class="timeline">
                                                                    @foreach($dayActivities as $time => $activity)
                                                                        <div class="timeline-item">
                                                                            <div class="timeline-time">{{ $time }}</div>
                                                                            <div class="timeline-content">{{ $activity }}</div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <p>{{ $dayActivities }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No itinerary information available.</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment Options -->
                    <div class="mb-4">
                        <h4>Payment Options</h4>
                        <div class="card bg-light">
                            <div class="card-body">
                              
                                <!-- Online Payment Button -->
                                <div id="payment-form-container" class="mb-3">
                                    <form id="payment-form" class="mb-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Card Information</label>
                                            <div id="card-element" class="form-control"></div>
                                            <div id="card-errors" class="invalid-feedback d-block"></div>
                                        </div>
                                        <button id="submit-button" type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($privateBooking->total_price, 2) }}
                                        </button>
                                    </form>
                                </div>

                                @push('scripts')
                                <script src="https://js.stripe.com/v3/"></script>
                                <style>
                                    #card-element {
                                        padding: 10px;
                                        border: 1px solid #ccc;
                                        border-radius: 4px;
                                        background-color: white;
                                        min-height: 40px;
                                    }
                                </style>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const stripe = Stripe('{{ config("services.stripe.key") }}');
                                        const elements = stripe.elements();
                                        const card = elements.create('card');
                                        card.mount('#card-element');

                                        const form = document.getElementById('payment-form');
                                        const submitButton = document.getElementById('submit-button');
                                        const displayError = document.getElementById('card-errors');

                                        form.addEventListener('submit', async function(event) {
                                            event.preventDefault();
                                            submitButton.disabled = true;
                                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                                            try {
                                                const {paymentMethod, error} = await stripe.createPaymentMethod({
                                                    type: 'card',
                                                    card: card,
                                                });

                                                if (error) {
                                                    throw error;
                                                }

                                                const response = await fetch('{{ route("private-booking.process-payment", $privateBooking->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({
                                                        payment_method_id: paymentMethod.id,
                                                        booking_id: '{{ $privateBooking->id }}',
                                                        payment_method: 'pay_now'
                                                    })
                                                });

                                                let errorMessage = 'Payment processing failed. Please try again.';
                                                
                                                if (!response.ok) {
                                                    if (response.status === 500) {
                                                        errorMessage = 'An internal server error occurred. Our team has been notified.';
                                                    } else if (response.status === 422) {
                                                        errorMessage = 'Invalid payment information. Please check your details.';
                                                    } else if (response.status === 401) {
                                                        errorMessage = 'Authentication error. Please log in again.';
                                                    }
                                                    
                                                    // Try to get more detailed error from JSON response if available
                                                    try {
                                                        const errorResult = await response.json();
                                                        if (errorResult.message) {
                                                            errorMessage = errorResult.message;
                                                        }
                                                    } catch (jsonError) {
                                                        console.error('Error parsing error response:', jsonError);
                                                    }
                                                    
                                                    throw new Error('Payment failed: ' + response.status + '. ' + errorMessage);
                                                }

                                                // Check if response is JSON before parsing
                                                const contentType = response.headers.get('content-type');
                                                if (contentType && contentType.includes('application/json')) {
                                                    const result = await response.json();
                                                    
                                                    if (result.success) {
                                                        window.location.href = '{{ route("private-payment.success") }}';
                                                    } else {
                                                        throw new Error(result.message || 'Payment failed');
                                                    }
                                                } else {
                                                    // Handle non-JSON response
                                                    if (response.ok) {
                                                        window.location.href = '{{ route("private-payment.success") }}';
                                                    } else {
                                                        throw new Error('Server returned an error: ' + response.status);
                                                    }
                                                }
                                            } catch (error) {
                                                displayError.textContent = error.message;
                                                submitButton.disabled = false;
                                                submitButton.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($privateBooking->total_price, 2) }}';
                                            }
                                        });
                                    });
                                </script>
                                @endpush

                                <!-- Pay Later Option -->
                                <form action="{{ route('private-booking.pay-later', $privateBooking->id) }}" method="POST" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="pay_later">
                                    <button type="submit" class="btn btn-outline-secondary btn-lg w-100">
                                        <i class="fas fa-clock me-2"></i>Pay Later
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payNowForm = document.querySelector('form[action*="process-payment"]');
        
        if (payNowForm) {
            payNowForm.addEventListener('submit', function(e) {
                // Prevent default submission
                e.preventDefault();
                
                // Show loading indicator
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                submitBtn.disabled = true;
                
                // Submit the form after a short delay
                setTimeout(() => {
                    this.submit();
                }, 500);
            });
        }
    });
</script>

@endsection

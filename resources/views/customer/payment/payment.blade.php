@extends('layout.layout')

@section('title', 'Payment Page')

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
                                <h5>{{ $package->name }}</h5>
                                <p class="text-muted">{{ $package->country ?? '' }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Travel Date:</strong><br>
                                        {{ \Carbon\Carbon::parse($booking->available_date)->format('d F Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Number of Travelers:</strong><br>
                                        {{ $booking->travelers->count() }}</p>
                                    </div>
                                   
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total Amount:</h5>
                                    <h5>RM {{ number_format($totalPrice, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                      <!-- Participant List -->
            <h5 class="border-bottom pb-2 mb-3">Participant List</h5>
            <div class="row mb-4">
                <div class="col-md-12">
                    @if($booking->travelers && count($booking->travelers) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Name</th>
                                    <th>IC Number</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->travelers as $index => $traveler)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $traveler->name }}</td>
                                    <td>{{ $traveler->ic_number }}</td>
                                    <td>
                                        <span class="badge {{ $traveler->category == 'Adult' ? 'bg-primary' : ($traveler->category == 'Child' ? 'bg-info' : 'bg-warning') }}">
                                            {{ $traveler->category }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No traveler information available for this booking.
                    </div>
                    @endif
                </div>
            </div>

                    <!-- Itinerary -->
                    <div class="mb-4">
                        <h4>Itinerary</h4>
                        <div class="card bg-light">
                            <div class="card-body">
                                @php
                                    $packageItinerary = is_string($package->itinerary) ? 
                                        json_decode($package->itinerary, true) : 
                                        $package->itinerary;
                                @endphp
                                
                                <p class="text-muted">Package Itinerary</p>
                                
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
                                                            @foreach($dayActivities as $time => $activity)
                                                                <p class="mb-1">
                                                                    <strong>{{ $time }}</strong> – {{ $activity }}
                                                                </p>
                                                            @endforeach
                                                        @else
                                                            {{-- In case it's still a string --}}
                                                            <p>{!! nl2br(e($dayActivities)) !!}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>No itinerary information available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="payment-options">
                        <h4 class="mb-3">Payment Options</h4>
                        
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
                                    <i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($totalPrice, 2) }}
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

                                        const response = await fetch('{{ route("payment.process", $bookingId) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                payment_method_id: paymentMethod.id,
                                                booking_id: '{{ $bookingId }}',
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
                                                errorMessage = 'Session expired. Please refresh the page and try again.';
                                            }

                                            try {
                                                const errorData = await response.json();
                                                if (errorData.message) {
                                                    errorMessage = errorData.message;
                                                }
                                            } catch (e) {
                                                // If parsing fails, use the default error message
                                                console.error('Error parsing response:', e);
                                            }

                                            throw new Error(errorMessage);
                                        }

                                        const result = await response.json();

                                        if (result.success) {
                                            window.location.href = '{{ route("payment.success") }}';
                                        } else {
                                            throw new Error(result.message || 'Payment failed');
                                        }
                                    } catch (error) {
                                        displayError.textContent = error.message;
                                        submitButton.disabled = false;
                                        submitButton.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay RM {{ number_format($totalPrice, 2) }}';
                                        console.error('Payment error:', error);
                                    }
                                });
                            });
                        </script>
                        @endpush

                    

                        <!-- Pay Later Option -->
                        <form action="{{ route('payment.process', $bookingId) }}" method="POST">
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

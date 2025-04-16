@extends('layout.layout')

@section('title', 'Private Booking')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Private Booking for {{ $package->name }}</h2>
                    <p class="text-muted mb-4">Complete the form below to customize your private booking experience.</p>
                    
                    <form action="{{ route('private-booking.store', $package) }}" method="POST" id="bookingForm">
                        @csrf
                        
                        <!-- Customer Details -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-user-circle me-2"></i>Contact Information</h4>
                            <div class="row g-3">
                                <!-- Email and Phone from database -->
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="customer_email" class="form-control" value="{{ auth()->user()->email ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="customer_phone" class="form-control" value="{{ auth()->user()->phone ?? '' }}" readonly>
                                </div>
                                
                                <!-- Travel Date Options -->
                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold">Travel Date</label>
                                    <div class="mb-3 border-start ps-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="date_option" id="predefined_date" value="predefined" checked>
                                            <label class="form-check-label" for="predefined_date">
                                                <span class="fw-medium">Choose from available dates</span>
                                                <small class="d-block text-muted">Select from our recommended travel dates</small>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="date_option" id="custom_date" value="custom">
                                            <label class="form-check-label" for="custom_date">
                                                <span class="fw-medium">Select your own date</span>
                                                <small class="d-block text-muted">Choose any date that works for you</small>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div id="predefined_dates_container" class="mt-3">
                                        <select name="available_date" class="form-select" id="available_date_select" required>
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
                                    
                                    <div id="custom_date_container" class="d-none mt-3">
                                        <div class="input-group date">
                                            <input type="text" name="custom_date" class="form-control datepicker" id="custom_date_input" placeholder="dd/mm/yyyy">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                        </div>
                                        <small class="form-text text-muted">Click to open calendar and select your preferred travel date</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Participants -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-users me-2"></i>Travelers Information</h4>
                            <p class="text-muted small mb-3">Please provide details for all travelers</p>
                            <div id="participants-container">
                                <div class="participant-row mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold">Traveler #1</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="participants[0][name]" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">IC Number</label>
                                            <input type="text" name="participants[0][ic_number]" class="form-control" required>
                                            <small class="form-text text-muted">Format: 000000-00-0000</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary mt-2" id="addParticipant">
                                <i class="fas fa-plus"></i> Add Traveler
                            </button>
                        </div>

                        <!-- Additional Activities -->
                        @php
                            $packageActivities = json_decode($package->activities, true) ?? [];
                        @endphp
                        
                        @if(!empty($packageActivities))
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-hiking me-2"></i>Additional Activities</h4>
                            <p class="text-muted small mb-3">Enhance your experience with these optional activities</p>
                            <div class="row g-3">
                                @foreach($packageActivities as $activity)
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" name="activities[]" value="{{ $activity['name'] }}" 
                                                       class="form-check-input activity-checkbox" 
                                                       id="activity{{ $loop->index }}">
                                                <label class="form-check-label" for="activity{{ $loop->index }}">
                                                    <span class="fw-bold">{{ $activity['name'] }}</span>
                                                    <span class="text-primary d-block">RM {{ number_format($activity['price'], 2) }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Custom Itinerary -->
                        <!-- Package Itinerary -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-map-marked-alt me-2"></i>Itinerary</h4>
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Package Itinerary</h5>
                                <div class="timeline">
                                    @php
                                        $packageItinerary = is_string($package->itinerary) 
                                            ? json_decode($package->itinerary, true) 
                                            : $package->itinerary;
                                        
                                        // Ensure days are properly numbered and sorted
                                        $formattedItinerary = [];
                                        if(is_array($packageItinerary) && !empty($packageItinerary)) {
                                            // Extract day numbers and sort numerically
                                            $dayNumbers = [];
                                            foreach($packageItinerary as $day => $activities) {
                                                $dayNum = preg_replace('/[^0-9]/', '', $day);
                                                $dayNumbers[$dayNum] = $activities;
                                            }
                                            
                                            // Sort by day number and rebuild with "Day X" format
                                            ksort($dayNumbers, SORT_NUMERIC);
                                            foreach($dayNumbers as $num => $activities) {
                                                $dayKey = "Day " . $num;
                                                $formattedItinerary[$dayKey] = $activities;
                                            }
                                            
                                            // If day 0 exists, rename it to Day 1 and shift others
                                            if(isset($formattedItinerary['Day 0'])) {
                                                $newItinerary = [];
                                                $dayCount = 1;
                                                foreach($formattedItinerary as $day => $activities) {
                                                    $newItinerary["Day " . $dayCount] = $activities;
                                                    $dayCount++;
                                                }
                                                $formattedItinerary = $newItinerary;
                                            }
                                        }
                                    @endphp
                                    
                                    @if(!empty($formattedItinerary))
                                        @foreach($formattedItinerary as $day => $dayActivities)
                                            <div class="day-item mb-3">
                                                <h6 class="fw-bold text-primary">{{ $day }}</h6>
                                                <ul class="list-group list-group-flush">
                                                    @if(is_array($dayActivities))
                                                        @foreach($dayActivities as $time => $activity)
                                                            <li class="list-group-item bg-transparent px-3 py-2 border-0">
                                                                <span class="fw-medium">{{ $time }}:</span> {{ $activity }}
                                                            </li>
                                                        @endforeach
                                                    @else
                                                        <li class="list-group-item bg-transparent px-3 py-2 border-0">
                                                            {{ $dayActivities }}
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-light border">
                                            <i class="fas fa-info-circle me-2 text-primary"></i>
                                            No detailed itinerary available for this package. Please contact us for more information.
                                        </div>
                                    @endif
                                </div>
                                <div class="alert alert-light border mt-3 small">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    This is the planned itinerary for this package. Specific activities and timing may vary based on weather conditions and local circumstances.
                                </div>
                            </div>
                            
                            <h5 class="border-bottom pb-2">Custom Requests</h5>
                            <p class="text-muted small mb-3">Let us know if you have any special requests or modifications to the itinerary</p>
                            <textarea name="custom_itinerary" class="form-control" rows="4" 
                                    placeholder="Examples: Additional stops, specific attractions you'd like to visit, dietary requirements, etc."></textarea>
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
                            <span>Number of Travelers:</span>
                            <span id="travelerCount" class="fw-bold">1</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Base Price:</span>
                        <span class="fw-bold">RM {{ number_format($package->price, 2) }}</span>
                    </div>
                    <div id="activities-price" class="d-flex justify-content-between mb-2">
                        <span>Additional Activities:</span>
                        <span>RM 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                        <span>Total Price:</span>
                        <span id="total-price" class="text-primary">RM {{ number_format($package->price, 2) }}</span>
                        <input type="hidden" name="total_price" id="totalPriceInput" value="{{ $package->price }}">
                    </div>
                    
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Select your travel date and optional activities to customize your booking.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Date option toggle
    const predefinedDateRadio = document.getElementById('predefined_date');
    const customDateRadio = document.getElementById('custom_date');
    const predefinedDatesContainer = document.getElementById('predefined_dates_container');
    const customDateContainer = document.getElementById('custom_date_container');
    const availableDateSelect = document.getElementById('available_date_select');
    const customDateInput = document.getElementById('custom_date_input');
    
    // Set initial state
    if (predefinedDateRadio.checked) {
        predefinedDatesContainer.classList.remove('d-none');
        customDateContainer.classList.add('d-none');
        availableDateSelect.setAttribute('required', '');
        customDateInput.removeAttribute('required');
    } else if (customDateRadio.checked) {
        predefinedDatesContainer.classList.add('d-none');
        customDateContainer.classList.remove('d-none');
        availableDateSelect.removeAttribute('required');
        customDateInput.setAttribute('required', '');
    }
    
    predefinedDateRadio.addEventListener('change', function() {
        if (this.checked) {
            predefinedDatesContainer.classList.remove('d-none');
            customDateContainer.classList.add('d-none');
            availableDateSelect.setAttribute('required', '');
            customDateInput.removeAttribute('required');
        }
    });
    
    customDateRadio.addEventListener('change', function() {
        if (this.checked) {
            predefinedDatesContainer.classList.add('d-none');
            customDateContainer.classList.remove('d-none');
            availableDateSelect.removeAttribute('required');
            customDateInput.setAttribute('required', '');
        }
    });

    // Initialize datepicker for custom date
    $(document).ready(function() {
        console.log('Initializing datepicker');
        $('#custom_date_input').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            startDate: 'today',
            todayHighlight: true
        });
        
        // Open datepicker when clicking on the calendar icon
        $('.input-group-text').click(function() {
            $('#custom_date_input').datepicker('show');
        });
    });
</script>
@endpush
@stack('scripts')
@endsection
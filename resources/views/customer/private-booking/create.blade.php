@extends('layout.layout')

@section('title', 'Private Booking')

@section('content')
<div class="container py-4">
 
    
    <div class="row">
        
        <div class="col-md-8">
            
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                <div class="col-12">
            <h1 class="fw">Booking Form for {{ $package->name }}</h1>
            <p class="text-muted">Complete the form below to book your travel experience.</p>
        </div>
                    <!-- Inside the form tag, add the hidden input for total_price -->
                    <form method="POST" action="{{ route('private-booking.store', $package->id) }}" id="bookingForm">
                        @csrf
                        <input type="hidden" name="total_price" id="total_price_input" value="{{ $package->price }}">
                        <input type="hidden" name="additional_price" id="additional_price_input" value="0">
                        
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
                   
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="date_option" id="customDate" value="custom">
                                <label class="form-check-label" for="customDate">
                                    Request a custom date
                                </label>
                            </div>
                            
                            <div id="custom_date_container" class="mb-3 d-none">
                                <input type="text" class="form-control" id="custom_date_input" name="custom_date" 
                                       placeholder="Select your preferred date" 
                                       data-provide="datepicker"
                                       data-date-format="dd/mm/yyyy"
                                       data-date-start-date="+14d"
                                       autocomplete="off">
                                <small class="form-text text-muted">Custom dates are subject to availability confirmation</small>
                            </div>
                        </div>
                        
                        <!-- Travelers Information -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-users me-2"></i>Contact Information</h4>
                            <p class="text-muted small mb-3">Please provide your contact details</p>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone ?? '' }}" readonly>
                                </div>
                            </div>
                            
                            <h5 class="mt-4 mb-3">Travelers Information</h5>
                            <div id="participants-container">
                                <!-- Participants will be added dynamically via JavaScript -->
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary mt-2" id="addParticipant">
                                <i class="fas fa-plus"></i> Add Traveler
                            </button>

                            
                        </div>

                        <!-- Additional Activities -->
                        @php
                            $packageActivities = is_string($package->activities) 
                                ? json_decode($package->activities, true) 
                                : $package->activities;
                        @endphp

                        @if(is_array($packageActivities) && count($packageActivities) > 0)
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-hiking me-2"></i>Additional Activities</h4>
                            <p class="text-muted small mb-3">Enhance your experience with these optional activities</p>
                            
                            <div class="row g-3">
                                @foreach($packageActivities as $index => $activity)
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" name="activity_selected[]" value="{{ $index }}" 
                                                       class="form-check-input activity-main-checkbox" 
                                                       id="activity{{ $index }}"
                                                       data-price="{{ $activity['price'] ?? 0 }}"
                                                       data-activity-id="{{ $index }}">
                                                <label class="form-check-label" for="activity{{ $index }}">
                                                    <span class="fw-bold">{{ $activity['name'] ?? 'Activity '.($index+1) }}</span>
                                                    <span class="d-block">RM {{ number_format($activity['price'] ?? 0, 2) }} per person</span>
                                                </label>
                                            </div>
                                            @if(isset($activity['description']))
                                                <p class="mt-2 small text-muted">{{ $activity['description'] }}</p>
                                            @endif
                                        
                                        <!-- Add these hidden fields to store activity data -->
                                        <input type="hidden" name="activities[{{ $index }}][name]" value="{{ $activity['name'] ?? 'Activity '.($index+1) }}">
                                        <input type="hidden" name="activities[{{ $index }}][price]" value="{{ $activity['price'] ?? 0 }}">
                                        @if(isset($activity['description']))
                                            <input type="hidden" name="activities[{{ $index }}][description]" value="{{ $activity['description'] }}">
                                        @endif
                                        
                                        <!-- Participant selection (initially hidden) -->
                                        <div class="mt-3 participant-selection d-none" id="participants-for-activity-{{ $index }}">
                                            <p class="mb-2 small fw-bold">Select participants for this activity:</p>
                                            <div class="participant-checkboxes">
                                                <!-- Will be populated dynamically via JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Custom Itinerary -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h4 class="mb-3"><i class="fas fa-map-marked-alt me-2"></i>Customize Your Itinerary</h4>
                            <p class="text-muted small mb-3">Review the package itinerary and add your customizations</p>
                            
                            <!-- Replace the customization section with this simpler approach -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Package Itinerary</h5>
                                <div class="timeline">
                                    @php
                                        $packageItinerary = is_string($package->itinerary) 
                                            ? json_decode($package->itinerary, true) 
                                            : $package->itinerary;
                                    @endphp
                                    
                                    @if(is_array($packageItinerary) && !empty($packageItinerary))
                                        @foreach($packageItinerary as $dayIndex => $dayActivities)
                                            <div class="day-item mb-4 border-bottom pb-3">
                                                <h6 class="fw-bold">Day {{ $dayIndex + 1 }}</h6>
                                                <div class="itinerary-container mb-3" style="max-height: 150px; overflow-y: auto; padding-right: 5px;">
                                                    @if(is_array($dayActivities))
                                                        @foreach($dayActivities as $time => $activity)
                                                            <div class="d-flex mb-2">
                                                                <div class="text-muted me-3" style="min-width: 70px;">{{ $time }}</div>
                                                                <div>{{ $activity }}</div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        @php
                                                            $lines = explode("\n", $dayActivities);
                                                        @endphp
                                                        @foreach($lines as $line)
                                                            <div class="mb-2">{{ $line }}</div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                
                                                <!-- Direct customization for this day -->
                                                <div class="mt-3">
                                                    <label class="form-label">Customize Day {{ $dayIndex + 1 }} (Optional)</label>
                                                    <textarea class="form-control"
                                                              name="custom_days[{{ $dayIndex }}][custom_activities]"
                                                              rows="5"
                                                              placeholder="Describe your preferred activities for Day {{ $dayIndex + 1 }}...">
@if(is_array($dayActivities))
@foreach($dayActivities as $time => $activity){{ $time }} - {{ $activity }}
@endforeach
@else{{ $dayActivities }}@endif</textarea>
                                                    <input type="hidden" name="custom_days[{{ $dayIndex }}][day_number]" value="{{ $dayIndex + 1 }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No itinerary details available.</p>
                                    @endif
                                </div>
                            </div>
                            
                            
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Continue to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Booking Summary -->
        <div class="col-md-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Booking Summary</h4>
                </div>
                <div class="card-body p-4">
                    <div class="package-details mb-3">
                        <h5 class="mb-3">{{ $package->name }}</h5>
                        <p class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $package->country ?? $package->location }}</p>
                        <p class="mb-3"><i class="fas fa-clock me-2 text-primary"></i>{{ $package->days ?? $package->duration }} Days</p>
                    
                    </div>
                    
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
                    
                    <!-- Activity breakdown will be inserted here dynamically -->
                    <div id="activity-breakdown"></div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Additional Activities:</span>
                        <span id="additionalPrice">RM 0.00</span>
                        <input type="hidden" name="additional_price" id="additional_price_input" value="0">
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                        <span>Total Price:</span>
                        <span id="totalPrice">RM {{ number_format($package->price, 2) }}</span>
                        <input type="hidden" name="total_price" id="total_price_input" value="{{ $package->price }}">
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
document.addEventListener('DOMContentLoaded', function() {
    // ===== CONSTANTS AND VARIABLES =====
    const today = new Date();
    const basePrice = parseFloat({{ $package->price }});
    const childDiscount = 0.5; // 50% discount for children
    let participantCount = 0;
    
    // ===== INITIALIZATION FUNCTIONS =====
    
    // Initialize date picker
    function initDatePicker() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            startDate: '+1d',
            autoclose: true
        });
    }
    
    // Initialize date option toggle
    function initDateOptionToggle() {
        $('input[name="date_option"]').change(function() {
            const isPredefined = $(this).val() === 'predefined';
            $('#predefined_dates_container').toggleClass('d-none', !isPredefined);
            $('#custom_date_container').toggleClass('d-none', isPredefined);
            $('#available_date_select').prop('required', isPredefined);
            $('#custom_date_input').prop('required', !isPredefined);
        });
    }
    
    // ===== PARTICIPANT MANAGEMENT =====
    
    // Add participant
    function addParticipant() {
        participantCount++;
        const participantId = Date.now();
        const newParticipant = `
            <div class="participant-row mb-3 p-3 border rounded bg-white" id="participant-${participantId}">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Traveler #${participantCount}</span>
                    ${participantCount > 1 ? 
                        `<button type="button" class="btn btn-sm btn-outline-danger remove-participant" data-id="${participantId}">
                            <i class="fas fa-times"></i> Remove
                        </button>` : ''}
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="participants[${participantId}][name]" class="form-control" required>
                    </div>

                   

                    <div class="col-md-6">
                    <label class="form-label">IC Number</label>
                    <input type="text" name="participants[${participantId}][ic_number]" 
                           class="form-control ic-number" 
                           pattern="[0-9]{12}"
                           maxlength="12"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           onchange="updateTravelerAge(${participantId}, this.value)"
                           required>
                    <small class="form-text text-muted">Format: 000000-00-0000</small>
                </div>



                    <div class="col-md-6">
                      
                        <input type="hidden" name="participants[${participantId}][type]" id="category-${participantId}">
                    </div>
                </div>
            </div>
        `;
        $('#participants-container').append(newParticipant);
        updateTravelerCount();
        updateTotalPrice();
        
        // Update participant checkboxes for all selected activities
        $('.activity-main-checkbox:checked').each(function() {
            updateParticipantCheckboxes($(this).data('activity-id'));
        });
    }
    
    // Remove participant
    function removeParticipant(participantId) {
        $(`#participant-${participantId}`).remove();
        participantCount--;
        updateTravelerCount();
        updateTotalPrice();
        
        // Update participant checkboxes for all selected activities
        $('.activity-main-checkbox:checked').each(function() {
            updateParticipantCheckboxes($(this).data('activity-id'));
        });
    }
    
    window.updateTravelerAge = function(participantId, icNumber) {
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

        // Determine category and update DOM
        let category;
        if (age < 2) {
            category = 'infant';
            $(`#age-category-${participantId}`).val('Infant (0-1 years)');
        } else if (age >= 2 && age < 12) {
            category = 'child';
            $(`#age-category-${participantId}`).val('Child (2-11 years)');
        } else {
            category = 'adult';
            $(`#age-category-${participantId}`).val('Adult (12+ years)');
        }

        // Update hidden input
        $(`#category-${participantId}`).val(category);

        // Update counts and price
        updateTravelerCount();
        updateTotalPrice();
    }
};
    
    // ===== PRICE CALCULATION FUNCTIONS =====
    
    // Update traveler count
    function updateTravelerCount() {
        let adults = 0;
        let children = 0;
        let infants = 0;
        
        // Count each type
        $('[id^=category-]').each(function() {
            const category = $(this).val();
            if (category === 'adult') adults++;
            else if (category === 'child') children++;
            else if (category === 'infant') infants++;
        });
        
        // Update display
        $('#adultsCount').text(adults);
        $('#childrenCount').text(children);
        $('#infantsCount').text(infants);
    }
    
    // Calculate base price based on participants
    function calculateBasePrice() {
        const adults = parseInt($('#adultsCount').text()) || 0;
        const children = parseInt($('#childrenCount').text()) || 0;
        
        // Calculate base package price
        // Adults pay full price, children get discount
        return basePrice * adults + (basePrice * children * (1 - childDiscount));
    }
    
    // Calculate additional price from activities
    function calculateAdditionalPrice() {
        let additionalPrice = 0;
        $('#activity-breakdown').empty();
        
        // Calculate price based on selected activities and participants
        $('.activity-main-checkbox:checked').each(function() {
            const activityId = $(this).data('activity-id');
            const activityPrice = parseFloat($(this).data('price'));
            const activityName = $(this).closest('.card-body').find('.fw-bold').text().trim();
            
            // Count selected participants for this activity
            const selectedParticipants = $(`#participants-for-activity-${activityId} input:checked`).length;
            
            if (selectedParticipants > 0) {
                const activityTotalPrice = activityPrice * selectedParticipants;
                additionalPrice += activityTotalPrice;
                
                // Add to the activity breakdown
                $('#activity-breakdown').append(`
                    <div class="d-flex justify-content-between mb-2">
                        <span>${activityName} (${selectedParticipants} travelers):</span>
                        <span>RM ${activityTotalPrice.toFixed(2)}</span>
                    </div>
                `);
            }
        });
        
        return additionalPrice;
    }
    
    // Update total price display and hidden inputs
    function updateTotalPrice() {
        const basePrice = calculateBasePrice();
        const additionalPrice = calculateAdditionalPrice();
        const totalPrice = basePrice + additionalPrice;
        
        // Update display and hidden inputs
        $('#additionalPrice').text(`RM ${additionalPrice.toFixed(2)}`);
        $('#totalPrice').text(`RM ${totalPrice.toFixed(2)}`);
        $('#total_price_input').val(totalPrice.toFixed(2));
        $('#additional_price_input').val(additionalPrice.toFixed(2));
    }
    
    // Event listeners for price updates
    $(document).on('change', '.activity-main-checkbox', function() {
        const activityId = $(this).data('activity-id');
        $(`#participants-for-activity-${activityId}`).toggleClass('d-none', !$(this).is(':checked'));
        updateTotalPrice();
    });
    
    $(document).on('change', '.participant-checkbox', function() {
        updateTotalPrice();
    });
    
    // ===== ACTIVITY MANAGEMENT =====
    
    // Update participant checkboxes for activities
    function updateParticipantCheckboxes(activityId) {
        const container = $(`#participants-for-activity-${activityId} .participant-checkboxes`);
        container.empty();
        
        // Add a checkbox for each participant
        $('.participant-row').each(function() {
            const participantId = $(this).attr('id').replace('participant-', '');
            const participantName = $(this).find('input[name^="participants"][name$="[name]"]').val() || `Traveler #${participantId}`;
            const participantType = $(`#category-${participantId}`).val();
            
            const checkbox = `
                <div class="form-check mb-2">
                    <input class="form-check-input participant-activity-checkbox" 
                           type="checkbox" 
                           id="participant-${participantId}-activity-${activityId}" 
                           name="activity_participants[${activityId}][]" 
                           value="${participantId}"
                           data-type="${participantType}"
                           checked>
                    <label class="form-check-label" for="participant-${participantId}-activity-${activityId}">
                        ${participantName} <small class="text-muted">(${participantType})</small>
                    </label>
                </div>
            `;
            container.append(checkbox);
        });
        
        // Bind change event to update price
        container.find('.participant-activity-checkbox').off('change').on('change', function() {
            updateTotalPrice();
        });
    }
    
    // ===== CUSTOM ITINERARY FUNCTIONS =====
    
    // Setup custom itinerary handling
    function setupCustomItinerary() {
        // Get all custom itinerary textareas
        const customDayTextareas = $('textarea[name^="custom_days"][name$="[custom_activities]"]');
        
        // Add event listener to each textarea to track changes
        customDayTextareas.each(function() {
            $(this).on('change keyup', function() {
                const textareaValue = $(this).val().trim();
                const dayElement = $(this).closest('.day-item');
                
                // Highlight the day item if it has custom content
                if (textareaValue.length > 0) {
                    dayElement.addClass('border-primary');
                } else {
                    dayElement.removeClass('border-primary');
                }
            });
        });
        
        // Add helper text to explain the format
        customDayTextareas.each(function() {
            const helpText = $('<small class="form-text text-muted">Format: "Time: Activity" on each line (e.g., "08:00: Breakfast at hotel")</small>');
            $(this).after(helpText);
        });
    }
    
    // Process custom itinerary data before form submission
    function processCustomItineraryData() {
        // Get all custom itinerary textareas
        const customDayTextareas = $('textarea[name^="custom_days"][name$="[custom_activities]"]');
        
        // Process each textarea with content
        customDayTextareas.each(function() {
            const textareaValue = $(this).val().trim();
            
            if (textareaValue.length > 0) {
                // Get the day index from the name attribute
                const nameAttr = $(this).attr('name');
                const dayIndexMatch = nameAttr.match(/custom_days\[(\d+)\]/);
                
                if (dayIndexMatch && dayIndexMatch[1]) {
                    const dayIndex = dayIndexMatch[1];
                    
                    // Parse the textarea content into a structured format
                    const lines = textareaValue.split('\n');
                    const customActivities = {};
                    
                    lines.forEach(line => {
                        // Try to extract time and activity
                        const timeSeparatorIndex = line.indexOf(':');
                        
                        if (timeSeparatorIndex > 0) {
                            // If there's a colon, use it to separate time and activity
                            const time = line.substring(0, timeSeparatorIndex).trim();
                            const activity = line.substring(timeSeparatorIndex + 1).trim();
                            
                            if (time && activity) {
                                customActivities[time] = activity;
                            }
                        } else if (line.trim()) {
                            // If there's no colon but the line has content, use a default time
                            customActivities[`Activity ${Object.keys(customActivities).length + 1}`] = line.trim();
                        }
                    });
                    
                    // If we have activities, convert to JSON and store in a hidden field
                    if (Object.keys(customActivities).length > 0) {
                        // Create a hidden field to store the JSON data
                        const hiddenField = $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', `custom_days[${dayIndex}][custom_activities_json]`)
                            .val(JSON.stringify(customActivities));
                        
                        // Add the hidden field to the form
                        $(this).after(hiddenField);
                    }
                }
            }
        });
    }
    
    // ===== EVENT BINDINGS =====
    
    // Initialize all components
    function initializeComponents() {
        // Initialize date picker
        initDatePicker();
        
        // Initialize date option toggle
        initDateOptionToggle();
        
        // Clear any existing participants before initializing
        $('#participants-container').empty();
        participantCount = 0;
        
        // Initialize with one participant
        addParticipant();
        
        // Bind add participant button
        $('#addParticipant').off('click').on('click', function() {
            addParticipant();
        });
        
        // Bind remove participant button
        $(document).off('click', '.remove-participant').on('click', '.remove-participant', function() {
            const participantId = $(this).data('id');
            removeParticipant(participantId);
        });
        
        // Bind activity selection
        $('.activity-main-checkbox').off('change').on('change', function() {
            const activityId = $(this).data('activity-id');
            const isChecked = $(this).prop('checked');
            
            if (isChecked) {
                $(`#participants-for-activity-${activityId}`).removeClass('d-none');
                updateParticipantCheckboxes(activityId);
            } else {
                $(`#participants-for-activity-${activityId}`).addClass('d-none');
            }
            
            updateTotalPrice();
        });
        
        // Setup custom itinerary
        setupCustomItinerary();
        
        // Form submission handling
        $('#bookingForm').off('submit').on('submit', function(event) {
            // Check if at least one participant has been added
            if (participantCount === 0) {
                event.preventDefault();
                alert('Please add at least one traveler before proceeding.');
                return false;
            }
            
            // Check if there is at least one adult traveler
            const hasAdult = $('[id^=category-]').toArray().some(element => $(element).val() === 'adult');
            if (!hasAdult) {
                event.preventDefault();
                alert('At least one adult traveler (12+ years) is required for booking.');
                return false;
            }
            
            // Process custom itinerary data
            processCustomItineraryData();
            
            // Update the total price and additional price inputs before submission
            const totalPrice = calculateTotalPrice();
            const additionalPrice = calculateAdditionalPrice();
            $('#total_price_input').val(totalPrice);
            $('#additional_price_input').val(additionalPrice);
            
            // Disable submit button to prevent double submission
            $(this).find('button[type="submit"]').prop('disabled', true);
            
            return true;
        });
        
        // Remove any duplicate total_price inputs
        $('input[name="total_price"]:not(#total_price_input)').remove();
    }
    
    // Initialize everything
    initializeComponents();
});
</script>
@endsection


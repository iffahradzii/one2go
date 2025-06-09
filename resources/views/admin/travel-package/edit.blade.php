@extends('layout.layoutAdmin')
@section("title")
Admin Edit
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Travel Package</h1>
    
    <form action="{{ route('admin.travel-package.update', $package->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Package Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $package->name }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="country" class="form-label">Country</label>
                        <select name="country" class="form-control" required>
                            <option value="Indonesia" {{ $package->country == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                            <option value="Thailand" {{ $package->country == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                            <option value="Vietnam" {{ $package->country == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                            <option value="South Korea" {{ $package->country == 'South Korea' ? 'selected' : '' }}>South Korea</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price (RM)</label>
                        <input type="number" name="price" step="0.01" class="form-control" value="{{ $package->price }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="duration" class="form-label">Duration (Days)</label>
                        <input type="number" name="duration" class="form-control" value="{{ $package->duration ?? 1 }}" min="1" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ $package->description }}</textarea>
                </div>
                
                <!-- Add visibility toggle -->
                <!-- Make sure this is in your form, inside the Basic Information card -->
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ $package->is_visible ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_visible">Package Visibility (visible to customers)</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Main Image</label>
                    <input type="file" name="image" class="form-control">
                    <div class="mt-2">
                        <p>Current Image:</p>
                        <img src="{{ asset('storage/' . $package->image) }}" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Itinerary Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Itinerary</h5>
            </div>
            <div class="card-body">
                <div id="itinerary-container">
                    @php
                        $itineraryData = is_string($package->itinerary) ? json_decode($package->itinerary, true) : $package->itinerary;
                    @endphp
                    @foreach ($itineraryData as $index => $itinerary)
                        <div class="mb-3">
                            <label class="fw-bold">Day {{ $index + 1 }}</label>
                            <textarea name="itinerary[]" class="form-control mb-2" rows="3">{{ $itinerary }}</textarea>
                        </div>
                    @endforeach
                </div>
                <!-- Button is hidden via JavaScript -->
                <button type="button" id="add-itinerary" class="btn btn-secondary btn-sm">Add Itinerary Day</button>
            </div>
        </div>

        <!-- Package Details Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Package Details</h5>
            </div>
            <div class="card-body">
                <!-- Include -->
                <div class="mb-4">
                    <label class="form-label fw-bold">What's Included</label>
                    <div id="include-container">
                        @php
                            $includeData = is_string($package->include) ? json_decode($package->include, true) : $package->include;
                        @endphp
                        @foreach ($includeData as $index => $include)
                            <div class="input-group mb-2 include-item-{{ $index }}">
                                <textarea name="include[]" class="form-control" rows="2">{{ $include }}</textarea>
                                <button type="button" class="btn btn-danger delete-include">×</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-include" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-plus"></i> Add Include Item
                    </button>
                </div>

                <!-- Exclude -->
                <div class="mb-4">
                    <label class="form-label fw-bold">What's Excluded</label>
                    <div id="exclude-container">
                        @php
                            $excludeData = is_string($package->exclude) ? json_decode($package->exclude, true) : $package->exclude;
                        @endphp
                        @foreach ($excludeData as $index => $exclude)
                            <div class="input-group mb-2 exclude-item-{{ $index }}">
                                <textarea name="exclude[]" class="form-control" rows="2">{{ $exclude }}</textarea>
                                <button type="button" class="btn btn-danger delete-exclude">×</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-exclude" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-plus"></i> Add Exclude Item
                    </button>
                </div>
            </div>
        </div>

        <!-- Additional Activities Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Additional Activities</h5>
            </div>
            <div class="card-body">
                <div id="activities-container">
                    @php
                        $activitiesData = is_string($package->activities) ? json_decode($package->activities, true) : $package->activities;
                    @endphp
                    @if(!empty($activitiesData))
                        @foreach ($activitiesData as $activity)
                            <div class="activity-item mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="activities[]" class="form-control" placeholder="Activity Name" value="{{ $activity['name'] ?? '' }}">
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <span class="input-group-text">RM</span>
                                            <input type="number" name="activity_prices[]" class="form-control" step="0.01" placeholder="Price per person" value="{{ $activity['price'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-activity">×</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="activity-item mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="activities[]" class="form-control" placeholder="Activity Name">
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text">RM</span>
                                        <input type="number" name="activity_prices[]" class="form-control" step="0.01" placeholder="Price per person">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger remove-activity">×</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-activity" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-plus"></i> Add Activity
                </button>
            </div>
        </div>

        <!-- Available Dates Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Available Dates</h5>
            </div>
            <div class="card-body">
                <div id="available-dates-container">
                    @php
                        $availableDates = is_string($package->available_dates) ? json_decode($package->available_dates, true) : $package->available_dates;
                        $minDate = date('Y-m-d', strtotime('0 days'));
                    @endphp
                    @if(count($availableDates) > 0)
                        @foreach ($availableDates as $index => $date)
                            <div class="input-group mb-2 date-item-{{ $index }}">
                                <input type="date" name="available_dates[]" class="form-control" value="{{ $date }}" min="{{ date('Y-m-d', strtotime('+14 days')) }}">
                                <button type="button" class="btn btn-danger delete-date" data-id="{{ $index }}">×</button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" id="add-available-date" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-plus"></i> Add Available Date
                </button>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
            <a href="{{ route('admin.travel-package.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Package</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Make duration field readonly
    const durationField = document.querySelector('input[name="duration"]');
    if (durationField) {
        durationField.setAttribute('readonly', true);
        durationField.classList.add('bg-light');
    }

    // Prevent adding new itinerary items
    const addItineraryBtn = document.getElementById('add-itinerary');
    if (addItineraryBtn) {
        addItineraryBtn.style.display = 'none';
    }
    
    // Add activity - using one-time event listener to prevent duplicates
    const addActivityBtn = document.getElementById('add-activity');
    if (addActivityBtn && !addActivityBtn.hasAttribute('data-initialized')) {
        addActivityBtn.setAttribute('data-initialized', 'true');
        addActivityBtn.addEventListener('click', function() {
            const container = document.getElementById('activities-container');
            container.insertAdjacentHTML('beforeend', `
                <div class="activity-item mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="activities[]" class="form-control" placeholder="Activity Name">
                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" name="activity_prices[]" class="form-control" step="0.01" placeholder="Price per person">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger remove-activity">×</button>
                        </div>
                    </div>
                </div>
            `);
        });
    }

    // Add Include Item - using one-time event listener
    const includeBtn = document.getElementById('add-include');
    if (includeBtn && !includeBtn.hasAttribute('data-initialized')) {
        includeBtn.setAttribute('data-initialized', 'true');
        includeBtn.addEventListener('click', function () {
            const container = document.getElementById('include-container');
            container.insertAdjacentHTML('beforeend', `
                <div class="input-group mb-2">
                    <textarea name="include[]" class="form-control" rows="2" placeholder="Enter what's included"></textarea>
                    <button type="button" class="btn btn-danger delete-include">×</button>
                </div>
            `);
        });
    }

    // Add Exclude Item - using one-time event listener
    const excludeBtn = document.getElementById('add-exclude');
    if (excludeBtn && !excludeBtn.hasAttribute('data-initialized')) {
        excludeBtn.setAttribute('data-initialized', 'true');
        excludeBtn.addEventListener('click', function () {
            const container = document.getElementById('exclude-container');
            container.insertAdjacentHTML('beforeend', `
                <div class="input-group mb-2">
                    <textarea name="exclude[]" class="form-control" rows="2" placeholder="Enter what's excluded"></textarea>
                    <button type="button" class="btn btn-danger delete-exclude">×</button>
                </div>
            `);
        });
    }
    
    // Add Available Date - using one-time event listener
    const addDateBtn = document.getElementById('add-available-date');
    if (addDateBtn && !addDateBtn.hasAttribute('data-initialized')) {
        addDateBtn.setAttribute('data-initialized', 'true');
        addDateBtn.addEventListener('click', function () {
            const minDate = new Date();
            minDate.setDate(minDate.getDate() + 14); // Add 14 days to current date
            const minDateStr = minDate.toISOString().split('T')[0];
            
            const container = document.getElementById('available-dates-container');
            container.insertAdjacentHTML('beforeend', `
                <div class="input-group mb-2">
                    <input type="date" name="available_dates[]" class="form-control" min="${minDateStr}">
                    <button type="button" class="btn btn-danger delete-date">×</button>
                </div>
            `);
        });
    }

    // Delegated Delete Handlers - only set up once
    if (!document.body.hasAttribute('data-delete-handlers-initialized')) {
        document.body.setAttribute('data-delete-handlers-initialized', 'true');
        document.body.addEventListener('click', function (e) {
            if (e.target.classList.contains('delete-include') || e.target.classList.contains('delete-include-new')) {
                e.preventDefault();
                e.target.closest('.input-group').remove();
            }

            if (e.target.classList.contains('delete-exclude') || e.target.classList.contains('delete-exclude-new')) {
                e.preventDefault();
                e.target.closest('.input-group').remove();
            }

            if (e.target.classList.contains('delete-date') || e.target.classList.contains('delete-date-new')) {
                e.preventDefault();
                e.target.closest('.input-group').remove();
            }

            if (e.target.classList.contains('remove-activity')) {
                e.preventDefault();
                e.target.closest('.activity-item').remove();
            }
        });
    }
});
</script>
@include('partials.dynamic-fields-js')
@endsection
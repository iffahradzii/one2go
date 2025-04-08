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
                    @endphp
                    @foreach ($availableDates as $index => $date)
                        <div class="input-group mb-2 date-item-{{ $index }}">
                            <input type="date" name="available_dates[]" class="form-control" value="{{ $date }}">
                            <button type="button" class="btn btn-danger delete-date">×</button>
                        </div>
                    @endforeach
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
@include('partials.dynamic-fields-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make duration field readonly
    const durationField = document.querySelector('input[name="duration"]');
    if (durationField) {
        durationField.setAttribute('readonly', true);
        durationField.classList.add('bg-light');
    }
    
    // Add delete functionality for include items
    document.querySelectorAll('.delete-include').forEach((button, index) => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this include item?')) {
                deleteItem('include', {{ $package->id }}, index);
            }
        });
    });
    
    // Add delete functionality for exclude items
    document.querySelectorAll('.delete-exclude').forEach((button, index) => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this exclude item?')) {
                deleteItem('exclude', {{ $package->id }}, index);
            }
        });
    });
    
    // Add delete functionality for available dates
    document.querySelectorAll('.delete-date').forEach((button, index) => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this date?')) {
                deleteItem('date', {{ $package->id }}, index);
            }
        });
    });
    
    // Function to handle deletion via AJAX
    function deleteItem(type, packageId, index) {
        fetch(`/admin/travel-package/${packageId}/${type}/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the element from the DOM
                const container = document.querySelector(`.${type}-item-${index}`);
                if (container) {
                    container.remove();
                }
            } else {
                alert('Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the item');
        });
    }
    
    // Prevent adding new itinerary items
    const addItineraryBtn = document.getElementById('add-itinerary');
    if (addItineraryBtn) {
        addItineraryBtn.style.display = 'none';
    }
    
    // Add include item
    document.getElementById('add-include').addEventListener('click', function() {
        const container = document.getElementById('include-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <textarea name="include[]" class="form-control" rows="2" placeholder="Enter what's included"></textarea>
                <button type="button" class="btn btn-danger delete-include">×</button>
            </div>
        `);
    });
    
    // Add exclude item
    document.getElementById('add-exclude').addEventListener('click', function() {
        const container = document.getElementById('exclude-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <textarea name="exclude[]" class="form-control" rows="2" placeholder="Enter what's excluded"></textarea>
                <button type="button" class="btn btn-danger delete-exclude">×</button>
            </div>
        `);
    });
    
    // Add activity
    document.getElementById('add-activity').addEventListener('click', function() {
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
    
    // Add available date
    document.getElementById('add-available-date').addEventListener('click', function() {
        const container = document.getElementById('available-dates-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <input type="date" name="available_dates[]" class="form-control">
                <button type="button" class="btn btn-danger delete-date">×</button>
            </div>
        `);
    });
    
    // Remove activity
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-activity')) {
            e.target.closest('.activity-item').remove();
        }
    });
});
</script>
@endsection

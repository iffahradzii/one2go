@extends('layout.layoutAdmin')

@section('title', 'Create Travel Package')

@section('content')
<div class="container">
    <h1>Create Travel Package</h1>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('admin.travel-package.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">Basic Information</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name">Package Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="country">Country</label>
                    <select name="country" class="form-control" required>
                        <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                        <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                        <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                        <option value="South Korea" {{ old('country') == 'South Korea' ? 'selected' : '' }}>South Korea</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="price">Base Price</label>
                            <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="duration">Duration</label>
                            <input type="number" name="duration" id="duration" class="form-control" min="1" value="{{ old('duration') }}" placeholder="Number of days" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Itinerary Section -->
        <div class="card mb-4">
            <div class="card-header">Daily Itinerary</div>
            <div class="card-body">
                <div id="itinerary-days-container">
                    <!-- Dynamic itinerary days will be added here -->
                </div>
            </div>
        </div>

        <!-- Include/Exclude Section -->
        <div class="card mb-4">
            <div class="card-header">Package Details</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="include">Include</label>
                    <div id="include-container">
                        <textarea name="include[]" class="form-control mb-2" rows="2" placeholder="Include item">{{ old('include.0') }}</textarea>
                    </div>
                    <button type="button" id="add-include" class="btn btn-secondary btn-sm">Add Include</button>
                </div>

                <div class="form-group mb-3">
                    <label for="exclude">Exclude</label>
                    <div id="exclude-container">
                        <textarea name="exclude[]" class="form-control mb-2" rows="2" placeholder="Exclude item">{{ old('exclude.0') }}</textarea>
                    </div>
                    <button type="button" id="add-exclude" class="btn btn-secondary btn-sm">Add Exclude</button>
                </div>
            </div>
        </div>

        <!-- Additional Activities -->
        <div class="card mb-4">
            <div class="card-header">Additional Activities</div>
            <div class="card-body">
                <div id="activities-container">
                    <div class="activity-item mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="activities[]" class="form-control" placeholder="Activity Name" value="{{ old('activities.0') }}">
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">RM</span>
                                    <input type="number" name="activity_prices[]" class="form-control" step="0.01" placeholder="Price per person" value="{{ old('activity_prices.0') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-activity" class="btn btn-secondary btn-sm">Add Activity</button>
            </div>
        </div>

        <!-- Dates and Image -->
        <div class="card mb-4">
            <div class="card-header">Available Dates & Image</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="available_dates">Available Dates</label>
                    <div id="available-dates-container">
                        <input type="date" name="available_dates[]" class="form-control mb-2" value="{{ old('available_dates.0') }}">
                    </div>
                    <button type="button" id="add-available-date" class="btn btn-secondary btn-sm">Add Date</button>
                </div>

                <div class="form-group">
                    <label for="image">Main Image</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create Package</button>
    </form>
</div>

@endsection

@section('scripts')
<script>
// Make sure DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const durationInput = document.getElementById('duration');
    if (durationInput) {
        durationInput.addEventListener('change', function() {
            const days = parseInt(this.value) || 0;
            const container = document.getElementById('itinerary-days-container');
            container.innerHTML = '';
            
            for (let i = 1; i <= days; i++) {
                container.innerHTML += `
                    <div class="mb-3">
                        <label class="fw-bold">Day ${i}</label>
                        <textarea name="itinerary[]" class="form-control" rows="3" required
                            placeholder="Enter activities for Day ${i}"></textarea>
                    </div>
                `;
            }
        });
        
        // Trigger the change event if there's a value (for form validation failures)
        if (durationInput.value) {
            durationInput.dispatchEvent(new Event('change'));
        }
    }

    // Add Include Item
    document.getElementById('add-include').addEventListener('click', function() {
        const container = document.getElementById('include-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <textarea name="include[]" class="form-control" rows="2" placeholder="Include item"></textarea>
                <button type="button" class="btn btn-danger remove-item">×</button>
            </div>
        `);
    });

    // Add Exclude Item
    document.getElementById('add-exclude').addEventListener('click', function() {
        const container = document.getElementById('exclude-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <textarea name="exclude[]" class="form-control" rows="2" placeholder="Exclude item"></textarea>
                <button type="button" class="btn btn-danger remove-item">×</button>
            </div>
        `);
    });

    // Add Activity
    document.getElementById('add-activity').addEventListener('click', function() {
        const container = document.getElementById('activities-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="activity-item mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" name="activities[]" class="form-control" placeholder="Activity Name" required>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="number" name="activity_prices[]" class="form-control" step="0.01" placeholder="Price per person" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-item">Remove</button>
                    </div>
                </div>
            </div>
        `);
    });

    // Add Available Date
    document.getElementById('add-available-date').addEventListener('click', function() {
        const container = document.getElementById('available-dates-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <input type="date" name="available_dates[]" class="form-control">
                <button type="button" class="btn btn-danger remove-item">×</button>
            </div>
        `);
    });

    // Remove item functionality (for all removable items)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.input-group, .activity-item').remove();
        }
    });
});
</script>
@endsection

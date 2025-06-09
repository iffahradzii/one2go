@extends("layout.layout")
@section("title")
Package Details
@endsection

@section('content')
<div class="container py-5">
    <!-- Package Title -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">{{ $package->name }}</h1>
        <p class="text-muted">Plan your dream trip with an unforgettable experience in {{ $package->country }}.</p>
    </div>

    <!-- Package Details -->
    <div class="row">
        <!-- Package Image -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <img src="{{ asset('storage/' . $package->image) }}" class="card-img-top img-fluid" alt="{{ $package->name }}">
            </div>
        </div>

        <!-- Package Information -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4">
                <!-- Base Price -->
                <div class="mb-4">
                    <h3 class="text-success mb-2">Starting from RM{{ number_format($package->price, 2) }}</h3>
                    <p class="text-muted small">*Price varies based on tour type and group size</p>
                </div>

                <hr>

              <!-- Available Dates -->
<p><strong>Available Dates:</strong></p>
<div class="mb-4">
    @php
        use Carbon\Carbon;

        $today = Carbon::today();
        $futureDate = $today->copy()->addDays(14); // Date 14 days from now
        $rawDates = is_string($package->available_dates)
            ? json_decode($package->available_dates, true)
            : $package->available_dates;

        // Filter only dates at least 14 days in the future
        $validDates = collect($rawDates)->filter(function ($date) use ($futureDate) {
            return Carbon::parse($date)->greaterThanOrEqualTo($futureDate);
        });
    @endphp

    @if($validDates->isNotEmpty())
        @foreach($validDates as $date)
            <span class="badge bg-primary mb-1">{{ Carbon::parse($date)->format('d M') }}</span>
        @endforeach
    @else
        <div class="text-danger">No available dates at the moment.</div>
    @endif
</div>



                <!-- Overview Section -->
                <p><strong>Overview:</strong></p>
                <div class="card bg-light p-3 mb-4">
                    <p class="mb-0">{{ $package->description }}</p>
                </div>

                <!-- Highlights Section with all details -->
                <p><strong>Trip Highlights:</strong></p>
                <div class="card bg-light p-3 mb-4">
                    <!-- Itinerary Preview -->
                    <h6 class="mb-2">Daily Activities:</h6>
                    <ul class="mb-3">
                        @php
                            $itinerary = is_string($package->itinerary) ? json_decode($package->itinerary, true) : $package->itinerary;
                        @endphp
                        @foreach ($itinerary as $day => $activities)
                            <li><strong>{{ ucfirst($day+1) }}:</strong> {{ Str::limit($activities, 100) }}</li>
                        @endforeach
                    </ul>

                    <!-- Inclusions -->
                    <h6 class="mb-2">What's Included:</h6>
                    <ul class="mb-3">
                        @php
                            $include = is_string($package->include) ? json_decode($package->include, true) : $package->include;
                        @endphp
                        @foreach ($include as $item)
                            <li><i class="fas fa-check text-success me-2"></i>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <!-- Exclusions -->
                    <h6 class="mb-2">What's Not Included:</h6>
                    <ul class="mb-0">
                        @php
                            $exclude = is_string($package->exclude) ? json_decode($package->exclude, true) : $package->exclude;
                        @endphp
                        @foreach ($exclude as $item)
                            <li><i class="fas fa-times text-danger me-2"></i>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Booking Options -->
                <div class="mt-4">
                    <h4 class="mb-3">Choose Your Tour Type:</h4>
                    
                    @auth
                        <div class="d-grid gap-3">
                            <a href="{{ route('booking.create', $package->id) }}" 
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-users me-2"></i>Group Tour
                                <small class="d-block">Join other travelers</small>
                            </a>
                            
                            <a href="{{ route('private-booking.create', $package->id) }}" 
                               class="btn btn-success btn-lg">
                                <i class="fas fa-user-shield me-2"></i>Private Tour
                                <small class="d-block">Exclusive for your group</small>
                            </a>
                        </div>
                    @else
                        <div class="d-grid gap-3">
                            <a href="#" class="btn btn-primary btn-lg" onclick="showLoginAlert()">
                                <i class="fas fa-users me-2"></i>Group Tour
                                <small class="d-block">Join other travelers</small>
                            </a>
                            
                            <a href="#" class="btn btn-success btn-lg" onclick="showLoginAlert()">
                                <i class="fas fa-user-shield me-2"></i>Private Tour
                                <small class="d-block">Exclusive for your group</small>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showLoginAlert() {
        alert('Please log in to proceed with booking.');
        window.location.href = "{{ route('login') }}";
    }
</script>
@endsection

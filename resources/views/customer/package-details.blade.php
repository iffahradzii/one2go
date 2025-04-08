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
                <h3 class="text-success">Price: RM{{ number_format($package->price, 2) }}</h3>
                <hr>
                <p><strong>Available Dates:</strong></p>
                <div>
                    @foreach (json_decode($package->available_dates, true) as $date)
                        <span class="badge bg-primary mb-1">{{ \Carbon\Carbon::parse($date)->format('d M') }}</span>
                    @endforeach
                </div>
                <hr>

                <!-- Description Section -->
                <p><strong>Description:</strong></p>
                <div class="card bg-light p-3 mb-3">
                    <p class="mb-0">{{ $package->description }}</p>
                </div>

                <!-- Itinerary Section -->
                <p><strong>Itinerary:</strong></p>
                <div class="card bg-light p-3 mb-3">
                    <ul class="mb-0">
                        @foreach (json_decode($package->itinerary, true) as $itinerary)
                            <li>{{ $itinerary }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Include Section -->
                <p><strong>Include:</strong></p>
                <div class="card bg-light p-3 mb-3">
                    <ul class="mb-0">
                        @foreach (json_decode($package->include, true) as $include)
                            <li>{{ $include }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Exclude Section -->
                <p><strong>Exclude:</strong></p>
                <div class="card bg-light p-3 mb-3">
                    <ul class="mb-0">
                        @foreach (json_decode($package->exclude, true) as $exclude)
                            <li>{{ $exclude }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Book Now Button -->
                @auth
                    <a href="{{ route('booking.form', $package->id) }}" class="btn btn-success btn-lg w-100">Book Now</a>
                @else
                    <a href="#" class="btn btn-success btn-lg w-100" onclick="showLoginAlert()">Book Now</a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showLoginAlert() {
        alert('Please log in to proceed with booking.');
        window.location.href = "{{ route('login') }}"; // Redirect to the login page
    }
</script>
@endsection

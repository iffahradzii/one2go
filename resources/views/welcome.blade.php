@extends("layout.layout")
@section("title")
 One2GO
@endsection

@section("content")
<main>
    <!-- Hero Section -->
    <section class="hero" style="background-color: #0047AB; color: white; padding: 5rem 0;">
        <div class="container text-center">
            <h1 class="display-4">Welcome to Traveler</h1>
            <p class="lead">Your journey starts here. Explore new destinations and book your dream vacation!</p>

            @guest
                <!-- Button for logged-out users to sign up or log in to make bookings -->
                <a href="{{ route('login') }}" class="btn btn-danger btn-lg">Login to Book a Trip</a>
                @else
                <!-- Button for logged-in users to make a booking -->
                <a href="{{ url('/bookings') }}" class="btn btn-light btn-lg">Make a Booking</a>
            @endguest
        </div>
    </section>

    <!-- Features Section -->
    <section class="features py-5">
        <div class="container text-center">
            <h2>Why Choose Us?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box">
                        <h3>Personalized Tours</h3>
                        <p>We offer tailored travel experiences based on your interests.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <h3>Expert Guides</h3>
                        <p>Our experienced guides will make your journey unforgettable.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <h3>Affordable Packages</h3>
                        <p>Explore the world without breaking the bank. We have budget-friendly options.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Destinations Table -->
    <section class="popular-destinations py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">International Escapes: Get the Guides</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <!-- Thailand -->
            <div class="col">
                <a href="{{ url('/destination/thailand') }}" class="text-decoration-none text-dark">
                    <div class="card">
                        <img src="{{ asset('images/thailand.jpg') }}" class="card-img-top" alt="Thailand">
                        <div class="card-body text-center">
                            <h5 class="card-title">Bangkok</h5>
                            <p class="card-text">Thailand</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Indonesia -->
            <div class="col">
                <a href="{{ url('/destination/indonesia') }}" class="text-decoration-none text-dark">
                    <div class="card">
                        <img src="{{ asset('images/indonesia.jpg') }}" class="card-img-top" alt="Indonesia">
                        <div class="card-body text-center">
                            <h5 class="card-title">Bali</h5>
                            <p class="card-text">Indonesia</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Vietnam -->
            <div class="col">
                <a href="{{ url('/destination/vietnam') }}" class="text-decoration-none text-dark">
                    <div class="card">
                        <img src="{{ asset('images/vietnam.jpg') }}" class="card-img-top" alt="Vietnam">
                        <div class="card-body text-center">
                            <h5 class="card-title">Hanoi</h5>
                            <p class="card-text">Vietnam</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- South Korea -->
            <div class="col">
                <a href="{{ url('/destination/south-korea') }}" class="text-decoration-none text-dark">
                    <div class="card">
                        <img src="{{ asset('images/south-korea.jpg') }}" class="card-img-top" alt="South Korea">
                        <div class="card-body text-center">
                            <h5 class="card-title">Seoul</h5>
                            <p class="card-text">South Korea</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
</main>


<!-- Include Bootstrap JS (necessary for Bootstrap functionality like modals, tooltips, etc.) -->

<!-- Include your JS (if needed for additional functionality) -->
<script src="{{ asset('js/app.js') }}"></script>
@endsection

@extends("layout.layout")
@section("title")
One2GO
@endsection

@section("content")
<main>
    <!-- Hero Section -->
    <section class="hero text-white text-center d-flex align-items-center" style="background: linear-gradient(to bottom right, #0047AB, #007BFF); padding: 5rem 0;">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">Welcome to One2GO Traveler</h1>
            <p class="mb-5">Join our growing family of happy travelers enjoying unforgettable experiences!</p>
            
            @guest
                <a href="{{ route('login') }}" class="btn btn-danger btn-lg px-4 py-2">Login to Book a Trip</a>
            @else
                <!-- Updated Button -->
                <a href="#popular-destinations" class="btn btn-lg px-4 py-2" style="background-color: #FF5722; color: white;">Make a Booking</a>
                @endguest
        </div>
    </section>





    <!-- Features Section -->
    <section class="features py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-5" style="color: #0047AB;">Why Choose Us?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box p-4 shadow rounded" style="background-color: #F0F8FF;">
                        <i class="bi bi-geo-alt-fill mb-3" style="font-size: 2rem; color: #007BFF;"></i>
                        <h4>Personalized Tours</h4>
                        <p>We offer tailored travel experiences based on your interests.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box p-4 shadow rounded" style="background-color: #F0F8FF;">
                        <i class="bi bi-people-fill mb-3" style="font-size: 2rem; color: #007BFF;"></i>
                        <h4>Expert Guides</h4>
                        <p>Our experienced guides will make your journey unforgettable.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box p-4 shadow rounded" style="background-color: #F0F8FF;">
                        <i class="bi bi-wallet-fill mb-3" style="font-size: 2rem; color: #007BFF;"></i>
                        <h4>Affordable Packages</h4>
                        <p>Explore the world without breaking the bank. We have budget-friendly options.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Popular Destinations Section -->
    <!-- Add ID to Section -->
    <section id="popular-destinations" class="popular-destinations py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <h2 class="text-center mb-5" style="font-family: 'Poppins', sans-serif; color: #0047AB;">International Escapes: Get the Guides</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
               <!-- Thailand -->
                <div class="col">
                    <a href="{{ route('packages.thailand') }}" class="text-decoration-none">
                         <div class="card border-0 shadow-sm destination-card">
                            <div class="card-overlay">
                                <img src="{{ asset('images/thailand.jpg') }}" class="card-img-top rounded" alt="Indonesia">
                                <div class="overlay"></div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold" style="color: #0047AB;">Bangkok</h5>
                                <p class="card-text text-muted">Thailand</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Indonesia -->
                <div class="col">
                    <a href="{{ url('/packages/indonesia') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm destination-card">
                            <div class="card-overlay">
                                <img src="{{ asset('images/indonesia.jpg') }}" class="card-img-top rounded" alt="Indonesia">
                                <div class="overlay"></div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold" style="color: #0047AB;">Bali</h5>
                                <p class="card-text text-muted">Indonesia</p>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- Vietnam -->
                <div class="col">
                    <a href="{{ url('/packages/vietnam') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm destination-card">
                            <div class="card-overlay">
                                <img src="{{ asset('images/vietnam.jpg') }}" class="card-img-top rounded" alt="Vietnam">
                                <div class="overlay"></div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold" style="color: #0047AB;">Hanoi</h5>
                                <p class="card-text text-muted">Vietnam</p>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- South Korea -->
                <div class="col">
                    <a href="{{ url('/packages/southkorea') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm destination-card">
                            <div class="card-overlay">
                                <img src="{{ asset('images/south-korea.jpg') }}" class="card-img-top rounded" alt="South Korea">
                                <div class="overlay"></div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold" style="color: #0047AB;">Seoul</h5>
                                <p class="card-text text-muted">South Korea</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Include Custom CSS -->
<style>
    .destination-card .card-overlay img {
        transition: transform 0.3s ease-in-out;
    }

    .destination-card:hover .card-overlay img {
        transform: scale(1.1);
    }

    .destination-card .overlay {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, 0.3);
        transition: opacity 0.3s ease-in-out;
        opacity: 0;
        border-radius: 0.5rem;
    }

   
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const counters = document.querySelectorAll(".counter");
        counters.forEach((counter) => {
            const updateCounter = () => {
                const target = +counter.getAttribute("data-target");
                const count = +counter.innerText;
                const increment = target / 200; // Adjust for speed

                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCounter, 20); // Speed of counter
                } else {
                    counter.innerText = target;
                }
            };
            updateCounter();
        });
    });
</script>
@endsection

@extends('layout.layout')

@section('title', 'About Us')

@section('content')

<!-- Hero Section -->
<div class="bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="display-4 fw-bold">About One 2 Go</h1>
                <p class="lead">Creating unforgettable journeys across Southeast Asia since 2010.</p>
            </div>
            <div class="col-md-5 text-end">
                <img src="{{ asset('images/one2go_1.jpg') }}" alt="One 2 Go Travel" class="img-fluid rounded shadow" style="max-height: 300px; object-fit: cover;">
            </div>
        </div>
    </div>
</div>

<!-- Company Story -->
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-md-6 mb-4 mb-md-0">
            <img src="{{ asset('images/one2go_1.jpg') }}" alt="Our Story" class="img-fluid rounded shadow-lg" style="width: 100%; max-height: 400px; object-fit: cover;">
        </div>
        <div class="col-md-6">
            <div class="ps-md-4">
                <h2 class="fw-bold mb-3 text-primary">Who We Are</h2>
                <p class="lead">
                    Based in Kulim, Kedah, One 2 Go Travel & Tour is your trusted travel partner for affordable and exciting tours to Thailand, Vietnam, Indonesia, and South Korea.
                </p>
                <p>
                    Since 2010, we’ve proudly helped thousands of travelers experience new cultures, enjoy breathtaking views, and create memories that last a lifetime — all without breaking the bank.
                </p>
                <p>
                    We believe travel should be easy, affordable, and enriching. Our team works hard to ensure every detail of your trip is taken care of — from itinerary planning to on-the-ground support.
                </p>
            </div>
        </div>
    </div>

    <!-- Core Values -->
    <h2 class="text-center fw-bold mb-4 text-primary">Our Core Values</h2>
    <div class="row text-center g-4 mb-5">
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white h-100 border-top border-primary border-3">
                <i class="fas fa-globe-asia fa-3x text-primary mb-3"></i>
                <h4>Affordability</h4>
                <p class="text-muted">We believe everyone deserves to explore the world, regardless of budget.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white h-100 border-top border-primary border-3">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h4>Trust</h4>
                <p class="text-muted">We build long-term relationships with our clients based on honesty and reliability.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white h-100 border-top border-primary border-3">
                <i class="fas fa-suitcase-rolling fa-3x text-primary mb-3"></i>
                <h4>Experience</h4>
                <p class="text-muted">We craft smooth, memorable travel experiences tailored to your needs.</p>
            </div>
        </div>
    </div>

  

    <!-- Contact CTA -->
    <div class="bg-primary text-white p-5 rounded shadow text-center">
        <h2 class="mb-3">Let’s Start Planning Your Trip!</h2>
        <p class="lead mb-4">We’d love to help you explore Southeast Asia.</p>
        <div class="d-flex flex-column align-items-center gap-2">
            <a href="https://www.facebook.com/one2gotravel/" target="_blank" class="btn btn-light btn-lg px-4">
                <i class="fab fa-facebook-f me-2"></i>Follow us on Facebook
            </a>
            <a href="mailto:one2gotravel@yahoo.com" class="btn btn-outline-light btn-lg px-4">
                <i class="fas fa-envelope me-2"></i>Email: one2gotravel@yahoo.com
            </a>
            <a href="https://wa.me/60194725587" target="_blank" class="btn btn-success btn-lg px-4">
                <i class="fab fa-whatsapp me-2"></i>WhatsApp Us: 019-472 5587
            </a>
        </div>
    </div>
</div>

@endsection

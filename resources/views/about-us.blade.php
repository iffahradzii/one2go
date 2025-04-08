@extends('layout.layout')

@section('title', 'About Us')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-4">About Us</h1>
    <div class="row align-items-center mb-4">
        <!-- Image Section -->
        <div class="col-md-6">
            <img src="{{ asset('images/one2go_1.jpg') }}" alt="About Us Image" class="img-fluid rounded shadow-lg" style="max-height: 400px; width: 100%; object-fit: cover;">
        </div>
        <!-- Text Section -->
        <div class="col-md-6">
            <h2>Who We Are</h2>
            <p>
                Welcome to One 2 Go Travel & Tour! Based in Kulim, Kedah, we specialize in providing affordable, 
                high-quality travel experiences to destinations across Southeast Asia, including Thailand, Vietnam, 
                Indonesia, and South Korea. Our mission is to make international travel accessible for everyone.
            </p>
            <h2>Our Mission</h2>
            <p>
                To deliver memorable and budget-friendly travel experiences, connecting our clients to the beauty 
                and diversity of the world around them. We aim to make every journey an adventure worth cherishing.
            </p>
        </div>
    </div>

    <!-- Values Section -->
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white">
                <h4>Integrity</h4>
                <p class="text-muted">We prioritize honesty and transparency in all our dealings with clients and partners.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white">
                <h4>Adventure</h4>
                <p class="text-muted">We aim to inspire a spirit of adventure in every journey we create.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white">
                <h4>Excellence</h4>
                <p class="text-muted">We strive to exceed expectations and deliver top-notch services every time.</p>
            </div>
        </div>
    </div>
</div>

<!-- Team Section -->
<div id="team" class="mb-5">
        <h2 class="text-center mb-4">Meet the Team</h2>
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="{{ asset('images/cooperate1.jpg') }}" alt="Team Member 1" class="img-fluid rounded-circle shadow mb-3" width="150">
                <h5>John Doe</h5>
                <p class="text-muted">Founder & CEO</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ asset('images/cooperate2.jpg') }}" alt="Team Member 2" class="img-fluid rounded-circle shadow mb-3" width="150">
                <h5>Jane Smith</h5>
                <p class="text-muted">Travel Consultant</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ asset('images/cooperate1.jpg') }}" alt="Team Member 3" class="img-fluid rounded-circle shadow mb-3" width="150">
                <h5>Michael Lee</h5>
                <p class="text-muted">Customer Support</p>
            </div>
        </div>
    </div>
@endsection
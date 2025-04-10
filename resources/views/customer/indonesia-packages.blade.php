@extends("layout.layout")
@section("title")
Indonesia
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section for Indonesia -->
    <div class="header-section" 
         style="background-image: url('{{ asset('images/indonesia.jpg') }}'); 
                background-size: cover; 
                background-position: center; 
                height: 300px; 
                color: white; 
                display: flex; 
                flex-direction: column; 
                justify-content: center; 
                align-items: center; 
                position: relative;">

        <!-- Blurry Background Overlay -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); z-index: 1;"></div>

        <!-- Content -->
        <div class="text-center py-4 px-3 rounded" style="z-index: 2;">
            <h1 class="display-4 fw-bold">Explore Indonesia</h1>
            <p class="lead">Discover the beauty of Indonesia with our curated travel packages. From pristine beaches to rich cultural heritage, experience it all with us at affordable prices.</p>
        </div>
    </div>
</div>

<div class="container py-4">
    <h2 class="text-center mb-5">Indonesia Travel Packages</h2>
    <div class="row g-4">
        @foreach ($packages as $package)
            <div class="col-md-4 d-flex">
                <div class="card shadow-sm w-100">
                    <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color: #f8f9fa;">
                        <img src="{{ asset('storage/' . $package->image) }}" class="card-img-top" alt="{{ $package->name }}" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center">{{ $package->name }}</h5>
                        <p class="card-text text-center text-muted">Price: RM{{ number_format($package->price, 2) }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('package.details', ['country' => strtolower($package->country), 'id' => $package->id]) }}" 
                               class="btn btn-outline-primary w-100 mb-2">View Details</a>
                            
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

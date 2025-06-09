@extends('layout.layoutAdmin')

@section('title', 'Review Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-star text-warning me-2"></i>Review Details</h2>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Reviews
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Review Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Customer Review</h5>
                                <div>
                                    <span class="badge bg-primary">ID: {{ $review->id }}</span>
                                    <span class="badge bg-secondary ms-2">{{ $review->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $review->user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $review->user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h6>Package:</h6>
                                <p class="mb-0">
                                    <strong>{{ $review->booking ? $review->booking->travelPackage->name : $review->privateBooking->travelPackage->name }}</strong>
                                    @if($review->privateBooking)
                                        <span class="badge bg-info ms-2">Private Booking</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <h6>Rating:</h6>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star fa-lg {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2 fw-bold">{{ $review->rating }}/5</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h6>Review:</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $review->review_text }}
                                </div>
                            </div>
                            
                            @if($review->photo_path)
                                <div class="mb-3">
                                    <h6>Photo:</h6>
                                    <img src="{{ asset('storage/' . $review->photo_path) }}" alt="Review Photo" class="img-fluid rounded" style="max-height: 300px;">
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Reply Form -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                @if($review->reply)
                                    <i class="fas fa-reply text-success me-2"></i>Edit Reply
                                @else
                                    <i class="fas fa-reply text-primary me-2"></i>Add Reply
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="reply_text" class="form-label">Your Reply:</label>
                                    <textarea class="form-control @error('reply_text') is-invalid @enderror" id="reply_text" name="reply_text" rows="4" required>{{ $review->reply ? $review->reply->reply_text : old('reply_text') }}</textarea>
                                    @error('reply_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        {{ $review->reply ? 'Update Reply' : 'Submit Reply' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Customer Info Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Name:</span>
                                    <span class="fw-bold">{{ $review->user->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Email:</span>
                                    <span>{{ $review->user->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Joined:</span>
                                    <span>{{ $review->user->created_at->format('d M Y') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Package Info Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-suitcase me-2"></i>Package Information</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $package = $review->booking ? $review->booking->travelPackage : $review->privateBooking->travelPackage;
                            @endphp
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Package:</span>
                                    <span class="fw-bold">{{ $package->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Location:</span>
                                    <span>{{ $package->location }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Duration:</span>
                                    <span>{{ $package->duration }} days</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Price:</span>
                                    <span>RM {{ number_format($package->price, 2) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
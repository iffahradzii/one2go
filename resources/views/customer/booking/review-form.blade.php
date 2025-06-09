@extends('layout.layout')

@section('title', 'Leave a Review')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="fw-bold text-primary">Share Your Experience</h1>
            <p class="text-muted lead">Tell us about your journey with {{ $booking->travelPackage->name }}</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-gradient text-white bg-primary py-3">
                    <h4 class="mb-0"><i class="fas fa-star me-2"></i>Your Review</h4>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ $isPrivate ? route('customer.review.store.private', $booking->id) : route('customer.review.store', $booking->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">How would you rate your experience?</label>
                            <div class="rating-stars p-3 bg-light rounded mb-3">
                                <div class="d-flex justify-content-center">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <div class="form-check form-check-inline mx-2">
                                            <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" required>
                                            <label class="form-check-label" for="rating{{ $i }}">
                                                <span class="fs-4">{{ $i }}</span> <i class="fas fa-star text-warning"></i>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="review_text" class="form-label fw-bold">Tell us about your experience</label>
                            <textarea class="form-control" id="review_text" name="review_text" rows="5" 
                                placeholder="What did you enjoy most? Any suggestions for improvement?" required></textarea>
                            <div class="form-text">Minimum 10 characters, maximum 1000 characters</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="review_photo" class="form-label fw-bold">
                                <i class="fas fa-camera me-2"></i>Share a photo from your trip
                            </label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="review_photo" name="review_photo" accept="image/*">
                                <label class="input-group-text" for="review_photo">Browse</label>
                            </div>
                            <div class="form-text text-muted">Optional. Max size: 2MB. Supported formats: JPEG, PNG, GIF</div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Submit Review
                            </button>
                            <a href="{{ route('my-booking') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to My Bookings
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
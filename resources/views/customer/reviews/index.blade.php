@extends('layout.layout')

@section('title', 'Reviews & Ratings')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center fw-bold">Reviews & Ratings</h1>
            <div class="text-center text-muted">See what others are saying about our trips</div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="d-flex justify-content-center mb-4">
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item mx-2">
                <button class="nav-link active px-4 py-2" id="all-reviews-tab" data-bs-toggle="tab" data-bs-target="#all-reviews" type="button" role="tab">
                    <i class="fas fa-comments me-2"></i>All Reviews
                </button>
            </li>
            @auth
            <li class="nav-item mx-2">
                <button class="nav-link px-4 py-2" id="my-reviews-tab" data-bs-toggle="tab" data-bs-target="#my-reviews" type="button" role="tab">
                    <i class="fas fa-user-edit me-2"></i>My Reviews
                </button>
            </li>
            @endauth
        </ul>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content">
        <!-- All Reviews Tab -->
        <div class="tab-pane fade show active" id="all-reviews" role="tabpanel">
            <div class="row g-4">
                @forelse($reviews as $review)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 rounded-4 overflow-hidden" style="background-color:white; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                            <div class="row g-0 h-100">
                                <div class="col-md-8">
                                    <div class="card-body p-4">
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            @if($review->privateBooking)
                                                <span class="badge bg-primary ms-2">Private Trip</span>
                                            @endif
                                        </div>
                                        
                                        <h5 class="card-title mb-2">{{ $review->booking ? $review->booking->travelPackage->name : $review->privateBooking->travelPackage->name }}</h5>
                                        
                                        <p class="card-text text-muted mb-3">{{ Str::limit($review->review_text, 120) }}</p>
                                        
                                        <div class="d-flex align-items-center mt-auto">
                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                            <small>{{ $review->user->name }}</small>
                                            <small class="ms-auto text-muted">{{ $review->created_at->format('d M Y') }}</small>
                                        </div>
                                        
                                        @if($review->reply)
                                        <div class="mt-3 pt-3 border-top">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-reply text-success me-2 mt-1"></i>
                                                <div>
                                                    <div class="d-flex align-items-center mb-1">
                                                    <small class="fw-bold text-success">Admin Response</small>
                                                        <small class="ms-2 text-muted">{{ $review->reply->created_at->format('d M Y') }}</small>
                                                    </div>
                                                    <p class="card-text small mb-0">{{ $review->reply->reply_text }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-4 d-flex align-items-center justify-content-center p-0">
                                    @if($review->photo_path)
                                        <img src="{{ asset('storage/' . $review->photo_path) }}" alt="Review Photo" class="img-fluid h-100 w-100" style="object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 w-100 bg-light">
                                            <i class="fas fa-mountain fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No reviews available yet.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $reviews->links() }}
            </div>
        </div>

        @auth
        <!-- My Reviews Tab -->
        <div class="tab-pane fade" id="my-reviews" role="tabpanel">
            <div class="row g-4">
                @forelse($reviews->where('user_id', auth()->id()) as $review)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 rounded-4 overflow-hidden" style="background-color:white; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                            <div class="row g-0 h-100">
                                <div class="col-md-8">
                                    <div class="card-body p-4">
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            @if($review->privateBooking)
                                                <span class="badge bg-primary ms-2">Private Trip</span>
                                            @endif
                                        </div>
                                        
                                        <h5 class="card-title mb-2">{{ $review->booking ? $review->booking->travelPackage->name : $review->privateBooking->travelPackage->name }}</h5>
                                        
                                        <p class="card-text text-muted mb-3">{{ Str::limit($review->review_text, 120) }}</p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                                            <div>
                                                @if(!$review->has_been_edited)
                                                    <a href="{{ route('customer.review.edit', $review->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" disabled title="This review has already been edited once">
                                                        <i class="fas fa-edit me-1"></i>Edited
                                                    </button>
                                                @endif
                                                <!-- <form action="{{ route('customer.review.destroy', $review->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Are you sure you want to delete this review?')">
                                                        <i class="fas fa-trash-alt me-1"></i>Delete
                                                    </button>
                                                </form> -->
                                            </div>
                                        </div>
                                        
                                        @if($review->reply)
                                        <div class="mt-3 pt-3 border-top">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-reply text-success me-2 mt-1"></i>
                                                <div>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <small class="fw-bold text-success">Admin Response</small>
                                                        <small class="ms-2 text-muted">{{ $review->reply->created_at->format('d M Y') }}</small>
                                                    </div>
                                                    <p class="card-text small mb-0">{{ $review->reply->reply_text }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-4 d-flex align-items-center justify-content-center p-0">
                                    @if($review->photo_path)
                                        <img src="{{ asset('storage/' . $review->photo_path) }}" alt="Review Photo" class="img-fluid h-100 w-100" style="object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 w-100 bg-light">
                                            <i class="fas fa-mountain fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">You haven't written any reviews yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endauth
    </div>
</div>
@endsection
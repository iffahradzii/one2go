@extends('layout.layout')

@section('title', 'Edit Review')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="fw-bold text-primary">Edit Your Review</h1>
            <p class="text-muted lead">Update your feedback for {{ $review->booking ? $review->booking->travelPackage->name : $review->privateBooking->travelPackage->name }}</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-gradient text-white bg-primary py-3">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Review</h4>
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
                    
                    <form action="{{ route('customer.review.update', $review->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">How would you rate your experience?</label>
                            <div class="rating-stars p-3 bg-light rounded mb-3">
                                <div class="d-flex justify-content-center">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <div class="form-check form-check-inline mx-2">
                                            <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ $review->rating == $i ? 'checked' : '' }} required>
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
                            <textarea class="form-control" id="review_text" name="review_text" rows="5" placeholder="Share your thoughts about the trip..." required>{{ $review->review_text }}</textarea>
                            <div class="form-text">Your honest feedback helps us improve and assists other travelers in making decisions.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="photo" class="form-label fw-bold">Update your photo (optional)</label>
                            <input class="form-control" type="file" id="photo" name="photo" accept="image/*">
                            <div class="form-text">Upload a new photo or leave empty to keep your current photo.</div>
                            
                            @if($review->photo_path)
                                <div class="mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remove_photo" name="remove_photo" value="1">
                                            <label class="form-check-label text-danger" for="remove_photo">
                                                Remove current photo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="mb-1">Current photo:</p>
                                        <img src="{{ asset($review->photo_path) }}" alt="Current Review Photo" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('customer.reviews.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <p class="mb-0 small">You can only edit a review once for each travel package. Please ensure your feedback is accurate and reflects your experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview image before upload
    document.getElementById('photo').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // If there's an existing preview, remove it
                const existingPreview = document.querySelector('.preview-container');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                // Create preview container
                const previewContainer = document.createElement('div');
                previewContainer.className = 'preview-container mt-2';
                
                // Create preview image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.maxHeight = '150px';
                
                // Add preview to page
                previewContainer.appendChild(document.createElement('p')).textContent = 'New photo preview:';
                previewContainer.appendChild(img);
                
                // Insert after file input
                event.target.parentNode.appendChild(previewContainer);
                
                // Uncheck remove photo if it was checked
                const removePhotoCheckbox = document.getElementById('remove_photo');
                if (removePhotoCheckbox) {
                    removePhotoCheckbox.checked = false;
                }
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Handle remove photo checkbox
    const removePhotoCheckbox = document.getElementById('remove_photo');
    if (removePhotoCheckbox) {
        removePhotoCheckbox.addEventListener('change', function() {
            const photoInput = document.getElementById('photo');
            if (this.checked) {
                photoInput.value = ''; // Clear file input
                
                // Remove preview if exists
                const previewContainer = document.querySelector('.preview-container');
                if (previewContainer) {
                    previewContainer.remove();
                }
            }
        });
    }
</script>
@endsection
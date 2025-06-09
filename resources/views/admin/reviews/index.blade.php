@extends('layout.layoutAdmin')

@section('title', 'Manage Reviews')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Title -->
    <h1 class="mb-4">Reviews & Ratings Management</h1>
    
    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Reviews</h6>
                            <h2 class="mb-0">{{ $reviews->total() }}</h2>
                        </div>
                        <i class="fas fa-comments fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Average Rating</h6>
                            <h2 class="mb-0">{{ number_format($reviews->avg('rating'), 1) }}/5</h2>
                        </div>
                        <i class="fas fa-star fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Replied Reviews</h6>
                            <h2 class="mb-0">{{ $reviews->where('reply', '!=', null)->count() }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Pending Replies</h6>
                            <h2 class="mb-0">{{ $reviews->where('reply', null)->count() }}</h2>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
               
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Rating</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>{{ $review->id }}</td>
                                        <td>{{ $review->user->name }}</td>
                                        <td>
                                            {{ $review->booking ? $review->booking->travelPackage->name : $review->privateBooking->travelPackage->name }}
                                            @if($review->privateBooking)
                                                <span class="badge bg-info">Private</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </td>
                                        <td>{{ $review->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($review->reply)
                                                <span class="badge bg-success">Replied</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn" style="background-color: #00c3ff; color: #fff; min-width: 120px; text-align: center; padding: 6px 12px; border-radius: 4px;">
                                                <i class="fas fa-reply me-1"></i>Reply
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No reviews available yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
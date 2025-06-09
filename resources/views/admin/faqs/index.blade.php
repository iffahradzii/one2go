@extends('layout.layoutAdmin')

@section('title', 'Manage FAQs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage FAQs</h1>
        <div class="d-flex gap-3 align-items-start flex-wrap">
            <!-- Search Box -->
            <form class="d-flex" action="{{ route('admin.faqs.index') }}" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search FAQs..." value="{{ request('search') }}">
                <button class="btn btn-primary uniform-btn ms-2" type="submit">
                    Search
                </button>
            </form>
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-primary uniform-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ request('type') ? ucfirst(request('type')) : 'All Types' }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.faqs.index') }}">All Types</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.faqs.index', ['type' => 'booking']) }}">Booking</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.faqs.index', ['type' => 'payment']) }}">Payment</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.faqs.index', ['type' => 'travel']) }}">Travel</a></li>
                </ul>
            </div>
            <!-- Add FAQ Button -->
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary uniform-btn">
                <i class="fas fa-plus-circle me-1"></i> Add New FAQ
            </a>
        </div>
    </div>
</div>


    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total FAQs</h6>
                            <h2 class="mb-0">{{ $faqs->count() }}</h2>
                        </div>
                        <i class="fas fa-question-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Published FAQs</h6>
                            <h2 class="mb-0">{{ $faqs->where('is_published', true)->count() }}</h2>
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
                            <h6 class="card-title mb-1">Draft FAQs</h6>
                            <h2 class="mb-0">{{ $faqs->where('is_published', false)->count() }}</h2>
                        </div>
                        <i class="fas fa-file-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">FAQ Categories</h6>
                            <h2 class="mb-0">4</h2>
                        </div>
                        <i class="fas fa-tags fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FAQs Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" width="5%">No</th>
                            <th scope="col" width="30%">Question</th>
                            <th scope="col" width="15%">Type</th>
                            <th scope="col" width="10%">Status</th>
                            <th scope="col" width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::limit($faq->question, 50) }}</td>
                                <td>
                                    <span class="badge py-2 px-3" style="min-width: 80px; 
                                        background-color: {{ 
                                            $faq->type == 'booking' ? '#0d6efd' : 
                                            ($faq->type == 'payment' ? '#198754' : 
                                            ($faq->type == 'travel' ? '#dc3545' : '#6c757d')) 
                                        }}; 
                                        color: white;">
                                        {{ ucfirst($faq->type) }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('faqs.visibility', $faq) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="badge rounded-pill border-0 py-2 px-3" 
                                                style="min-width: 90px; background-color: {{ $faq->is_published ? '#198754' : '#6c757d' }}; cursor: pointer;">
                                            {{ $faq->is_published ? 'Published' : 'Draft' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.faqs.edit', $faq) }}" 
                                           class="btn btn-info text-white d-flex align-items-center justify-content-center" 
                                           style="min-width: 120px; background-color: #00c3ff; border: none; border-radius: 8px; padding: 8px 16px;">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.faqs.destroy', $faq) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger d-flex align-items-center justify-content-center"
                                                    style="min-width: 120px; background-color: #dc3545; border: none; border-radius: 8px; padding: 8px 16px;"
                                                    onclick="return confirm('Are you sure you want to delete this FAQ?')"
                                                    title="Delete FAQ">
                                                <i class="fas fa-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No FAQs found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination (if available) -->
            @if(method_exists($faqs, 'links'))
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $faqs->firstItem() ?? 0 }} to {{ $faqs->lastItem() ?? 0 }} of {{ $faqs->total() }} results
                </div>
                <div>
                    {{ $faqs->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Additional scripts if needed
</script>
@endsection
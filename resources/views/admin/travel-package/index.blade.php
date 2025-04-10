@extends('layout.layoutAdmin')

@section('title', 'Travel Package Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Travel Package Management</h1>
        <div class="d-flex gap-3">
            <!-- Search Box -->
            <form class="d-flex" action="{{ route('admin.travel-package.index') }}" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search packages..." value="{{ request('search') }}">
                <button class="btn btn-primary ms-2" type="submit">Search</button>
            </form>
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ $selectedCountry ?? 'All Countries' }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.travel-package.index') }}">All Countries</a></li>
                    @foreach($countries as $country)
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.travel-package.index', ['country' => $country]) }}">
                                {{ $country }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Add Package Button -->
            <a href="{{ route('admin.travel-package.create') }}" class="btn btn-primary">Add Package</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Packages</h6>
                            <h2 class="mb-0">{{ $packages->count() }}</h2>
                        </div>
                        <i class="fas fa-box fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Active Packages</h6>
                            <h2 class="mb-0">{{ $packages->where('is_visible', true)->count() }}</h2>
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
                            <h6 class="card-title mb-1">Hidden Packages</h6>
                            <h2 class="mb-0">{{ $packages->where('is_visible', false)->count() }}</h2>
                        </div>
                        <i class="fas fa-eye-slash fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Countries</h6>
                            <h2 class="mb-0">{{ count($countries) }}</h2>
                        </div>
                        <i class="fas fa-globe fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Packages Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Country</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packages as $package)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($package->image)
                                        <img src="{{ asset('storage/' . $package->image) }}" 
                                             alt="{{ $package->name }}" 
                                             width="50" 
                                             height="50" 
                                             class="rounded">
                                    @else
                                        <div class="text-muted"><i class="fas fa-image fa-2x"></i></div>
                                    @endif
                                </td>
                                <td>{{ $package->name }}</td>
                                <td>{{ $package->country }}</td>
                                <td>RM {{ number_format($package->price, 2) }}</td>
                                <!-- Update the Status column in the table -->
                                <td>
                                    <select class="form-select form-select-sm" 
                                            onchange="updateStatus(this, {{ $package->id }})"
                                            style="width: auto;">
                                        <option value="Visible" {{ $package->is_visible ? 'selected' : '' }}>Visible</option>
                                        <option value="Invisible" {{ !$package->is_visible ? 'selected' : '' }}>Invisible</option>
                                    </select>
                                </td>
                                
                                <!-- Update the Actions column, removing the delete form -->
                                <td>
                                    <a href="{{ route('admin.travel-package.edit', ['travel_package' => $package->id]) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                                <!-- Remove the delete form that was here -->
                                </tr>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No packages found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($packages instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $packages->firstItem() ?? 0 }} to {{ $packages->lastItem() ?? 0 }} of {{ $packages->total() }} results
                    </div>
                    <div>
                        {{ $packages->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Add Font Awesome CDN -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<script>
function updateStatus(selectElement, packageId) {
    const status = selectElement.value === 'Visible' ? 1 : 0;
    
    fetch(`{{ url('admin/travel-package/toggle-visibility') }}/${packageId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ is_visible: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optional: Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                Status updated successfully
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection

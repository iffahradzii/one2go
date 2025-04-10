@extends('layout.layoutAdmin')

@section('title', 'Customer Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Customer Management</h1>
        <div class="d-flex gap-3">
            <!-- Search Box -->
            <form class="d-flex" action="{{ route('admin.customer.index') }}" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search customers..." value="{{ request('search') }}">
                <button class="btn btn-primary ms-2" type="submit">Search</button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Customers</h6>
                            <h2 class="mb-0">{{ $customers->count() }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Verified Customers</h6>
                            <h2 class="mb-0">{{ $customers->whereNotNull('email_verified_at')->count() }}</h2>
                        </div>
                        <i class="fas fa-user-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Unverified Customers</h6>
                            <h2 class="mb-0">{{ $customers->whereNull('email_verified_at')->count() }}</h2>
                        </div>
                        <i class="fas fa-user-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Registered Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $customer->email_verified_at ? 'bg-success' : 'bg-warning' }}">
                                        {{ $customer->email_verified_at ? 'Verified' : 'Unverified' }}
                                    </span>
                                </td>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.customer.edit', ['customer' => $customer->id]) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"> Edit</i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No customers found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($customers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} results
                    </div>
                    <div>
                        {{ $customers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
@endsection

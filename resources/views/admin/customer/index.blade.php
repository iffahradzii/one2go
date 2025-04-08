@extends('layout.layoutAdmin')

@section("title")
Customer Details
@endsection

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Customer List</h1>

    <!-- Flash Message -->
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Responsive Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Registered Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td> <!-- Assuming 'phone' field exists in the database -->
                        <td>{{ $customer->created_at->format('d M Y') }}</td>
                        <td>
                            <!-- Edit Button -->
                            <a href="{{ route('admin.customer.edit', ['customer' => $customer->id]) }}" class="btn btn-warning btn-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No customers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

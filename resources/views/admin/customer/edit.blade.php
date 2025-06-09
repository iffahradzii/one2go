@extends('layout.layoutAdmin')
@section("title")
Customer Details Edit
@endsection

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Edit Customer Phone Number</h1>

    <!-- Flash Message -->
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.customer.update', ['customer' => $customer->id]) }}" method="POST" class="col-md-6 mx-auto">
        @csrf
        @method('PUT')

        <!-- Display Name (Read-Only) -->
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" class="form-control" value="{{ $customer->name }}" readonly>
        </div>

        <!-- Display Email (Read-Only) -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" value="{{ $customer->email }}" readonly>
        </div>

        <!-- Editable Phone Number -->
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $customer->phone }}" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

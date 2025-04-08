@extends('layout.layoutAdmin')
@section("title")
Admin Dashboard
@endsection

@section('content')
<div class="container mt-5">
    <!-- Flash Message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-center mb-4">Travel Packages</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Filter Button (Icon) -->
        <button class="btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-filter"></i> Filter
        </button>
        <div class="dropdown-menu p-3">
            <form method="GET" action="{{ route('admin.travel-package.index') }}">
                <div class="form-group mb-2">
                    <label for="country" class="form-label">Select Country</label>
                    <select name="country" class="form-select">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ $selectedCountry == $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Apply Filter</button>
            </form>
        </div>
        

        <!-- Add Package Button aligned to the right -->
        <a href="{{ route('admin.travel-package.create') }}" class="btn btn-primary">Add Package</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($packages as $package)
                <tr>
                    <!-- Display Image -->
                    <td>
                        @if($package->image)
                            <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" width="50" height="50" class="rounded">
                        @else
                            <span>No Image</span>
                        @endif
                    </td>

                    <!-- Display Name -->
                    <td>{{ $package->name }}</td>

                    <!-- Display Price -->
                    <td>RM {{ number_format($package->price, 2) }}</td>

                    <!-- Display Country -->
                    <td>{{ $package->country }}</td>

                    <!-- Actions -->
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            <!-- Visibility Dropdown -->
                            <form action="{{ route('admin.travel-package.toggleVisibility', ['id' => $package->id]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="visibilityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $package->is_visible ? 'Visible' : 'Invisible' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="visibilityDropdown">
                                        <li>
                                            <button class="dropdown-item" type="submit" name="visibility" value="1">Visible</button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" type="submit" name="visibility" value="0">Invisible</button>
                                        </li>
                                    </ul>
                                </div>
                            </form>

                            <!-- Edit Button -->
                            <a href="{{ route('admin.travel-package.edit', ['travel_package' => $package->id]) }}" class="btn btn-warning btn-sm mx-2">Edit</a>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.travel-package.destroy', ['travel_package' => $package->id]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this package?')">Delete</button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No packages found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<!-- Add Font Awesome CDN -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
@endsection

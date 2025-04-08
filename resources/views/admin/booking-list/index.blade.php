@extends('layout.layoutAdmin')

@section('title', 'Admin Booking List')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-4">Admin Booking List</h1>


</div>


    <!-- Booking Table -->
    <div class="card shadow">
       
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Travel Package</th>
                        <th>Date</th>
                        <th>Adults</th>
                        <th>Children</th>
                        <th>Infants</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $booking->customer_name }}</td>
                            <td>{{ $booking->travelPackage->name ?? 'Unknown Package' }}</td>
                            <td>{{ $booking->available_date }}</td>
                            <td>{{ $booking->adults }}</td>
                            <td>{{ $booking->children }}</td>
                            <td>{{ $booking->infants }}</td>
                            <td>RM{{ number_format($booking->total_price, 2) }}</td>
                            <td>
                                <span class="badge 
                                    @if ($booking->payment_status == 'paid') bg-success
                                    @elseif ($booking->payment_status == 'pending') bg-warning
                                    @else bg-secondary @endif">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.booking.index', ['bookingId' => $booking->id]) }}" class="btn btn-sm btn-info">View</a>
                                <form action="{{ route('admin.booking.index', ['bookingId' => $booking->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No bookings available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>
@endsection

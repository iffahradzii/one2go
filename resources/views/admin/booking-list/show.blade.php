@extends('layout.layoutAdmin')

@section('title', 'Booking Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Booking Details #{{ $booking->id }}</h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#updateStatus{{ $booking->id }}">
                            Update Status
                        </button>
                        <a href="{{ route('admin.booking.index') }}" class="btn btn-light btn-sm">Back to List</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold mb-3">Customer Information</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Name</th>
                                        <td>{{ $booking->customer_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $booking->customer_email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $booking->customer_phone }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold mb-3">Booking Details</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Package</th>
                                        <td>{{ $booking->travelPackage->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Travel Date</th>
                                        <td>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge rounded-pill 
                                                @if($booking->payment_status == 'paid') bg-success
                                                @elseif($booking->payment_status == 'pending') bg-warning
                                                @else bg-danger @endif">
                                                {{ ucfirst($booking->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Participants -->
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold mb-3">Participants</h6>
                            <div class="table-responsive">
                            @php
                                            $adultCount = $booking->travelers->where('category', 'Adult')->count();
                                            $childCount = $booking->travelers->where('category', 'Child')->count();
                                            $infantCount = $booking->travelers->where('category', 'Infant')->count();
                                        @endphp
                                

                                <table class="table table-borderless">
                                    <tr>
                                        <th>Adults</th>
                                        <td>{{ $adultCount }}</td>
                                    </tr>
                                    <tr>
                                        <th>Children</th>
                                        <td>{{ $childCount }}</td>
                                    </tr>
                                    <tr>
                                        <th>Infants</th>
                                        <td>{{ $infantCount }}</td>
                                    </tr>
                                </table>
                               
        
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold mb-3">Payment Information</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Total Price</th>
                                        <td>RM {{ number_format($booking->total_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Booking Date</th>
                                        <td>{{ $booking->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($booking->notes)
                        <!-- Notes -->
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">Notes</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $booking->notes }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
@include('admin.booking-list.partials.status-modal', ['booking' => $booking])
@endsection
@extends('layout.layoutAdmin')

@section('title', 'Private Booking Details')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="fw-bold">Private Booking Details</h1>
                <div>
                    <button onclick="printReceipt()" class="btn btn-success me-2">
                        <i class="fas fa-download me-2"></i>Save Receipt
                    </button>
                    <a href="{{ route('admin.private-booking.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="receipt-content">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Booking Information
                </h5>
                <span class="badge bg-light text-primary">Private Package</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Booking ID and Basic Info -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-3 mb-3">{{ $booking->travelPackage->name }}</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Booking ID</small>
                                    <span class="fw-bold">#{{ $booking->id }}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Country</small>
                                    <span>{{ $booking->travelPackage->country }}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Duration</small>
                                    <span>{{ $booking->travelPackage->duration }} Days</span>                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Travel Date</small>
                                    <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                </div>

                                <div class="col-md-3 mt-3">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge rounded-pill 
                                        @if($booking->payment_status == 'paid') bg-success
                                        @elseif($booking->payment_status == 'pending') bg-warning
                                        @else bg-danger @endif">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <h5 class="border-bottom pb-2 mb-3">User Information</h5>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200" class="bg-light">Name</th>
                                <td>{{ $booking->customer_name }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Email</th>
                                <td>{{ $booking->customer_email }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Phone</th>
                                <td>{{ $booking->customer_phone }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Participant List -->
            <h5 class="border-bottom pb-2 mb-3">Participant List</h5>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" style="color: white">#</th>
                                    <th style="color: white">Name</th>
                                    <th style="color: white">IC Number</th>
                                    <th style="color: white">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->participants as $index => $participant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $participant->name }}</td>
                                    <td>{{ $participant->ic_number }}</td>
                                    <td>
                                        <span class="badge {{ $participant->type == 'ADULT' ? 'bg-primary' : ($participant->type == 'CHILD' ? 'bg-info' : 'bg-warning') }}">
                                            {{ $participant->type }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Additional Activities -->
            <h5 class="border-bottom pb-2 mb-3">Additional Activities</h5>
            <div class="row mb-4">
                <div class="col-md-12">
                    @if($booking->activities && count($booking->activities) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th style="color: white">Activity</th>
                                    <th style="color: white">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach($booking->activities as $activity)
                            <tr>
                                    <td>{{ $activity->activity_name }}</td>
                                    <td>RM {{ number_format($activity->activity_price, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td class="fw-bold">Total Additional Activities</td>
                                    <td class="fw-bold">RM {{ number_format($booking->additional_price ?? 0, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No additional activities selected for this booking.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Itinerary -->
            <h5 class="border-bottom pb-2 mb-3">Itinerary</h5>
            <div class="row mb-4">
                <div class="col-md-12">
                    @if($booking->travelPackage && $booking->travelPackage->itinerary)
                        @php
                            $hasCustomItinerary = $booking->customDays && $booking->customDays->count() > 0;
                            $packageItinerary = is_string($booking->travelPackage->itinerary) ? 
                                json_decode($booking->travelPackage->itinerary, true) : 
                                $booking->travelPackage->itinerary;
                        @endphp
                        
                        @if($hasCustomItinerary)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>This itinerary has been customized.
                            </div>
                        @else
                            <p class="text-muted mb-3">Using original package itinerary</p>
                        @endif
                        
                        @if(is_array($packageItinerary) && count($packageItinerary) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="150" style="color: white">Day</th>
                                            <th style="color: white">Activities</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($packageItinerary as $dayIndex => $dayActivities)
                                            @php
                                                $customDay = $hasCustomItinerary ? $booking->customDays->where('day_number', $dayIndex + 1)->first() : null;
                                                $customActivities = $customDay ? 
                                                    (is_string($customDay->custom_activities) ? 
                                                        json_decode($customDay->custom_activities, true) : 
                                                        $customDay->custom_activities) : 
                                                    null;
                                            @endphp
                                            <tr>
                                                <td class="fw-bold">
                                                    Day {{ $dayIndex + 1 }}
                                                    @if($customDay)
                                                        <span class="badge bg-primary ms-2">Customized</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($customDay && $customActivities)
                                                        @foreach($customActivities as $activity)
                                                            {{ $activity }}<br>
                                                        @endforeach
                                                    @else
                                                        @if(is_array($dayActivities))
                                                            <ul class="mb-0">
                                                                @foreach($dayActivities as $time => $activity)
                                                                    <li><strong>{{ $time }}</strong>: {{ $activity }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            {{ $dayActivities }}
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No structured itinerary information available for this booking.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No itinerary information available for this booking.
                        </div>
                    @endif
                </div>
            </div>

        
                </div>
            </div>

            <!-- Total Price -->
            <h5 class="border-bottom pb-2 mb-3">Price Details</h5>
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Package Base Price:</span>
                                <span>RM {{ number_format($booking->travelPackage->price, 2) }}</span>
                            </div>
                           
                            @if($booking->activities && count($booking->activities) > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Additional Activities:</span>
                                <span>RM {{ number_format($booking->additional_price ?? 0, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span>Number of Participants:</span>
                                <span>{{ $booking->participants->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Booking Date:</span>
                                <span>{{ $booking->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Price:</span>
                                <span class="text-primary">RM {{ number_format($booking->total_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="text-center">
                <p class="mb-0 text-muted">Thank you for booking with us!</p>
                <p class="mb-0 small text-muted">If you have any questions, please contact our support team.</p>
            </div>
        </div>
    </div>
</div>



<script>
function printReceipt() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Get the receipt content
    const receiptContent = document.getElementById('receipt-content').cloneNode(true);
    
    // Create HTML content for the print window
    const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Booking Receipt #${receiptContent.querySelector('.fw-bold').innerText}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
            <style>
                body { padding: 20px; }
                @media print {
                    .no-print { display: none; }
                    body { padding: 0; }
                }
                .header { text-align: center; margin-bottom: 20px; }
                .footer { text-align: center; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Booking Receipt</h2>
                    <p>Thank you for your booking!</p>
                </div>
                ${receiptContent.outerHTML}
                <div class="footer">
                    <p class="mb-0 text-muted">Thank you for booking with us!</p>
                    <p class="mb-0 small text-muted">If you have any questions, please contact our support team.</p>
                </div>
                <div class="row mt-4 no-print">
                    <div class="col-12 text-center">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print me-2"></i>Print Receipt
                        </button>
                        <button onclick="window.close()" class="btn btn-secondary ms-2">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </body>
        </html>
    `;
    
    // Write to the new window and focus it
    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();
}
</script>
@endsection

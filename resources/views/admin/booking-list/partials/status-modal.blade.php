<div class="modal fade" id="updateStatus{{ $booking->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Update Booking Status
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.booking.updateStatus', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Booking Details</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Customer</small>
                                        <span>{{ $booking->customer_name }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Package</small>
                                        <span>{{ $booking->travelPackage->name }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Travel Date</small>
                                        <span>{{ \Carbon\Carbon::parse($booking->available_date)->format('d M Y') }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Total Price</small>
                                        <span>RM {{ number_format($booking->total_price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Current Status</label>
                        <div>
                            <span class="badge rounded-pill fs-6 
                                @if($booking->payment_status == 'paid') bg-success
                                @elseif($booking->payment_status == 'pending') bg-warning
                                @else bg-danger @endif">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_status" class="form-label fw-bold">New Status</label>
                        <select class="form-select form-select-lg" name="payment_status" required>
                            <option value="pending" @if($booking->payment_status == 'pending') selected @endif>
                                🕒 Pending
                            </option>
                            <option value="paid" @if($booking->payment_status == 'paid') selected @endif>
                                ✅ Paid
                            </option>
                            <option value="cancelled" @if($booking->payment_status == 'cancelled') selected @endif>
                                ❌ Cancelled
                            </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
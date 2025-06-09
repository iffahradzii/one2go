
<!-- Private Booking Status Modal -->
<div class="modal fade" id="updatePrivateStatus{{ $booking->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Update Private Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.private-booking.updateStatus', $booking->id) }}" method="POST">
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
                                    <div class="col-12">
                                        <small class="text-muted d-block">Travel Date</small>
                                        <input type="date" 
                                               name="travel_date" 
                                               class="form-control" 
                                               value="{{ $booking->available_date }}" 
                                               min="{{ date('Y-m-d', strtotime('+14 days')) }}"
                                               @if($booking->payment_status == 'cancelled') disabled @endif>
                                        <small class="form-text text-muted">Date must be at least 2 weeks from today</small>
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

                    @if($booking->payment_status != 'cancelled')
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="update_type" id="updateDate" value="date" checked>
                            <label class="form-check-label" for="updateDate">Update Date</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="update_type" id="cancelBooking" value="cancel">
                            <label class="form-check-label" for="cancelBooking">Cancel Booking</label>
                        </div>
                    </div>

                    <div id="cancelConfirmation" class="alert alert-danger d-none" role="alert">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="confirm_cancel" id="confirmCancel">
                            <label class="form-check-label" for="confirmCancel">
                                I confirm that I want to cancel this booking
                            </label>
                        </div>
                    </div>
                    @endif

                    <input type="hidden" name="payment_status" value="{{ $booking->payment_status }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    @if($booking->payment_status != 'cancelled')
                    <button type="submit" class="btn btn-primary" id="updateButton">
                        <i class="fas fa-save me-1"></i>Update Date
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateTypeRadios = document.querySelectorAll('input[name="update_type"]');
    const cancelConfirmation = document.getElementById('cancelConfirmation');
    const confirmCancelCheckbox = document.getElementById('confirmCancel');
    const updateButton = document.getElementById('updateButton');
    const dateInput = document.querySelector('input[name="travel_date"]');
    const paymentStatusInput = document.querySelector('input[name="payment_status"]');

    updateTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
    if (this.value === 'cancel') {
        cancelConfirmation.classList.remove('d-none');
        confirmCancelCheckbox.required = true;
        updateButton.innerHTML = '<i class="fas fa-ban me-1"></i>Cancel Booking';
        updateButton.classList.remove('btn-primary');
        updateButton.classList.add('btn-danger');

        // Disable travel date to bypass required + min
        dateInput.required = false;
        dateInput.disabled = true;
        paymentStatusInput.value = 'cancelled';
    } else {
        cancelConfirmation.classList.add('d-none');
        confirmCancelCheckbox.required = false;
        updateButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Date';
        updateButton.classList.remove('btn-danger');
        updateButton.classList.add('btn-primary');

        // Enable travel date again
        dateInput.disabled = false;
        dateInput.required = true;
        paymentStatusInput.value = '{{ $booking->payment_status }}';
    }
});

    });
});
</script>
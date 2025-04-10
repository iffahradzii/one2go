<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        // Update expired pending bookings to cancelled
        $this->updateExpiredBookings();
    
        $bookings = Booking::with(['travelPackage', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Calculate statistics
        $totalBookings = $bookings->total();
        $pendingBookings = Booking::where('payment_status', 'pending')->count();
        $paidBookings = Booking::where('payment_status', 'paid')->count();
        $cancelledBookings = Booking::where('payment_status', 'cancelled')->count();
        
        return view('admin.booking-list.index', compact(
            'bookings',
            'totalBookings',
            'pendingBookings',
            'paidBookings',
            'cancelledBookings'
        ));
    }

    private function updateExpiredBookings()
    {
        // Update status for pending bookings where travel date has passed
        Booking::where('payment_status', Booking::PAYMENT_STATUS['pending'])
            ->where('available_date', '<', now())
            ->update([
                'payment_status' => Booking::PAYMENT_STATUS['cancelled']
            ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['travelPackage', 'user']);
        return view('admin.booking-list.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,cancelled'
        ]);

        $booking->update($validated);
        return redirect()->back()->with('success', 'Booking status updated successfully');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.booking.index')
            ->with('success', 'Booking deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PrivateBooking;
use Illuminate\Http\Request;

class PrivateBookingController extends Controller
{
    public function index(Request $request)
    {
        $this->updateCompletedPrivateBookings();

        $search = $request->input('search');
        $status = $request->input('status');
        
        // Base query for private bookings using the PrivateBooking model
        $query = PrivateBooking::query();
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('travelPackage', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status') && !empty($request->status)) {
            $status = $request->status;
            $query->whereHas('payments', function($query) use ($status) {
                $query->where('payment_status', $status)
                      ->whereIn('id', function($subQuery) {
                          $subQuery->selectRaw('MAX(id)')
                                   ->from('payments')
                                   ->whereColumn('private_booking_id', 'private_bookings.id')
                                   ->groupBy('private_booking_id');
                      });
            });
        }
        
        // Get paginated results
        $bookings = $query->latest()->paginate(10);
        
        // Statistics for private bookings
        $totalBookings = PrivateBooking::count();
      
         // Count bookings with specific payment statuses
         $pendingBookings = PrivateBooking::whereHas('payments', function($query) {
            $query->where('payment_status', 'pending')
                  ->whereIn('id', function($subQuery) {
                      $subQuery->selectRaw('MAX(id)')
                               ->from('payments')
                               ->whereColumn('private_booking_id', 'private_bookings.id')
                               ->groupBy('private_booking_id');
                  });
        })->count();

        $paidBookings = PrivateBooking::whereHas('payments', function($query) {
            $query->where('payment_status', 'paid')
                  ->whereIn('id', function($subQuery) {
                      $subQuery->selectRaw('MAX(id)')
                               ->from('payments')
                               ->whereColumn('private_booking_id', 'private_bookings.id')
                               ->groupBy('private_booking_id');
                  });
        })->count();

        $completedBookings = PrivateBooking::whereHas('payments', function($query) {
                    $query->where('payment_status', 'complete')
                        ->whereIn('id', function($subQuery) {
                            $subQuery->selectRaw('MAX(id)')
                                    ->from('payments')
                                    ->whereColumn('private_booking_id', 'private_bookings.id')
                                    ->groupBy('private_booking_id');
                        });
                })->count();

           
        $cancelledBookings = PrivateBooking::whereHas('payments', function($query) {
            $query->where('payment_status', 'cancelled')
                  ->whereIn('id', function($subQuery) {
                      $subQuery->selectRaw('MAX(id)')
                               ->from('payments')
                               ->whereColumn('private_booking_id', 'private_bookings.id')
                               ->groupBy('private_booking_id');
                  });
        })->count();
        
        return view('admin.booking-list.indexPrivate', compact(
            'bookings', 
            'totalBookings', 
            'pendingBookings', 
            'paidBookings', 
            'cancelledBookings',
            'completedBookings'
        ));
    }
    
    public function show($id)
    {
        $booking = PrivateBooking::with(['travelPackage', 'participants', 'activities', 'customDays'])
            ->findOrFail($id);
        
        return view('admin.booking-list.showPrivate', compact('booking'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $booking = PrivateBooking::findOrFail($id);
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,cancelled',
            'travel_date' => 'nullable|date|after:' . now()->addDays(14)->format('Y-m-d')
        ]);

        // Update the booking's travel date if provided
        if (isset($validated['travel_date'])) {
            $booking->update([
                'available_date' => $validated['travel_date']
            ]);
        }

        // Update existing payment record with all necessary fields
        $booking->latestPayment()->update([
            'payment_status' => $validated['payment_status'],
            'amount' => $booking->total_price,
            'payment_method' => 'admin_update',
            'notes' => 'Status updated by admin'
        ]);
        
        return redirect()->back()->with('success', 'Booking status updated successfully');
    }
    
    public function destroy($id)
    {
        $booking = PrivateBooking::findOrFail($id);
        $booking->delete();
        
        return redirect()->route('admin.private-booking.index')->with('success', 'Booking deleted successfully');
    }
    private function updateCompletedPrivateBookings()
{
    $bookingsToComplete = PrivateBooking::whereHas('latestPayment', function($query) {
        $query->where('payment_status', 'paid');
    })->where('available_date', '<', now())->get();

    foreach ($bookingsToComplete as $booking) {
        $booking->latestPayment()->update([
            'payment_status' => 'complete',
            'notes' => 'Automatically marked as complete after travel date passed',
            'payment_method' => 'system'
        ]);
    }
}

}

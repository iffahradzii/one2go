<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PrivateBooking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    
    public function index(Request $request)
    {
        $this->updateCompletedBookings();
        // Get the booking type from request
        $bookingType = $request->input('type', 'general');
        
        if ($bookingType == 'general') {
            // Handle general bookings
            $bookingsQuery = Booking::with(['travelPackage', 'travelers', 'latestPayment']);
            
            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $bookingsQuery->where(function($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%");
                });
            }
            
            // Apply status filter if provided
            if ($request->has('status') && !empty($request->status)) {
                $status = $request->status;
                $bookingsQuery->whereHas('payments', function($query) use ($status) {
                    $query->where('payment_status', $status)
                          ->whereIn('id', function($subQuery) {
                              $subQuery->selectRaw('MAX(id)')
                                       ->from('payments')
                                       ->whereColumn('booking_id', 'bookings.id')
                                       ->groupBy('booking_id');
                          });
                });
            }
            
            $bookings = $bookingsQuery->latest()->paginate(10);
            
            // Statistics for general bookings only
            $totalBookings = Booking::count();
            
            // Count bookings with specific payment statuses
            $pendingBookings = Booking::whereHas('payments', function($query) {
                $query->where('payment_status', 'pending')
                      ->whereIn('id', function($subQuery) {
                          $subQuery->selectRaw('MAX(id)')
                                   ->from('payments')
                                   ->whereColumn('booking_id', 'bookings.id')
                                   ->groupBy('booking_id');
                      });
            })->count();
            
            $paidBookings = Booking::whereHas('payments', function($query) {
                $query->where('payment_status', 'paid')
                      ->whereIn('id', function($subQuery) {
                          $subQuery->selectRaw('MAX(id)')
                                   ->from('payments')
                                   ->whereColumn('booking_id', 'bookings.id')
                                   ->groupBy('booking_id');
                      });
                      
            })->count();

            $completedBookings = Booking::whereHas('payments', function($query) {
                $query->where('payment_status', 'complete')
                    ->whereIn('id', function($subQuery) {
                        $subQuery->selectRaw('MAX(id)')
                                ->from('payments')
                                ->whereColumn('booking_id', 'bookings.id')
                                ->groupBy('booking_id');
                    });
            })->count();
            
            $cancelledBookings = Booking::whereHas('payments', function($query) {
                $query->where('payment_status', 'cancelled')
                      ->whereIn('id', function($subQuery) {
                          $subQuery->selectRaw('MAX(id)')
                                   ->from('payments')
                                   ->whereColumn('booking_id', 'bookings.id')
                                   ->groupBy('booking_id');
                      });
            })->count();
            
            return view('admin.booking-list.index', compact(
                'bookings',
                'totalBookings', 
                'pendingBookings', 
                'paidBookings', 
                'cancelledBookings',
                'bookingType',
                'completedBookings'
            ));
        } else {
            // Redirect to the private booking controller for private bookings
            return redirect()->route('admin.private-booking.index');
        }
    }

    private function updateExpiredBookings()
    {
        // Find all bookings with pending payments where the travel date has passed
        $expiredBookings = Booking::whereHas('payments', function($query) {
                $query->where('payment_status', 'pending')
                      ->whereIn('id', function($subQuery) {
                          $subQuery->selectRaw('MAX(id)')
                                   ->from('payments')
                                   ->whereColumn('booking_id', 'bookings.id')
                                   ->groupBy('booking_id');
                      });
            })
            ->where('available_date', '<', now())
            ->get();
        
        // Update payment status for each expired booking
        foreach ($expiredBookings as $booking) {
            $booking->payments()->create([
                'payment_status' => 'cancelled',
                'amount' => $booking->total_price,
                'payment_method' => 'system',
                'notes' => 'Automatically cancelled due to expired date'
            ]);
        }
        
        return count($expiredBookings);
    }

    public function show(Booking $booking)
    {
        $booking->load(['travelPackage', 'user']);
        return view('admin.booking-list.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
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
    
        // Update existing payment record instead of creating new one
        $booking->latestPayment()->update([
            'payment_status' => $validated['payment_status'],
            'amount' => $booking->total_price,
            'payment_method' => 'admin_update',
            'notes' => 'Status updated by admin'
        ]);
    
        return redirect()->back()->with('success', 'Booking status updated successfully');
    }

    private function updateCompletedBookings()
    {
        $bookingsToComplete = Booking::whereHas('latestPayment', function($query) {
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

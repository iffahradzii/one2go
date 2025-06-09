<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\TravelPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function showBookingForm($packageId)
    {
        $package = TravelPackage::findOrFail($packageId);
        $user = Auth::user();
        return view('customer.booking.form', compact('package', 'user'));
    }

    public function store(Request $request, $packageId)
    {
        $user = Auth::user();
        
        // Validate request data
        $request->validate([
            'available_date' => 'required|date',
            'travelers' => 'required|array|min:1',
            'travelers.*.name' => 'required|string',
            'travelers.*.ic' => 'required|string',
            'travelers.*.category' => 'required|in:Adult,Child,Infant',
        ]);
        
        // Debug the incoming request data
        Log::info('Booking request data:', [
            'total_price' => $request->total_price,
            'all_data' => $request->all()
        ]);
        
        $booking = Booking::create([
            'travel_package_id' => $packageId,
            'user_id' => Auth::id(),
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? '',
            'available_date' => $request->available_date,
            'booking_date' => now(),
            'total_price' => $request->total_price ?? 0,
            'notes' => $request->notes,
            // Remove the payment_status field from here
        ]);
    
        // Create a payment record for this booking
        $booking->payments()->create([
            'payment_status' => 'pending',
            'amount' => $request->total_price ?? 0,
        ]);
    
        // Store travelers information
        if (isset($request->travelers) && is_array($request->travelers)) {
            foreach ($request->travelers as $travelerData) {
                $booking->travelers()->create([
                    'name' => $travelerData['name'],
                    'ic_number' => $travelerData['ic'],
                    'category' => $travelerData['category']
                ]);
            }
        }

        return redirect()->route('payment.page', $booking->id)
            ->with('success', 'Booking created successfully! Please complete your payment.');
    }

    public function index()
    {
        // Cancel expired bookings first
        Booking::cancelExpiredBookings();
        
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your bookings');
        }
        
        $bookings = $user->bookings;
        
        // Get private bookings for the same user
        $privateBookings = $user->privateBookings;
        
        return view('customer.booking.index', compact('bookings', 'privateBookings'));
    }
    
    public function showMyBookings()
    {
        return $this->index();
    }
    
    public function showBookingDetails($id)
    {
        $booking = Booking::with(['travelPackage', 'travelers'])->findOrFail($id);
        
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('customer.booking.general-detail', compact('booking'));
    }
    
   
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\TravelPackage;
use Illuminate\Support\Facades\Auth;

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
        
        $booking = Booking::create([
            'travel_package_id' => $packageId,
            'user_id' => auth()->id(),
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'available_date' => $request->available_date,
            'booking_date' => now(),
            'total_price' => $request->totalPrice,
            'notes' => $request->notes,
            'payment_status' => 'pending',
        ]);

        // Store travelers information
        // Remove this update section as it's causing the error
        // The booking already has customer_name from the create() call above
        
        // When saving travelers, ensure you're using proper parameter binding
        foreach ($request->travelers as $travelerData) {
            $booking->travelers()->create([
                'name' => $travelerData['name'],
                'ic_number' => $travelerData['ic'],
                'category' => $travelerData['category']
            ]);
        }

        return redirect()->route('payment.page', $booking->id);
    }

    public function showMyBookings()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with('travelPackage')
            ->orderBy('available_date', 'asc')
            ->get();

        return view('customer.booking.index', compact('bookings'));
    }
}

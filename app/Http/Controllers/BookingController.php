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
        $user = Auth::user(); // Get the logged-in user

        return view('customer.booking.form', compact('package', 'user'));
    }

    public function storeBooking(Request $request, $packageId)
    {
        $user = Auth::user();
        $package = TravelPackage::findOrFail($packageId);

        $request->validate([
            'available_date' => 'required|date',
            'customer_phone' => 'required|string|max:15',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'infants' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $totalPrice = ($request->adults + $request->children) * $package->price;

        $booking = Booking::create([
            'user_id' => $user->id,
            'travel_package_id' => $package->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $request->customer_phone,
            'available_date' => $request->available_date,
            'adults' => $request->adults,
            'children' => $request->children,
            'infants' => $request->infants,
            'total_price' => $totalPrice,
            'notes' => $request->notes,  // Store the notes
            'payment_status' => 'pending',  // Set payment status to 'pending'
        ]);

        // Redirect to the payment page for the user to proceed with payment
        return redirect()->route('payment.page', ['bookingId' => $booking->id]);   
     }


    public function showMyBookings()
    {
        $user = Auth::user();

        // Retrieve the user's bookings with travel package details
        $bookings = Booking::where('user_id', $user->id)
            ->with('travelPackage') // Eager load related travel package data
            ->orderBy('available_date', 'asc') // Keep the order by date for consistency
            ->get();

        return view('customer.booking.index', compact('bookings'));
    }

    public function index()
    {
        $bookings = Booking::all();        
        return view('admin.booking-list.index', compact('bookings'));
    }


}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\TravelPackage;


class PaymentController extends Controller
{
    public function showPaymentPage($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $package = TravelPackage::findOrFail($booking->travel_package_id);  // Fetch the associated package


        return view('customer.payment.payment', [
            'bookingId' => $booking->id,
            'totalPrice' => $booking->total_price,
            'booking' => $booking,
            'package' => $package,  
            
        ]);
    }

    public function processPayment(Request $request, $bookingId)
    {
        $paymentMethod = $request->payment_method;
    
        if ($paymentMethod == 'pay_now') {
            // Simulate payment success
            Booking::where('id', $bookingId)->update(['payment_status' => 'paid']);
    
            return redirect()->route('my-booking')->with('success', 'Payment successful!');
        } elseif ($paymentMethod == 'pay_later') {
            // Mark as pending payment
            Booking::where('id', $bookingId)->update(['payment_status' => 'pending']);
    
            return redirect()->route('my-booking')->with('success', 'You can pay later. Booking is confirmed!');
        }
    
        return back()->withErrors('Invalid payment method selected.');
    }
    

    public function payLater($bookingId)
    {
        // Update the booking to "pending payment"
        Booking::where('id', $bookingId)->update(['payment_status' => 'pending']);

        // Redirect to My Booking page
        return redirect()->route('my-booking')->with('success', 'You can pay later. Booking is confirmed!');
    }

    public function paymentSuccess()
    {
        return view('customer.payment.success'); // You can create this view to show a success message
    }
}

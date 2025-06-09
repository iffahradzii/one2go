<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\TravelPackage;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function showPaymentPage($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            $package = TravelPackage::findOrFail($booking->travel_package_id);
            
            return view('customer.payment.payment', [
                'booking' => $booking,
                'package' => $package,
                'bookingId' => $bookingId,
                'totalPrice' => $booking->total_price
            ]);
        } catch (\Exception $e) {
            return redirect()->route('my-booking')->with('error', 'There was an error processing your payment. Please try again.');
        }
    }

    public function processPayment(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            $paymentMethod = $request->payment_method ?? 'pay_now';
            
            // If paying now with Stripe
            if ($paymentMethod !== 'pay_later') {
                // Set your Stripe API key
                Stripe::setApiKey(config('services.stripe.secret'));
                
                // Create a payment intent
                $paymentIntent = PaymentIntent::create([
                    'amount' => $booking->total_price * 100, // Amount in cents
                    'currency' => 'myr',
                    'payment_method' => $request->payment_method_id,
                    'confirm' => true,
                    'description' => 'Booking #' . $booking->id . ' - ' . $booking->user->name,
                    'metadata' => [
                        'booking_id' => $booking->id,
                        'customer_email' => $booking->user->email ?? 'Not provided',
                    ],
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never'
                    ]
                ]);
                
                // Store Stripe payment ID in your database
                $stripePaymentId = $paymentIntent->id;
            } else {
                $stripePaymentId = null;
            }
            
            // Update the payment record
            $booking->latestPayment()->update([
                'payment_status' => $paymentMethod === 'pay_later' ? 'pending' : 'paid',
                'amount' => $booking->total_price,
                'payment_method' => $paymentMethod === 'pay_later' ? 'pending' : 'online',
                'notes' => $paymentMethod === 'pay_later' ? 'Customer chose to pay later' : 'Payment processed online',
                'stripe_payment_id' => $stripePaymentId
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return $paymentMethod === 'pay_later'
                ? redirect()->route('my-booking')->with('success', 'You can pay later. Booking is confirmed!')
                : redirect()->route('payment.success')->with('success', 'Payment successful!');
        } catch (ApiErrorException $e) {
            // Handle Stripe API errors
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Stripe Error: ' . $e->getMessage()], 500);
            }
            return back()->withErrors('Stripe Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }

            return back()->withErrors('There was an error processing your payment. Please try again.');
        }
    }

    public function paymentSuccess()
    {
        return view('customer.payment.success', ['bookingType' => 'regular']);
    }
}

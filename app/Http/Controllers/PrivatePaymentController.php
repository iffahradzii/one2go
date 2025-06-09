<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateBooking;
use App\Models\TravelPackage;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PrivatePaymentController extends Controller
{
    public function showPaymentPage($privateBookingId)
    {
        // Add debug logging
        Log::info('Showing private payment page for booking ID: ' . $privateBookingId);
        
        try {
            $privateBooking = PrivateBooking::findOrFail($privateBookingId);
            $package = TravelPackage::findOrFail($privateBooking->travel_package_id);
            
            // Count participants by type
            $adults = $privateBooking->participants()->where('type', 'adult')->count();
            $children = $privateBooking->participants()->where('type', 'child')->count();
            $infants = $privateBooking->participants()->where('type', 'infant')->count();
            
            // Create a booking object with the necessary properties for the view
            $booking = (object)[
                'adults' => $adults,
                'children' => $children,
                'infants' => $infants
            ];
            
            return view('customer.private-booking.payment', [
                'privateBooking' => $privateBooking,
                'package' => $package
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing private payment page: ' . $e->getMessage());
            return redirect()->route('my-booking')->with('error', 'There was an error processing your payment. Please try again.');
        }
    }
    
    public function processPayment(Request $request, $privateBookingId)
    {
        try {
            Log::info('Private payment processing started', [
                'booking_id' => $privateBookingId,
                'payment_method' => $request->payment_method,
                'request_data' => $request->all()
            ]);
            
            $privateBooking = PrivateBooking::findOrFail($privateBookingId);
            $paymentMethod = $request->payment_method ?? 'pay_now';
            
            // If paying now with Stripe
            if ($paymentMethod !== 'pay_later') {
                // Set your Stripe API key
                Stripe::setApiKey(config('services.stripe.secret'));
                
                // Create a payment intent
                $paymentIntent = PaymentIntent::create([
                    'amount' => $privateBooking->total_price * 100, // Amount in cents
                    'currency' => 'myr',
                    'payment_method' => $request->payment_method_id,
                    'confirm' => true,
                    'description' => 'Private Booking #' . $privateBooking->id . ' - ' . $privateBooking->user->name,
                    'metadata' => [
                        'booking_id' => $privateBooking->id,
                        'customer_email' => $privateBooking->user->email ?? 'Not provided',
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
            
            // Update existing payment record
            $privateBooking->latestPayment()->update([
                'payment_status' => $paymentMethod === 'pay_later' ? 'pending' : 'paid',
                'amount' => $privateBooking->total_price,
                'payment_method' => $paymentMethod === 'pay_later' ? 'pending' : 'online',
                'notes' => $paymentMethod === 'pay_later' ? 'Customer chose to pay later' : 'Payment processed online',
                'stripe_payment_id' => $stripePaymentId
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }
            
            Log::info('Private payment successful, redirecting to success page', [
                'booking_id' => $privateBookingId
            ]);
            
            return $paymentMethod === 'pay_later'
                ? redirect()->route('my-booking')->with('success', 'You can pay later. Private booking is confirmed!')
                : redirect()->route('private-payment.success')->with('success', 'Payment successful for your private booking!');
        } catch (ApiErrorException $e) {
            // Handle Stripe API errors
            Log::error('Stripe error processing private booking payment: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Stripe Error: ' . $e->getMessage()], 500);
            }
            
            return back()->withErrors('Stripe Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error processing private booking payment: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            
            return back()->withErrors('There was an error processing your payment. Please try again.');
        }
    }
    
    public function payLater($privateBookingId)
    {
        try {
            $privateBooking = PrivateBooking::findOrFail($privateBookingId);
            
            // Update existing payment record instead of creating new one
            $privateBooking->latestPayment()->update([
                'payment_status' => 'pending',
                'amount' => $privateBooking->total_price,
                'payment_method' => 'pending',
                'notes' => 'Customer chose to pay later'
            ]);
            
            return redirect()->route('my-booking')->with('success', 'You can pay later. Private booking is confirmed!');
        } catch (\Exception $e) {
            Log::error('Error processing pay later for private booking: ' . $e->getMessage());
            return back()->withErrors('There was an error processing your request. Please try again.');
        }
    }
    
    public function paymentSuccess()
    {
        // Use the new dedicated success page for private bookings
        return view('customer.private-booking.success', ['bookingType' => 'private']);
    }
}
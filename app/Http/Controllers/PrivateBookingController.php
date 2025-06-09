<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use App\Models\PrivateBooking;
use App\Models\PrivateBookingParticipant;
use App\Models\PrivateBookingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrivateBookingController extends Controller
{
    public function create(TravelPackage $package)
    {
        // Cancel expired bookings first
        PrivateBooking::cancelExpiredBookings();
        
        return view('customer.private-booking.create', compact('package'));
    }

    public function store(Request $request, TravelPackage $package)
    {
        // Cancel expired bookings first
        PrivateBooking::cancelExpiredBookings();
        
        $request->validate([
            'date_option' => 'required|in:predefined,custom',
            'available_date' => 'required_if:date_option,predefined',
            'custom_date' => 'required_if:date_option,custom',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.ic_number' => 'required|string',
        ]);

        $travelDate = $request->date_option == 'predefined' 
            ? $request->available_date 
            : Carbon::createFromFormat('d/m/Y', $request->custom_date)->format('Y-m-d');

        DB::beginTransaction();

        try {
            $privateBooking = PrivateBooking::create([
                'user_id' => Auth::id(),
                'travel_package_id' => $package->id,
                'customer_name' => Auth::user()->name,
                'customer_email' => Auth::user()->email,
                'customer_phone' => Auth::user()->phone ?? '',
                'available_date' => $travelDate,
                'booking_date' => now(),
                'base_price' => $package->price,
                'total_price' => $request->total_price ?? $package->price,
                'additional_price' => $request->additional_price ?? 0,
                // Remove 'payment_status' => 'pending'
            ]);
            
            // Create initial payment record
            $privateBooking->payments()->create([
                'payment_status' => 'pending',
                'amount' => $privateBooking->total_price,
                'notes' => 'Initial booking payment'
            ]);
            
            // Save participants
            foreach ($request->participants as $participant) {
                PrivateBookingParticipant::create([
                    'private_booking_id' => $privateBooking->id,
                    'name' => $participant['name'],
                    'ic_number' => $participant['ic_number'],
                    'type' => $participant['type'] ?? 'adult'
                ]);
            }
            
            // Save custom itinerary days if provided
            if ($request->has('custom_days') && is_array($request->custom_days)) {
                foreach ($request->custom_days as $dayIndex => $dayData) {
                    // Check if we have custom activities data from the textarea
                    if (isset($dayData['custom_activities']) && !empty(trim($dayData['custom_activities']))) {
                        // Create the custom day record
                        \App\Models\PrivateBookingCustomDay::create([
                            'private_booking_id' => $privateBooking->id,
                            'day_number' => $dayData['day_number'],
                            'custom_activities' => $dayData['custom_activities_json'] ?? $dayData['custom_activities']
                        ]);
                    }
                }
            }
            
            // Save selected activities if any
            if ($request->has('activity_selected') && is_array($request->activity_selected)) {
                $packageActivities = is_string($package->activities) 
                    ? json_decode($package->activities, true) 
                    : $package->activities;
                
                foreach ($request->activity_selected as $activityIndex) {
                    if (isset($packageActivities[$activityIndex])) {
                        $activity = $packageActivities[$activityIndex];
                        
                        // Create activity record
                        $bookingActivity = new PrivateBookingActivity([
                            'private_booking_id' => $privateBooking->id,
                            'activity_name' => $activity['name'] ?? 'Activity '.($activityIndex+1),
                            'activity_price' => $activity['price'] ?? 0,
                            'activity_index' => $activityIndex
                        ]);
                        $bookingActivity->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('private-booking.payment', $privateBooking->id)
                ->with('success', 'Private booking created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create booking: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
        }
    }

    public function showPayment(PrivateBooking $privateBooking)
    {
        // Cancel expired bookings first
        PrivateBooking::cancelExpiredBookings();
        
        if ($privateBooking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
    
        // Check if this specific booking is now expired
        if ($privateBooking->payment_status === 'pending' && Carbon::parse($privateBooking->available_date)->lt(Carbon::today())) {
            // Create a new payment record with cancelled status
            $privateBooking->payments()->create([
                'payment_status' => 'cancelled',
                'amount' => $privateBooking->total_price,
                'notes' => 'Automatically cancelled due to expired date'
            ]);
            
            return redirect()->route('booking.index')
                ->with('warning', 'This booking has been cancelled because the travel date has passed.');
        }
    
        return view('customer.private-booking.payment', compact('privateBooking'));
    }
    
    public function showBookingDetails($id)
    {
        $booking = PrivateBooking::with(['travelPackage', 'participants', 'activities', 'customDays'])
            ->findOrFail($id);
        
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('customer.booking.private-detail', compact('booking'));
    }
    
   
}
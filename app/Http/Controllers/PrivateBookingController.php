<?php

namespace App\Http\Controllers;

use App\Models\PrivateBooking;
use App\Models\PrivateBookingParticipant;
use App\Models\AdditionalActivity;
use App\Models\TravelPackage;
use Illuminate\Http\Request;

class PrivateBookingController extends Controller
{
    public function create(TravelPackage $package)
    {
        $activities = AdditionalActivity::where('is_active', true)->get();
        return view('customer.private-booking.create', compact('package', 'activities'));
    }

    public function store(Request $request, TravelPackage $package)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'available_date' => 'required|date|after:today',
            'custom_itinerary' => 'nullable|string',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.ic_number' => 'required|string|unique:private_booking_participants,ic_number',
            'activities' => 'nullable|array',
            'activities.*' => 'exists:additional_activities,id'
        ]);

        $booking = PrivateBooking::create([
            'user_id' => auth()->id(),
            'travel_package_id' => $package->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'available_date' => $validated['available_date'],
            'custom_itinerary' => $validated['custom_itinerary'],
            'base_price' => $package->price,
            'total_price' => $package->price // Will be updated after adding activities
        ]);

        // Add participants
        foreach ($validated['participants'] as $participant) {
            $booking->participants()->create([
                'name' => $participant['name'],
                'ic_number' => $participant['ic_number']
            ]);
        }

        // Add activities if selected
        if (!empty($validated['activities'])) {
            $additional_price = 0;
            $activities = AdditionalActivity::whereIn('id', $validated['activities'])->get();
            
            foreach ($activities as $activity) {
                $booking->activities()->attach($activity->id, [
                    'price_at_time_of_booking' => $activity->price
                ]);
                $additional_price += $activity->price;
            }

            $booking->update([
                'additional_price' => $additional_price,
                'total_price' => $booking->base_price + $additional_price
            ]);
        }

        return redirect()->route('payment.show', $booking->id)
            ->with('success', 'Private booking created successfully');
    }
}

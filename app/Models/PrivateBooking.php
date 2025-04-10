<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateBooking extends Model
{
    protected $fillable = [
        'user_id',
        'travel_package_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'available_date',
        'base_price',
        'additional_price',
        'total_price',
        'custom_itinerary',
        'payment_status'
    ];

    protected $attributes = [
        'payment_status' => 'pending'
    ];

    public const PAYMENT_STATUS = [
        'pending' => 'pending',
        'paid' => 'paid',
        'cancelled' => 'cancelled'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class);
    }

    /**
     * Get the participants for the private booking.
     */
    public function participants()
    {
        return $this->hasMany(PrivateBookingParticipant::class);
    }

    /**
     * Get the additional activities for the private booking.
     */
    public function additionalActivities()
    {
        return $this->belongsToMany(AdditionalActivity::class, 'private_booking_activities')
            ->withPivot('price_at_time_of_booking')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'travel_package_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'available_date',
        'adults',
        'children',
        'infants',
        'total_price',
        'payment_status',  // Add this line
        'notes',
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
        return $this->belongsTo(TravelPackage::class, 'travel_package_id');
    }

    /**
     * Get the travelers for the booking.
     */
    // Make sure this relationship exists in your Booking model
    public function travelers()
    {
        return $this->hasMany(BookingTraveler::class);
    }
}

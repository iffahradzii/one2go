<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTraveler extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'ic_number',
        'category'
    ];

    /**
     * Get the booking that owns the traveler.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
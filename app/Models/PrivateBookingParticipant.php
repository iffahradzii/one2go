<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateBookingParticipant extends Model
{
    protected $fillable = [
        'private_booking_id',
        'name',
        'ic_number',
        'type'
    ];

    public function privateBooking()
    {
        return $this->belongsTo(PrivateBooking::class);
    }

    public function determineType()
    {
        // Logic to determine type based on IC number
        // This will be implemented based on Malaysian IC number format
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBookingParticipant extends Model
{
    use HasFactory;

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
}

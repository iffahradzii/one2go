<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBookingActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_booking_id',
        'activity_name',
        'activity_price',
        'activity_index'
    ];

    public function privateBooking()
    {
        return $this->belongsTo(PrivateBooking::class);
    }

    public function participants()
    {
        return $this->belongsToMany(
            PrivateBookingParticipant::class,
            'activity_participants',
            'private_booking_activity_id',
            'participant_id'
        );
    }
}
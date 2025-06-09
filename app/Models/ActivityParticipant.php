<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_booking_activity_id',
        'participant_id'
    ];

    public function activity()
    {
        return $this->belongsTo(PrivateBookingActivity::class, 'private_booking_activity_id');
    }

    public function participant()
    {
        return $this->belongsTo(PrivateBookingParticipant::class, 'participant_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBookingCustomDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_booking_id',
        'day_number',
        'custom_activities'
    ];
    protected $casts = [
        'custom_activities' => 'array',
    ];
    
    public function booking()
    {
        return $this->belongsTo(PrivateBooking::class, 'private_booking_id');
    }
}
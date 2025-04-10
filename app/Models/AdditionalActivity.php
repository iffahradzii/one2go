<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalActivity extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function privateBookings()
    {
        return $this->belongsToMany(PrivateBooking::class, 'private_booking_activities')
            ->withPivot('price_at_time_of_booking')
            ->withTimestamps();
    }
}

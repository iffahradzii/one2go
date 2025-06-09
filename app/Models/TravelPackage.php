<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPackage extends Model
{
    use HasFactory;

    protected $table = 'travel_packages';

    protected $fillable = [
        'name',
        'country',
        'price',
        'duration',
        'description',
        'itinerary',
        'include',
        'exclude',
        'itinerary_pdfs',
        'include_pdfs',
        'exclude_pdfs',
        'image',
        'available_dates',
        'activities',
        'is_visible'
    ];

    protected $casts = [
        'itinerary' => 'array',
        'include' => 'array',
        'exclude' => 'array',
        'available_dates' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Add this method to your existing TravelPackage model

    public function additionalActivities()
    {
        return $this->hasMany(AdditionalActivity::class);
    }

    public function privateBookings()
    {
        return $this->hasMany(PrivateBooking::class);
    }
}

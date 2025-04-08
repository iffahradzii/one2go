<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPackage extends Model
{
    use HasFactory;

    protected $table = 'travel_packages';

    protected $fillable = [
        'name', 'country', 'price', 'description', 'image', 
        'itinerary', 'include', 'exclude', 'available_dates', 'itinerary_pdfs', 'include_pdfs', 'exclude_pdfs'
    ];

    protected $casts = [
        'itinerary' => 'array',
        'include' => 'array',
        'exclude' => 'array',
        'available_dates' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'travel_package_id');
    }


    
}

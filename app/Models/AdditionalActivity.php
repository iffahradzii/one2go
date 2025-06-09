<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_package_id',
        'name',
        'description',
        'price',
        'is_active',
    ];

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class);
    }
    
    public function pricingTiers()
    {
        return $this->hasMany(ActivityPricingTier::class);
    }
    
    public function privateBookingActivities()
    {
        return $this->hasMany(PrivateBookingActivity::class);
    }
}

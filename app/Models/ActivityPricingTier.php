<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'additional_activity_id',
        'participant_type',
        'price',
    ];

    public function additionalActivity()
    {
        return $this->belongsTo(AdditionalActivity::class);
    }
}
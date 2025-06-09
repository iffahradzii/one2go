<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'booking_id',
        'private_booking_id',
        'payment_status',
        'amount',
        'payment_method',
        'notes',
    ];
    
    /**
     * Get the booking associated with the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    
    /**
     * Get the private booking associated with the payment.
     */
    public function privateBooking(): BelongsTo
    {
        return $this->belongsTo(PrivateBooking::class);
    }
    
    /**
     * Get the related booking (either regular or private).
     */
    public function getBookingAttribute()
    {
        return $this->booking_id ? $this->booking : $this->privateBooking;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_package_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'available_date',
        'total_price',
        'notes',
        // Remove 'payment_status' from here if it exists
    ];

   
    public const PAYMENT_STATUS = [
        'pending' => 'pending',
        'paid' => 'paid',
        'cancelled' => 'cancelled'
    ];

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the travelers for the booking.
     */
    // Make sure this relationship exists in your Booking model
    public function travelers()
    {
        return $this->hasMany(BookingTraveler::class);
    }

    public static function cancelExpiredBookings()
    {
        // Find bookings with pending payments where either:
        // 1. Booking date is within 14 days of the trip, or
        // 2. Booking was created more than 14 days ago
        $expiredBookings = self::whereHas('payments', function($query) {
                $query->where('payment_status', 'pending');
            })
            ->where(function($query) {
                $query->where('available_date', '<=', now()->addDays(14)->toDateString())
                      ->orWhere('created_at', '<', now()->subDays(14));
            })
            ->get();
        
        // Update payment status for each expired booking
        foreach ($expiredBookings as $booking) {
            // Update existing payment record instead of creating new one
            $booking->latestPayment()->update([
                'payment_status' => 'cancelled',
                'notes' => 'Automatically cancelled due to approaching trip date (within 14 days) or payment deadline exceeded (14 days)'
            ]);
        }
        
        return count($expiredBookings);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the payments for the booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment for the booking.
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    /**
     * Get the payment status attribute.
     */
    public function getPaymentStatusAttribute()
    {
        return $this->latestPayment ? $this->latestPayment->payment_status : 'pending';
    }
}

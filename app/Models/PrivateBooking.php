<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBooking extends Model
{
    use HasFactory;

    protected $table = 'private_bookings'; // Add this line to specify the table name
    
    protected $fillable = [
        'user_id',
        'travel_package_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'available_date',
        'booking_date',
        'base_price',
        'additional_price',
        'total_price',
        // Remove 'payment_status' from here
    ];

    protected $casts = [
        'available_date' => 'date',
        'booking_date' => 'date',
    ];
    public const PAYMENT_STATUS = [
        'pending' => 'pending',
        'paid' => 'paid',
        'cancelled' => 'cancelled'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class);
    }

    public function participants()
    {
        return $this->hasMany(PrivateBookingParticipant::class);
    }

    public function activities()
    {
        return $this->hasMany(PrivateBookingActivity::class);
    }
    
    public function customDays()
    {
        return $this->hasMany(PrivateBookingCustomDay::class);
    }

    public static function cancelExpiredBookings()
    {
        // Find private bookings with pending payments where either:
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
        return $this->hasMany(Review::class, 'private_booking_id');
    }

    /**
     * Get the payments for the private booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'private_booking_id');
    }

    /**
     * Get the latest payment for the private booking.
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'private_booking_id')->latest();
    }

    /**
     * Get the payment status attribute.
     */
    public function getPaymentStatusAttribute()
    {
        return $this->latestPayment ? $this->latestPayment->payment_status : 'pending';
    }
}
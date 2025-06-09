<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'private_booking_id',
        'user_id',
        'rating',
        'review_text',
        'photo_path',
        'has_been_edited',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function privateBooking()
    {
        return $this->belongsTo(PrivateBooking::class);
    }

    public function reply()
    {
        return $this->hasOne(ReviewReply::class);
    }
}
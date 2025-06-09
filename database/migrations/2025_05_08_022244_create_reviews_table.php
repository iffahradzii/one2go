<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('private_booking_id')->nullable()->constrained('private_bookings')->onDelete('cascade');
            $table->integer('rating')->comment('Rating from 1-5');
            $table->text('review_text');
            $table->timestamps();
            
            // Ensure a review is linked to either a booking or a private booking, but not both
            $table->index(['booking_id', 'private_booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
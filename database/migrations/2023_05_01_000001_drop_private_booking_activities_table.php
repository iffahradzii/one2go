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
        Schema::dropIfExists('private_booking_activities');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('private_booking_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_booking_id')->constrained('private_bookings')->onDelete('cascade');
            $table->foreignId('additional_activity_id')->constrained('additional_activities')->onDelete('cascade');
            $table->decimal('price_at_time_of_booking', 10, 2);
            $table->timestamps();
        });
    }
};
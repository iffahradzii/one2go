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
        Schema::create('private_booking_custom_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_booking_id')->constrained('private_bookings')->onDelete('cascade');
            $table->integer('day_number');
            $table->text('original_activities')->nullable();
            $table->text('custom_activities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_booking_custom_days');
    }
};
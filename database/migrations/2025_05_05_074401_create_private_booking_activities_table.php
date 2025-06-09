<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_booking_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_booking_id')->constrained('private_bookings')->onDelete('cascade');
            $table->string('activity_name');
            $table->decimal('activity_price', 10, 2)->default(0);
            $table->integer('activity_index')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_booking_activities');
    }
};
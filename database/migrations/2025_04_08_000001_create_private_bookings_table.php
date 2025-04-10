<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('private_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('travel_package_id')->constrained('travel_packages')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->date('available_date');
            $table->decimal('base_price', 10, 2);
            $table->decimal('additional_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->text('custom_itinerary')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('private_booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_booking_id')->constrained('private_bookings')->onDelete('cascade');
            $table->string('name');
            $table->string('ic_number')->unique();
            $table->enum('type', ['adult', 'child', 'infant']); // Will be automatically determined based on IC
            $table->timestamps();
        });

        Schema::create('additional_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('private_booking_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_booking_id')->constrained('private_bookings')->onDelete('cascade');
            $table->foreignId('additional_activity_id')->constrained('additional_activities')->onDelete('cascade');
            $table->decimal('price_at_time_of_booking', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_booking_activities');
        Schema::dropIfExists('additional_activities');
        Schema::dropIfExists('private_booking_participants');
        Schema::dropIfExists('private_bookings');
    }
};
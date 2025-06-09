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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('travel_package_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->date('available_date');
            $table->date('booking_date');
            $table->decimal('base_price', 10, 2);
            $table->decimal('additional_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('private_booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_booking_id')
                  ->constrained('private_bookings')  // Update the foreign key constraint
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('ic_number');
            $table->enum('type', ['adult', 'child', 'infant']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_booking_participants');
        Schema::dropIfExists('private_bookings');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('travel_package_id')->constrained('travel_packages')->onDelete('cascade'); // Foreign key to travel_packages table
            $table->string('customer_name'); // Snapshot of user's name
            $table->string('customer_email'); // Snapshot of user's email
            $table->string('customer_phone'); // User-provided phone
            $table->date('available_date'); // Selected available date
            $table->unsignedInteger('adults'); // Number of adults
            $table->unsignedInteger('children'); // Number of children
            $table->unsignedInteger('infants'); // Number of infants
            $table->decimal('total_price', 10, 2); // Total price of the booking
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

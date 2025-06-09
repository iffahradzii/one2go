<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('private_booking_custom_days');
        Schema::dropIfExists('private_booking_activities');
        Schema::dropIfExists('private_booking_participants');
        Schema::dropIfExists('private_bookings');
    }

    public function down()
    {
        // If you need to rollback, you'll need to recreate these tables
        // Add the table creation code here if needed
    }
};
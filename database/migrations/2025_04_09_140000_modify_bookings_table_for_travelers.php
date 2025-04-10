<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Make these columns nullable since we're using booking_travelers table
            $table->unsignedInteger('adults')->nullable()->change();
            $table->unsignedInteger('children')->nullable()->change();
            $table->unsignedInteger('infants')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('adults')->nullable(false)->change();
            $table->unsignedInteger('children')->nullable(false)->change();
            $table->unsignedInteger('infants')->nullable(false)->change();
        });
    }
};
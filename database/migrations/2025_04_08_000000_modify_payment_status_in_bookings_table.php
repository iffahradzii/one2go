<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // First drop the existing enum column
            $table->dropColumn('payment_status');
            
            // Then recreate it with the correct values
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
        });
    }
};
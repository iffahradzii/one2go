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
        // First, migrate existing payment status data to the new payments table
        DB::statement("
            INSERT INTO payments (booking_id, payment_status, created_at, updated_at)
            SELECT id, payment_status, created_at, updated_at FROM bookings
            WHERE payment_status IS NOT NULL
        ");
        
        DB::statement("
            INSERT INTO payments (private_booking_id, payment_status, created_at, updated_at)
            SELECT id, payment_status, created_at, updated_at FROM private_bookings
            WHERE payment_status IS NOT NULL
        ");
        
        // Then remove the columns from the original tables
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
        
        Schema::table('private_bookings', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the columns back
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_status')->nullable();
        });
        
        Schema::table('private_bookings', function (Blueprint $table) {
            $table->string('payment_status')->nullable();
        });
        
        // Migrate data back from payments table
        DB::statement("
            UPDATE bookings
            SET payment_status = (
                SELECT payment_status FROM payments
                WHERE payments.booking_id = bookings.id
                LIMIT 1
            )
            WHERE EXISTS (
                SELECT 1 FROM payments
                WHERE payments.booking_id = bookings.id
            )
        ");
        
        DB::statement("
            UPDATE private_bookings
            SET payment_status = (
                SELECT payment_status FROM payments
                WHERE payments.private_booking_id = private_bookings.id
                LIMIT 1
            )
            WHERE EXISTS (
                SELECT 1 FROM payments
                WHERE payments.private_booking_id = private_bookings.id
            )
        ");
    }
};
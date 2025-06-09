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
        Schema::table('booking_travelers', function (Blueprint $table) {
            // Add the missing columns if they don't exist
            if (!Schema::hasColumn('booking_travelers', 'name')) {
                $table->string('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_travelers', function (Blueprint $table) {
            if (Schema::hasColumn('booking_travelers', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
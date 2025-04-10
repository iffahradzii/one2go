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
            // Add missing columns
            if (!Schema::hasColumn('booking_travelers', 'booking_id')) {
                $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('booking_travelers', 'ic_number')) {
                $table->string('ic_number', 12);
            }
            
            if (!Schema::hasColumn('booking_travelers', 'category')) {
                $table->enum('category', ['Adult', 'Child', 'Infant']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_travelers', function (Blueprint $table) {
            // Remove columns if they exist
            $columns = ['booking_id', 'ic_number', 'category'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('booking_travelers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
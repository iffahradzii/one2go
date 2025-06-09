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
        Schema::table('travel_packages', function (Blueprint $table) {
            // Add duration column if it doesn't exist
            if (!Schema::hasColumn('travel_packages', 'duration')) {
                $table->integer('duration')->default(1);
            }
            
            // Add activities column if it doesn't exist
            if (!Schema::hasColumn('travel_packages', 'activities')) {
                $table->json('activities')->nullable();
            }
            
            // Add is_visible column if it doesn't exist
            if (!Schema::hasColumn('travel_packages', 'is_visible')) {
                $table->boolean('is_visible')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('travel_packages', 'duration')) {
                $table->dropColumn('duration');
            }
            
            if (Schema::hasColumn('travel_packages', 'activities')) {
                $table->dropColumn('activities');
            }
            
            if (Schema::hasColumn('travel_packages', 'is_visible')) {
                $table->dropColumn('is_visible');
            }
        });
    }
};

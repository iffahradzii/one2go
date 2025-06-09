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
        if (Schema::hasTable('faqs') && !Schema::hasColumn('faqs', 'type')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->string('type')->default('general')->after('answer');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('faqs') && Schema::hasColumn('faqs', 'type')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
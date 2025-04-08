<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelPackagesTable extends Migration
{
    public function up()
    {
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id(); // Default primary key 'id'
            $table->string('name');
            $table->enum('country', ['Indonesia', 'Thailand', 'Vietnam', 'South Korea']);
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->json('itinerary')->nullable();
            $table->json('include')->nullable();
            $table->json('exclude')->nullable();
            $table->json('itinerary_pdfs')->nullable();
            $table->json('include_pdfs')->nullable();
            $table->json('exclude_pdfs')->nullable();
            $table->string('image')->nullable();
            $table->json('available_dates')->nullable();
            $table->boolean('is_visible')->default(true); 
            $table->timestamps();

            
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_packages');
    }
}

